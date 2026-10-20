@extends('admin.layouts.app')

@section('title', __('System Logs'))

@section('content')
<div class="h-[calc(100vh-8rem)] flex gap-4" x-data="logViewer(@js($files))" x-init="init()">

    {{-- SIDEBAR: FILE LIST --}}
    <div class="w-72 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col flex-shrink-0 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">{{ __('Log Files') }}</h3>
            <button type="button" @click="loadFiles()" class="text-gray-400 hover:text-gray-600 transition" title="{{ __('Refresh Files') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1">
            <template x-if="files.length === 0">
                <div class="p-4 text-center text-xs text-gray-400">{{ __('No log files found.') }}</div>
            </template>
            
            <template x-for="file in files" :key="file.name">
                <button @click="loadLogs(file.name)"
                        class="w-full text-left px-3 py-2.5 rounded-lg transition flex flex-col gap-1"
                        :class="activeFile === file.name ? 'bg-indigo-50 border border-indigo-100' : 'hover:bg-gray-50 border border-transparent'">
                    <div class="flex items-center justify-between w-full">
                        <span class="text-xs font-bold truncate" :class="activeFile === file.name ? 'text-indigo-700' : 'text-gray-900'" x-text="file.name"></span>
                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-gray-100 text-gray-600" x-text="file.size_human"></span>
                    </div>
                    <div class="text-[10px] text-gray-500" x-text="file.modified"></div>
                </button>
            </template>
        </div>
    </div>

    {{-- MAIN CONTENT: LOG VIEWER --}}
    <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col min-w-0 overflow-hidden">
        
        {{-- TOOLBAR --}}
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3 flex-1 min-w-[200px]">
                <div class="relative flex-1 max-w-sm">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                    <input type="search" x-model.debounce.150ms="search" placeholder="{{ __('Search logs...') }}"
                           class="w-full h-9 pl-9 pr-3 rounded-lg border border-gray-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <select x-model="levelFilter" class="h-9 rounded-lg border-gray-300 text-sm focus:ring-indigo-500">
                    <option value="">{{ __('All Levels') }}</option>
                    <option value="EMERGENCY">EMERGENCY</option>
                    <option value="ALERT">ALERT</option>
                    <option value="CRITICAL">CRITICAL</option>
                    <option value="ERROR">ERROR</option>
                    <option value="WARNING">WARNING</option>
                    <option value="NOTICE">NOTICE</option>
                    <option value="INFO">INFO</option>
                    <option value="DEBUG">DEBUG</option>
                </select>

                <select x-model="limit" @change="loadLogs(activeFile)" class="h-9 rounded-lg border-gray-300 text-sm focus:ring-indigo-500">
                    <option value="100">100 {{ __('lines') }}</option>
                    <option value="500">500 {{ __('lines') }}</option>
                    <option value="1000">1000 {{ __('lines') }}</option>
                    <option value="2000">2000 {{ __('lines') }}</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button @click="loadLogs(activeFile)" :disabled="loading" class="h-9 px-3 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition disabled:opacity-50 flex items-center gap-2">
                    <svg :class="loading ? 'animate-spin' : ''" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ __('Refresh') }}
                </button>
                
                <a :href="'/admin/tools/logs/download/' + activeFile" x-show="activeFile" class="h-9 px-3 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    {{ __('Download') }}
                </a>

                <button @click="clearLog()" x-show="activeFile" class="h-9 px-3 rounded-lg bg-red-50 text-red-600 text-sm font-medium hover:bg-red-100 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                    {{ __('Clear') }}
                </button>
            </div>
        </div>

        {{-- STATS BAR --}}
        <div class="px-4 py-2 border-b border-gray-100 flex items-center gap-4 text-xs font-medium bg-white">
            <span class="text-gray-500">{{ __('Loaded') }}: <span class="text-gray-900" x-text="entries.length"></span></span>
            <span class="text-gray-500">{{ __('Filtered') }}: <span class="text-indigo-600" x-text="filteredEntries.length"></span></span>
            
            <div class="flex items-center gap-1 ml-auto">
                <template x-if="counts.ERROR > 0">
                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-700" x-text="counts.ERROR + ' ERR'"></span>
                </template>
                <template x-if="counts.WARNING > 0">
                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-700" x-text="counts.WARNING + ' WARN'"></span>
                </template>
                <template x-if="counts.INFO > 0">
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700" x-text="counts.INFO + ' INFO'"></span>
                </template>
            </div>
        </div>

        {{-- LOG ENTRIES --}}
        <div class="flex-1 overflow-y-auto bg-gray-50/50 p-4 space-y-2 custom-scrollbar relative">
            
            <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 flex items-center justify-center">
                <div class="flex flex-col items-center gap-2">
                    <svg class="w-8 h-8 text-indigo-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    <span class="text-sm font-medium text-gray-600">{{ __('Reading logs...') }}</span>
                </div>
            </div>

            <template x-if="!loading && filteredEntries.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-sm font-medium">{{ __('No log entries match your filters.') }}</p>
                </div>
            </template>

            <template x-for="(entry, index) in filteredEntries" :key="index">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden group">
                    <div class="px-4 py-3 flex items-start gap-3">
                        
                        {{-- Level Badge --}}
                        <span class="flex-shrink-0 mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border"
                              :class="{
                                 'bg-red-50 text-red-700 border-red-200': entry.level === 'EMERGENCY' || entry.level === 'ALERT' || entry.level === 'CRITICAL' || entry.level === 'ERROR',
                                 'bg-amber-50 text-amber-700 border-amber-200': entry.level === 'WARNING',
                                 'bg-blue-50 text-blue-700 border-blue-200': entry.level === 'NOTICE' || entry.level === 'INFO',
                                 'bg-gray-100 text-gray-600 border-gray-200': entry.level === 'DEBUG'
                              }"
                              x-text="entry.level"></span>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1 mb-1">
                                <span class="text-xs font-semibold text-gray-500 font-mono" x-text="entry.timestamp"></span>
                                <span class="text-[10px] text-gray-400" x-text="entry.environment"></span>
                            </div>
                            
                            <p class="text-sm text-gray-900 break-words font-mono leading-relaxed" x-text="entry.message"></p>

                            {{-- Context --}}
                            <template x-if="entry.context">
                                <div class="mt-2 rounded bg-gray-900 p-3 overflow-x-auto relative group/ctx">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">{{ __('Context') }}</div>
                                    <pre class="text-xs text-gray-200 font-mono" x-text="formatJson(entry.context)"></pre>
                                    <button @click="copyText(entry.context)" class="absolute top-2 right-2 p-1 rounded bg-white/10 text-white opacity-0 group-hover/ctx:opacity-100 transition" title="{{ __('Copy Context') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Expand Stack Trace Button --}}
                        <button x-show="entry.stack" @click="expanded[index] = !expanded[index]"
                                class="flex-shrink-0 text-gray-400 hover:text-indigo-600 transition"
                                :title="expanded[index] ? '{{ __('Hide Stack Trace') }}' : '{{ __('Show Stack Trace') }}'">
                            <svg class="w-5 h-5 transition-transform duration-200" :class="expanded[index] ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>

                    {{-- Stack Trace --}}
                    <div x-show="expanded[index]" x-collapse x-cloak class="border-t border-gray-100 bg-gray-50 p-4 overflow-x-auto">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">{{ __('Stack Trace') }}</div>
                        <pre class="text-xs text-gray-700 font-mono leading-relaxed whitespace-pre" x-text="entry.stack"></pre>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- TOAST --}}
    <div x-show="toast.show" x-cloak x-transition aria-live="polite"
         class="fixed bottom-6 right-6 px-4 py-3 rounded-xl shadow-lg text-white text-sm font-bold z-[60]"
         :class="toast.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'" x-text="toast.message"></div>
</div>

<script>
function logViewer(initialFiles) {
    return {
        files: initialFiles,
        activeFile: null,
        entries: [],
        counts: {},
        limit: 500,
        loading: false,
        
        search: '',
        levelFilter: '',
        expanded: {},

        toast: { show: false, message: '', type: 'success' },
        _toastTimer: null,

        init() {
            if (this.files.length > 0) {
                this.loadLogs(this.files[0].name);
            }
        },

        get filteredEntries() {
            const q = this.search.trim().toLowerCase();
            return this.entries.filter(e => {
                if (this.levelFilter && e.level !== this.levelFilter) return false;
                if (!q) return true;
                return (e.message + ' ' + e.stack + ' ' + e.context).toLowerCase().includes(q);
            });
        },

        async loadFiles() {
            try {
                const res = await fetch(window.location.href, { headers: { 'Accept': 'application/json' } });
                // Since index is a view, we just refresh the page for simplicity, 
                // or we could add an API endpoint. For now, window reload is easiest if files change.
                window.location.reload();
            } catch (e) {}
        },

        async loadLogs(fileName) {
            if (!fileName) return;
            this.activeFile = fileName;
            this.loading = true;
            this.entries = [];
            this.expanded = {};

            try {
                const res = await fetch('{{ route('admin.tools.logs.fetch') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ file: fileName, limit: this.limit }),
                });

                const data = await res.json();
                
                if (data.success) {
                    this.entries = data.entries;
                    this.counts = data.counts;
                } else {
                    this.showToast(data.message || '{{ __('Failed to load logs.') }}', 'error');
                }
            } catch (e) {
                this.showToast('{{ __('Network error.') }}', 'error');
            } finally {
                this.loading = false;
            }
        },

        async clearLog() {
            if (!this.activeFile) return;
            if (!confirm('{{ __('Are you sure you want to clear this log file? This action cannot be undone.') }}')) return;

            try {
                const res = await fetch('{{ route('admin.tools.logs.clear') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ file: this.activeFile }),
                });

                const data = await res.json();
                
                if (data.success) {
                    this.showToast(data.message || '{{ __('Log cleared.') }}', 'success');
                    this.entries = [];
                    this.counts = {};
                } else {
                    this.showToast(data.message || '{{ __('Failed to clear log.') }}', 'error');
                }
            } catch (e) {
                this.showToast('{{ __('Network error.') }}', 'error');
            }
        },

        formatJson(str) {
            try {
                const obj = JSON.parse(str);
                return JSON.stringify(obj, null, 2);
            } catch (e) {
                return str;
            }
        },

        async copyText(text) {
            try {
                await navigator.clipboard.writeText(text);
                this.showToast('{{ __('Copied to clipboard.') }}', 'success');
            } catch (e) {
                this.showToast('{{ __('Could not copy.') }}', 'error');
            }
        },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => { this.toast.show = false; }, 3500);
        },
    };
}
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
@endsection
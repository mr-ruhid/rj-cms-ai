@extends('admin.layouts.app')

@section('title', __('Theme Editor'))

@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.3/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.3/ext-language_tools.js"></script>

<style>
    #theme-ace { position:absolute; top:0; right:0; bottom:0; left:0; font-size:14px; border-radius:0 0 0.75rem 0.75rem; }
</style>

<div class="h-[calc(100vh-8rem)] flex gap-4" x-data="themeEditor()" x-init="init()">

    <div class="w-72 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col flex-shrink-0">

        <div class="p-4 border-b border-gray-100 space-y-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">{{ __('Theme') }}</label>
                <select x-model="activeSlug" @change="switchTheme()" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <template x-for="t in themes" :key="t.slug">
                        <option :value="t.slug" x-text="t.name + (t.status === 'active' ? ' ★' : '')"></option>
                    </template>
                </select>
            </div>

            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                <button type="button" @click="tab = 'files'" :class="tab === 'files' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-xs font-medium transition">{{ __('Files') }}</button>
                <button type="button" @click="tab = 'backups'; loadBackups()" :class="tab === 'backups' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-xs font-medium transition">{{ __('Backups') }}</button>
                <button type="button" @click="tab = 'info'" :class="tab === 'info' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'" class="flex-1 px-2 py-1.5 rounded-md text-xs font-medium transition">{{ __('Info') }}</button>
            </div>

            <input type="text" x-model="search" x-show="tab === 'files'" placeholder="{{ __('Filter files...') }}" class="w-full px-3 py-1.5 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs">
        </div>

        <div x-show="tab === 'files'" class="flex-1 overflow-y-auto p-2">
            <template x-for="file in filteredFiles" :key="file.path">
                <button @click="openFile(file)"
                        :disabled="file.readonly"
                        class="w-full text-left px-2.5 py-1.5 rounded-md text-xs transition flex items-center gap-2 truncate"
                        :class="currentFile === file.path
                            ? 'bg-rj-purple/10 text-rj-purple font-medium'
                            : (file.readonly ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50')">
                    <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-text="file.path" class="truncate"></span>
                </button>
            </template>
            <div x-show="filteredFiles.length === 0" class="p-4 text-center text-xs text-gray-400">
                {{ __('No files.') }}
            </div>
        </div>

        <div x-show="tab === 'backups'" class="flex-1 overflow-y-auto p-2 space-y-2">
            <div x-show="backups.length === 0" class="p-4 text-center text-xs text-gray-400">
                {{ __('No backups yet.') }}
            </div>
            <template x-for="b in backups" :key="b.timestamp">
                <div class="border border-gray-200 rounded-lg p-2.5 bg-gray-50">
                    <div class="text-[11px] font-mono text-gray-700 truncate" x-text="b.file"></div>
                    <div class="text-[10px] text-gray-500 mt-0.5" x-text="b.created_at"></div>
                    <div class="flex items-center gap-1.5 mt-2">
                        <button @click="restoreBackup(b)" :disabled="restoring === b.timestamp"
                                class="flex-1 px-2 py-1 rounded bg-gradient-to-r from-rj-purple to-rj-pink text-white text-[10px] font-medium hover:opacity-90 disabled:opacity-50">
                            <span x-text="restoring === b.timestamp ? '{{ __('Restoring...') }}' : '{{ __('Restore') }}'"></span>
                        </button>
                        <button @click="deleteBackup(b)" :disabled="deleting === b.timestamp"
                                class="px-2 py-1 rounded bg-white border border-red-200 text-red-600 text-[10px] font-medium hover:bg-red-50 disabled:opacity-50">
                            {{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="tab === 'info'" class="flex-1 overflow-y-auto p-3 space-y-3 text-xs">
            <div>
                <div class="text-[10px] uppercase text-gray-400 font-semibold mb-1">{{ __('Theme') }}</div>
                <div class="font-medium text-gray-900" x-text="info.name"></div>
                <div class="text-gray-500 mt-0.5">v<span x-text="info.version"></span> · <span x-text="info.author || '—'"></span></div>
            </div>
            <div>
                <div class="text-[10px] uppercase text-gray-400 font-semibold mb-1">{{ __('Status') }}</div>
                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold uppercase"
                      :class="info.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                      x-text="info.is_active ? '{{ __('Active') }}' : '{{ __('Inactive') }}'"></span>
            </div>
            <div>
                <div class="text-[10px] uppercase text-gray-400 font-semibold mb-1">{{ __('Location') }}</div>
                <div class="font-mono text-[10px] text-gray-600 break-all" x-text="info.path"></div>
                <div class="mt-1" :class="info.path_exists ? 'text-green-600' : 'text-red-600'">
                    <span x-text="info.path_exists ? '✓ ' + '{{ __('Directory exists') }}' : '✗ ' + '{{ __('Directory missing') }}'"></span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-gray-50 rounded p-2">
                    <div class="text-[10px] uppercase text-gray-400 font-semibold">{{ __('Files') }}</div>
                    <div class="text-sm font-bold text-gray-900" x-text="info.file_count"></div>
                </div>
                <div class="bg-gray-50 rounded p-2">
                    <div class="text-[10px] uppercase text-gray-400 font-semibold">{{ __('Size') }}</div>
                    <div class="text-sm font-bold text-gray-900" x-text="info.total_size_human"></div>
                </div>
            </div>
            <div>
                <div class="text-[10px] uppercase text-gray-400 font-semibold mb-1">{{ __('Customizations') }}</div>
                <div class="font-medium" x-text="info.customizations_count"></div>
            </div>
            <div>
                <div class="text-[10px] uppercase text-gray-400 font-semibold mb-1">{{ __('Quick Links') }}</div>
                <div class="space-y-1">
                    <a :href="'/admin/themes/' + info.slug + '/customizer'" target="_blank" class="block text-rj-purple hover:underline">→ {{ __('Customize') }}</a>
                    <a href="{{ route('admin.themes.index') }}" class="block text-rj-purple hover:underline">→ {{ __('All Themes') }}</a>
                    <a href="{{ route('home') }}" target="_blank" class="block text-rj-purple hover:underline">→ {{ __('View Site') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 flex gap-4 min-w-0">

        <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col min-w-0 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="text-sm font-mono text-gray-600 truncate" x-text="currentFile || '{{ __('Select a file') }}'"></div>
                    <span x-show="isDirty" x-cloak class="w-2 h-2 rounded-full bg-amber-500" :title="'{{ __('Unsaved changes') }}'"></span>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="askAi()" :disabled="!currentFile || aiLoading || isBinary"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-medium hover:opacity-90 transition disabled:opacity-50">
                        <svg x-show="!aiLoading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <svg x-show="aiLoading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                        <span x-text="aiLoading ? '{{ __('Reviewing...') }}' : '{{ __('AI Review') }}'"></span>
                    </button>

                    <button @click="saveFile()" :disabled="!currentFile || saving || !isDirty || isBinary"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-xs font-medium hover:opacity-90 transition disabled:opacity-50">
                        <svg x-show="!saving" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <svg x-show="saving" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>

            <div class="flex-1 relative">
                <div id="theme-ace"></div>
                <div x-show="isBinary && currentFile" x-cloak class="absolute inset-0 bg-gray-50 flex flex-col items-center justify-center text-center p-8">
                    <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-gray-500">{{ __('Binary file — cannot be edited in browser.') }}</p>
                    <p class="text-xs text-gray-400 mt-1" x-text="currentFile"></p>
                </div>
            </div>
        </div>

        <template x-if="aiReview">
            <div class="w-80 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col flex-shrink-0 overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('AI Review') }}</h3>
                    <button @click="aiReview = null" class="text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    <div class="rounded-lg p-4 text-center border-2"
                         :class="aiReview.status === 'good' ? 'bg-green-50 border-green-200' : (aiReview.status === 'warning' ? 'bg-amber-50 border-amber-200' : 'bg-red-50 border-red-200')">
                        <div class="text-3xl font-bold"
                             :class="aiReview.status === 'good' ? 'text-green-600' : (aiReview.status === 'warning' ? 'text-amber-600' : 'text-red-600')"
                             x-text="aiReview.score"></div>
                        <div class="text-xs uppercase font-semibold mt-1"
                             :class="aiReview.status === 'good' ? 'text-green-700' : (aiReview.status === 'warning' ? 'text-amber-700' : 'text-red-700')"
                             x-text="aiReview.status"></div>
                    </div>
                    <div class="text-sm text-gray-700 leading-relaxed" x-text="aiReview.summary"></div>
                    <template x-if="aiReview.issues && aiReview.issues.length > 0">
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-gray-500 uppercase">{{ __('Issues') }}</h4>
                            <template x-for="(issue, i) in aiReview.issues" :key="i">
                                <div class="text-xs p-3 rounded-lg border"
                                     :class="issue.severity === 'error' ? 'bg-red-50 border-red-100 text-red-800' : (issue.severity === 'warning' ? 'bg-amber-50 border-amber-100 text-amber-800' : 'bg-blue-50 border-blue-100 text-blue-800')">
                                    <div class="font-semibold mb-1" x-text="issue.severity.toUpperCase() + (issue.line && issue.line !== 'global' ? ' · Line ' + issue.line : '')"></div>
                                    <div x-text="issue.message"></div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function themeEditor() {
    let aceEditor = null;
    let isUpdating = false;

    return {
        themes: @json($themes),
        activeSlug: @json($theme->slug),
        files: @json($files),
        info: @json($info),
        backups: [],
        currentFile: null,
        isDirty: false,
        isBinary: false,
        saving: false,
        restoring: null,
        deleting: null,
        search: '',
        tab: 'files',

        aiLoading: false,
        aiReview: null,

        init() {
            if (typeof ace === 'undefined') {
                alert('{{ __('Ace editor failed to load.') }}');
                return;
            }

            aceEditor = ace.edit("theme-ace");
            aceEditor.setTheme("ace/theme/monokai");
            aceEditor.session.setMode("ace/mode/php");
            aceEditor.setOptions({
                fontSize: "14px",
                showPrintMargin: false,
                useWorker: false,
                wrap: true,
            });

            aceEditor.session.on('change', () => {
                if (isUpdating) return;
                this.isDirty = true;
            });

            aceEditor.commands.addCommand({
                name: 'saveFile',
                bindKey: { win: 'Ctrl-S', mac: 'Cmd-S' },
                exec: () => this.saveFile(),
            });
        },

        get filteredFiles() {
            if (!this.search) return this.files;
            const q = this.search.toLowerCase();
            return this.files.filter(f => f.path.toLowerCase().includes(q));
        },

        async switchTheme() {
            if (this.isDirty && !confirm('{{ __('Unsaved changes will be lost. Continue?') }}')) {
                return;
            }
            window.location.href = '{{ route('admin.appearance.editor') }}?theme=' + this.activeSlug;
        },

        detectMode(name) {
            if (name.endsWith('.blade.php')) return 'ace/mode/php';
            if (name.endsWith('.php')) return 'ace/mode/php';
            if (name.endsWith('.js')) return 'ace/mode/javascript';
            if (name.endsWith('.css')) return 'ace/mode/css';
            if (name.endsWith('.json')) return 'ace/mode/json';
            if (name.endsWith('.html')) return 'ace/mode/html';
            if (name.endsWith('.svg')) return 'ace/mode/xml';
            if (name.endsWith('.xml')) return 'ace/mode/xml';
            if (name.endsWith('.md')) return 'ace/mode/markdown';
            return 'ace/mode/text';
        },

        async openFile(file) {
            if (file.readonly) {
                this.isBinary = true;
                this.currentFile = file.path;
                isUpdating = true;
                if (aceEditor) aceEditor.setValue('');
                isUpdating = false;
                this.isDirty = false;
                return;
            }

            if (this.isDirty && !confirm('{{ __('Discard unsaved changes?') }}')) {
                return;
            }

            try {
                const res = await fetch('{{ url('/admin/appearance/editor') }}/' + this.activeSlug + '/read?path=' + encodeURIComponent(file.path), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.error) { alert(data.error); return; }

                this.currentFile = file.path;
                this.isBinary = data.binary || false;
                this.aiReview = null;

                if (!aceEditor || this.isBinary) return;

                isUpdating = true;
                aceEditor.setValue(data.content || '', -1);
                aceEditor.session.setMode(this.detectMode(file.name));
                aceEditor.clearSelection();
                isUpdating = false;

                this.isDirty = false;
            } catch (e) {
                alert('{{ __('Failed to read file.') }}');
            }
        },

        async saveFile() {
            if (!this.currentFile || !this.isDirty || !aceEditor || this.isBinary) return;
            this.saving = true;

            try {
                const res = await fetch('{{ url('/admin/appearance/editor') }}/' + this.activeSlug + '/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ path: this.currentFile, content: aceEditor.getValue() })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    this.isDirty = false;
                    this.toast(data.message || '{{ __('Saved.') }}', 'success');
                } else {
                    alert(data.error || '{{ __('Save failed.') }}');
                }
            } catch (e) {
                alert('{{ __('Network error.') }}');
            } finally {
                this.saving = false;
            }
        },

        async loadBackups() {
            try {
                const res = await fetch('{{ url('/admin/appearance/editor') }}/' + this.activeSlug + '/backups', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.backups = data.backups || [];
            } catch (e) {
                this.backups = [];
            }
        },

        async restoreBackup(b) {
            if (!confirm('{{ __('Restore this backup? Current file will be saved as a new backup first.') }}')) return;
            this.restoring = b.timestamp;
            try {
                const res = await fetch('{{ url('/admin/appearance/editor') }}/' + this.activeSlug + '/backups/' + b.timestamp + '/restore', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.toast(data.message || '{{ __('Restored.') }}', 'success');
                    if (this.currentFile === b.file) {
                        await this.loadFiles();
                        const file = this.files.find(f => f.path === b.file);
                        if (file) await this.openFile(file);
                    }
                } else {
                    alert(data.error || '{{ __('Restore failed.') }}');
                }
            } catch (e) {
                alert('{{ __('Network error.') }}');
            } finally {
                this.restoring = null;
            }
        },

        async deleteBackup(b) {
            if (!confirm('{{ __('Delete this backup permanently?') }}')) return;
            this.deleting = b.timestamp;
            try {
                const res = await fetch('{{ url('/admin/appearance/editor') }}/' + this.activeSlug + '/backups/' + b.timestamp, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.backups = this.backups.filter(x => x.timestamp !== b.timestamp);
                } else {
                    alert(data.error || '{{ __('Delete failed.') }}');
                }
            } catch (e) {
                alert('{{ __('Network error.') }}');
            } finally {
                this.deleting = null;
            }
        },

        async loadFiles() {
            try {
                const res = await fetch('{{ url('/admin/appearance/editor') }}/' + this.activeSlug + '/files', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.files = data.files || [];
            } catch (e) {}
        },

        async askAi() {
            if (!this.currentFile || !aceEditor || this.isBinary) return;
            this.aiLoading = true;
            this.aiReview = null;
            try {
                const res = await fetch('{{ route('admin.plugins.editor.review') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ filename: this.currentFile, code: aceEditor.getValue() })
                });
                const data = await res.json();
                if (data.success && data.data) {
                    this.aiReview = data.data;
                } else {
                    alert(data.error || '{{ __('AI analysis failed.') }}');
                }
            } catch (e) {
                alert('{{ __('AI error.') }}');
            } finally {
                this.aiLoading = false;
            }
        },

        toast(message, type) {
            const el = document.createElement('div');
            el.className = 'fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium z-50';
            el.style.background = type === 'error' ? '#dc2626' : '#059669';
            el.textContent = message;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 3500);
        },
    };
}
</script>
@endsection
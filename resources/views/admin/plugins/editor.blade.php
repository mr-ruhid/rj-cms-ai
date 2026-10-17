@extends('admin.layouts.app')

@section('title', __('Plugin Editor'))

@section('content')
<div class="h-[calc(100vh-8rem)] flex gap-4" x-data="pluginEditor()" x-init="init()">

    {{-- SIDEBAR (File Tree) --}}
    <div class="w-64 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col flex-shrink-0">
        <div class="p-4 border-b border-gray-100">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">{{ __('Active Plugin') }}</label>
            <select x-model="activePlugin" @change="loadFiles()" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                <template x-for="p in plugins" :key="p.slug">
                    <option :value="p.slug" x-text="p.name"></option>
                </template>
            </select>
        </div>
        
        <div class="flex-1 overflow-y-auto p-2">
            <template x-for="file in files" :key="file.path">
                <button @click="openFile(file)" 
                        class="w-full text-left px-3 py-2 rounded-lg text-sm transition flex items-center gap-2 truncate"
                        :class="currentFile === file.path ? 'bg-rj-purple/10 text-rj-purple font-medium' : 'text-gray-700 hover:bg-gray-50'">
                    <svg class="w-4 h-4 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-text="file.name" class="truncate"></span>
                </button>
            </template>
        </div>
    </div>

    {{-- MAIN EDITOR --}}
    <div class="flex-1 flex gap-4 min-w-0">
        <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col min-w-0 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                <div class="text-sm font-mono text-gray-600" x-text="currentFile || '{{ __('Select a file') }}'"></div>
                
                <div class="flex items-center gap-2">
                    <button @click="askAi()" :disabled="!currentFile || aiLoading"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-medium hover:opacity-90 transition disabled:opacity-50">
                        <svg x-show="!aiLoading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <svg x-show="aiLoading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                        <span x-text="aiLoading ? '{{ __('Reviewing...') }}' : '{{ __('AI Code Review') }}'"></span>
                    </button>

                    <button @click="saveFile()" :disabled="!currentFile || saving || !isDirty"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-xs font-medium hover:opacity-90 transition disabled:opacity-50">
                        <svg x-show="!saving" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <svg x-show="saving" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>

            <div class="flex-1 relative" wire:ignore>
                <textarea id="code-editor" x-model="code" @input="isDirty = true"></textarea>
            </div>
        </div>

        {{-- AI REVIEW PANEL --}}
        <div x-show="aiReview" x-cloak class="w-80 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col flex-shrink-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('AI Review') }}</h3>
                <button @click="aiReview = null" class="text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                {{-- Score Card --}}
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
                        <h4 class="text-xs font-bold text-gray-500 uppercase">{{ __('Issues found') }}</h4>
                        <template x-for="(issue, i) in aiReview.issues" :key="i">
                            <div class="text-xs p-3 rounded-lg border"
                                 :class="issue.severity === 'error' ? 'bg-red-50 border-red-100 text-red-800' : (issue.severity === 'warning' ? 'bg-amber-50 border-amber-100 text-amber-800' : 'bg-blue-50 border-blue-100 text-blue-800')">
                                <div class="font-semibold mb-1 flex items-center gap-2">
                                    <span x-text="issue.severity.toUpperCase()"></span>
                                    <span class="opacity-60" x-show="issue.line !== 'global'">Line <span x-text="issue.line"></span></span>
                                </div>
                                <div x-text="issue.message"></div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

{{-- CodeMirror CDN --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/material-darker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/clike/clike.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/php/php.min.js"></script>

<style>
    .CodeMirror { height: 100%; font-size: 13px; font-family: 'Fira Code', monospace; }
</style>

<script>
function pluginEditor() {
    return {
        plugins: @json($plugins),
        activePlugin: '{{ $plugins->first()?->slug }}',
        files: [],
        currentFile: null,
        code: '',
        isDirty: false,
        saving: false,
        
        aiLoading: false,
        aiReview: null,
        editor: null,

        init() {
            // CodeMirror init
            this.editor = CodeMirror.fromTextArea(document.getElementById('code-editor'), {
                lineNumbers: true,
                mode: 'application/x-httpd-php',
                theme: 'material-darker',
                indentUnit: 4,
                tabSize: 4,
                lineWrapping: true,
            });

            // Sync CodeMirror changes to Alpine state
            this.editor.on('change', () => {
                this.code = this.editor.getValue();
                this.isDirty = true;
            });

            if (this.activePlugin) this.loadFiles();
        },

        async loadFiles() {
            this.files = [];
            this.currentFile = null;
            this.code = '';
            this.aiReview = null;
            
            if (!this.activePlugin) return;

            try {
                const res = await fetch(`/admin/plugins/editor/${this.activePlugin}/files`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.files = data.files || [];
            } catch (e) {
                alert('Failed to load files');
            }
        },

        async openFile(file) {
            if (this.isDirty && !confirm('You have unsaved changes. Continue?')) return;
            
            try {
                const res = await fetch(`/admin/plugins/editor/${this.activePlugin}/read?path=${encodeURIComponent(file.path)}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                
                if (data.error) { alert(data.error); return; }

                this.currentFile = file.path;
                this.code = data.content || '';
                this.isDirty = false;
                this.aiReview = null;
                
                this.editor.setValue(this.code);
                
                // Set mode based on extension
                let mode = 'text/plain';
                if (file.name.endsWith('.php') || file.name.endsWith('.blade.php')) mode = 'application/x-httpd-php';
                else if (file.name.endsWith('.js')) mode = 'javascript';
                else if (file.name.endsWith('.css')) mode = 'css';
                else if (file.name.endsWith('.json')) mode = 'application/json';
                else if (file.name.endsWith('.html') || file.name.endsWith('.svg')) mode = 'htmlmixed';
                
                this.editor.setOption('mode', mode);
                setTimeout(() => this.editor.refresh(), 100);

            } catch (e) {
                alert('Failed to read file');
            }
        },

        async saveFile() {
            if (!this.currentFile || !this.isDirty) return;
            this.saving = true;

            try {
                const res = await fetch(`/admin/plugins/editor/${this.activePlugin}/save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ path: this.currentFile, content: this.code })
                });
                
                const data = await res.json();
                if (res.ok && data.success) {
                    this.isDirty = false;
                } else {
                    alert(data.error || 'Failed to save');
                }
            } catch (e) {
                alert('Network error');
            } finally {
                this.saving = false;
            }
        },

        async askAi() {
            if (!this.currentFile) return;
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
                    body: JSON.stringify({ filename: this.currentFile, code: this.code })
                });
                
                const data = await res.json();
                
                if (data.success && data.data) {
                    this.aiReview = data.data;
                } else {
                    alert(data.error || 'Review failed');
                }
            } catch (e) {
                alert('AI request failed');
            } finally {
                this.aiLoading = false;
            }
        }
    };
}
</script>
@endsection
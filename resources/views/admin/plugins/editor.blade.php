@extends('admin.layouts.app')

@section('title', __('Plugin Editor'))

@section('content')

{{-- Ace Editor CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.3/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.3/ext-language_tools.js"></script>

<style>
    #ace-editor {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        font-size: 14px;
        border-radius: 0 0 0.75rem 0.75rem;
    }
</style>

<div class="h-[calc(100vh-8rem)] flex gap-4" x-data="pluginEditor()" x-init="init()">

    {{-- SIDEBAR --}}
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

            {{-- Ace Editor Container --}}
            <div class="flex-1 relative" wire:ignore>
                <div id="ace-editor"></div>
            </div>
        </div>

        {{-- AI REVIEW PANEL --}}
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
        </template>
    </div>
</div>

<script>
function pluginEditor() {
    let aceEditor = null;
    let isUpdating = false; // Fayl keçidində "save" xətasının qarşısını alır

    return {
        plugins: @json($plugins),
        activePlugin: '{{ $plugins->first()?->slug }}',
        files: [],
        currentFile: null,
        isDirty: false,
        saving: false,

        aiLoading: false,
        aiReview: null,

        init() {
            if (typeof ace === 'undefined') {
                alert('Ace Editor yüklənmədi. İnternet bağlantısını yoxlayın.');
                return;
            }

            // Ace Editor-u işə salırıq
            aceEditor = ace.edit("ace-editor");
            aceEditor.setTheme("ace/theme/monokai");
            aceEditor.session.setMode("ace/mode/php");
            aceEditor.setOptions({
                fontSize: "14px",
                showPrintMargin: false,
                useWorker: false,
                wrap: true
            });

            // Dəyişiklik olduqda isDirty-ni true edirik (əgər proqram tərəfindən yazılmırsa)
            aceEditor.session.on('change', () => {
                if (isUpdating) return;
                this.isDirty = true;
            });

            if (this.activePlugin) this.loadFiles();
        },

        detectMode(name) {
            if (name.endsWith('.php')) return 'ace/mode/php';
            if (name.endsWith('.js')) return 'ace/mode/javascript';
            if (name.endsWith('.css')) return 'ace/mode/css';
            if (name.endsWith('.json')) return 'ace/mode/json';
            if (name.endsWith('.html')) return 'ace/mode/html';
            if (name.endsWith('.svg')) return 'ace/mode/xml';
            if (name.endsWith('.md')) return 'ace/mode/markdown';
            return 'ace/mode/text';
        },

        async loadFiles() {
            this.files = [];
            this.currentFile = null;
            this.isDirty = false;
            this.aiReview = null;
            
            isUpdating = true;
            if (aceEditor) aceEditor.setValue('');
            isUpdating = false;

            if (!this.activePlugin) return;

            try {
                const res = await fetch(`/admin/plugins/editor/${this.activePlugin}/files`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.files = data.files || [];
            } catch (e) {
                alert('Fayllar yüklənmədi: ' + e.message);
            }
        },

        async openFile(file) {
            if (this.isDirty && !confirm('Yadda saxlanmamış dəyişikliklər var. Davam edilsin?')) return;

            try {
                const res = await fetch(`/admin/plugins/editor/${this.activePlugin}/read?path=${encodeURIComponent(file.path)}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.error) { alert(data.error); return; }

                this.currentFile = file.path;
                this.aiReview = null;

                if (!aceEditor) return;

                // Kodu Ace Editor-a yazırıq (isUpdating ilə xətanın qarşısını alırıq)
                isUpdating = true;
                aceEditor.setValue(data.content || '', -1); // -1 kursoru ən başa atır
                aceEditor.session.setMode(this.detectMode(file.name));
                aceEditor.clearSelection();
                isUpdating = false;

                this.isDirty = false;

            } catch (e) {
                alert('Fayl oxunmadı: ' + e.message);
            }
        },

        async saveFile() {
            if (!this.currentFile || !this.isDirty || !aceEditor) return;
            this.saving = true;

            try {
                const res = await fetch(`/admin/plugins/editor/${this.activePlugin}/save`, {
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
                } else {
                    alert(data.error || 'Yadda saxlanmadı');
                }
            } catch (e) {
                alert('Şəbəkə xətası');
            } finally {
                this.saving = false;
            }
        },

        async askAi() {
            if (!this.currentFile || !aceEditor) return;
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
                    alert(data.error || 'AI analizi alınmadı');
                }
            } catch (e) {
                alert('AI xətası');
            } finally {
                this.aiLoading = false;
            }
        }
    };
}
</script>
@endsection
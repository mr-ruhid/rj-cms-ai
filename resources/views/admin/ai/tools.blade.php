@extends('admin.layouts.app')

@section('title', __('AI Tools'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="aiTools()">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-rj-purple" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                {{ __('AI Center') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage models, prompts, and monitor AI usage across your site.') }}</p>
        </div>
    </div>

    <div class="border-b border-gray-200">
        <nav class="flex gap-4 overflow-x-auto">
            <a href="{{ route('admin.ai.index') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                {{ __('Overview') }}
            </a>
            <a href="{{ route('admin.ai.models') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                {{ __('Models') }}
            </a>
            <a href="{{ route('admin.ai.prompts') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                {{ __('Prompts') }}
            </a>
            <a href="{{ route('admin.ai.usage') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                {{ __('Usage') }}
            </a>
            <a href="{{ route('admin.ai.tools') }}" class="px-4 py-3 border-b-2 border-rj-purple text-rj-purple font-semibold text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ __('Tools') }}
            </a>
        </nav>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col group hover:border-rj-purple/40 transition">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Content Generator') }}</h3>
            <p class="text-sm text-gray-500 mb-6 flex-1">{{ __('Generate a full blog post, including title, excerpt, and SEO meta, based on a single topic.') }}</p>
            <button @click="openModal('content')" class="w-full py-2.5 rounded-xl bg-gray-50 text-gray-700 font-semibold text-sm hover:bg-gray-100 transition">
                {{ __('Generate Post') }}
            </button>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col group hover:border-rj-purple/40 transition">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Bulk Translate') }}</h3>
            <p class="text-sm text-gray-500 mb-6 flex-1">{{ __('Translate all posts or pages of a specific type into another language automatically.') }}</p>
            <button @click="openModal('translate')" class="w-full py-2.5 rounded-xl bg-gray-50 text-gray-700 font-semibold text-sm hover:bg-gray-100 transition">
                {{ __('Translate Content') }}
            </button>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col group hover:border-rj-purple/40 transition">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('SEO Auditor') }}</h3>
            <p class="text-sm text-gray-500 mb-6 flex-1">{{ __('Scan your entire site to find published posts missing SEO titles or descriptions.') }}</p>
            
            <button @click="runAudit()" :disabled="loading.audit" class="w-full py-2.5 rounded-xl bg-gray-50 text-gray-700 font-semibold text-sm hover:bg-gray-100 transition flex items-center justify-center gap-2 disabled:opacity-50">
                <svg x-show="loading.audit" x-cloak class="w-4 h-4 animate-spin text-amber-600" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                <span x-show="!loading.audit">{{ __('Run Audit') }}</span>
                <span x-show="loading.audit" x-cloak>{{ __('Scanning...') }}</span>
            </button>
        </div>

    </div>

    <div x-show="modal.show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="closeModal()">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="!loading.modal && closeModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900" x-text="modal.title"></h3>
                <button @click="!loading.modal && closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-5">
                
                <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm" x-text="error"></div>

                <template x-if="modal.type === 'content' && !result">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Topic') }}</label>
                            <input type="text" x-model="form.topic" placeholder="e.g. The benefits of AI in web design" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Tone') }}</label>
                            <select x-model="form.tone" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm bg-white">
                                <option value="professional">Professional</option>
                                <option value="friendly">Friendly</option>
                                <option value="casual">Casual</option>
                                <option value="formal">Formal</option>
                                <option value="persuasive">Persuasive</option>
                            </select>
                        </div>
                    </div>
                </template>

                <template x-if="modal.type === 'translate' && !result">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Post Type') }}</label>
                            <select x-model="form.post_type" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm bg-white">
                                <option value="post">{{ __('Posts') }}</option>
                                <option value="page">{{ __('Pages') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Target Language') }}</label>
                            <select x-model="form.target_locale" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm bg-white">
                                @foreach($locales as $locale)
                                    <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </template>

                <template x-if="result && modal.type === 'content'">
                    <div class="space-y-4">
                        <div class="bg-emerald-50 text-emerald-800 p-3 rounded-xl text-sm border border-emerald-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ __('Content generated successfully. Copy the fields below.') }}
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">{{ __('Title') }}</label>
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-sm font-medium"><span x-text="result.title"></span></div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">{{ __('Excerpt') }}</label>
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-sm text-gray-600" x-text="result.excerpt"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">{{ __('SEO Title') }}</label>
                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-xs" x-text="result.seo_title"></div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">{{ __('SEO Description') }}</label>
                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-xs" x-text="result.seo_description"></div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="result && modal.type === 'translate'">
                    <div class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">{{ __('Translation Queued') }}</h4>
                        <p class="text-sm text-gray-500 mt-2" x-text="result.message"></p>
                    </div>
                </template>

                <template x-if="auditResult">
                    <div>
                        <template x-if="auditResult.length === 0">
                            <div class="text-center py-8">
                                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900">{{ __('All clear!') }}</h4>
                                <p class="text-sm text-gray-500 mt-2">{{ __('No missing SEO data found.') }}</p>
                            </div>
                        </template>
                        <template x-if="auditResult.length > 0">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 mb-3">{{ __('Posts Missing SEO Data') }} (<span x-text="auditResult.length"></span>)</h4>
                                <div class="max-h-60 overflow-y-auto space-y-2 pr-2">
                                    <template x-for="post in auditResult" :key="post.id">
                                        <a :href="post.url" class="flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:border-rj-purple/50 hover:bg-gray-50 transition group">
                                            <span class="text-sm font-medium text-gray-900 group-hover:text-rj-purple" x-text="post.title"></span>
                                            <svg class="w-4 h-4 text-gray-400 group-hover:text-rj-purple" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3">
                <button @click="closeModal()" x-show="!loading.modal" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:text-gray-900 transition">
                    {{ __('Close') }}
                </button>
                
                <button x-show="modal.type !== 'audit' && !result" @click="submitForm()" :disabled="loading.modal" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 transition shadow-sm disabled:opacity-50 min-w-[120px]">
                    <svg x-show="loading.modal" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    <span x-show="!loading.modal">{{ __('Generate') }}</span>
                    <span x-show="loading.modal" x-cloak>{{ __('Processing...') }}</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function aiTools() {
    return {
        modal: { show: false, type: '', title: '' },
        loading: { modal: false, audit: false },
        form: { topic: '', tone: 'professional', post_type: 'post', target_locale: 'az' },
        result: null,
        auditResult: null,
        error: '',

        openModal(type) {
            this.modal = { show: true, type, title: type === 'content' ? '{{ __('Generate Content') }}' : '{{ __('Bulk Translate') }}' };
            this.result = null;
            this.error = '';
            this.form.topic = '';
        },

        closeModal() {
            this.modal.show = false;
            setTimeout(() => { this.result = null; this.auditResult = null; this.error = ''; }, 300);
        },

        async submitForm() {
            this.loading.modal = true;
            this.error = '';
            this.result = null;

            try {
                const url = this.modal.type === 'content' 
                    ? '{{ route('admin.ai.tools.generate-content') }}' 
                    : '{{ route('admin.ai.tools.bulk-translate') }}';

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await res.json();

                if (data.success) {
                    this.result = data.data || data;
                } else {
                    this.error = data.message || '{{ __('An error occurred.') }}';
                }
            } catch (e) {
                this.error = '{{ __('Network error. Please try again.') }}';
            } finally {
                this.loading.modal = false;
            }
        },

        async runAudit() {
            if (this.loading.audit) return;
            this.loading.audit = true;
            this.modal = { show: true, type: 'audit', title: '{{ __('SEO Audit Results') }}' };
            this.auditResult = null;
            this.error = '';

            try {
                const res = await fetch('{{ route('admin.ai.tools.seo-audit') }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.auditResult = data.missing || [];
                } else {
                    this.error = data.message || '{{ __('Failed to run audit.') }}';
                }
            } catch (e) {
                this.error = '{{ __('Network error. Please try again.') }}';
            } finally {
                this.loading.audit = false;
            }
        }
    }
}
</script>
@endpush
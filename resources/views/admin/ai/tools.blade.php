@extends('admin.layouts.app')

@section('title', __('AI Tools'))

@section('content')
@php
    $activeTab = 'admin.ai.tools';
    $tabs = [
        ['route' => 'admin.ai.index',   'label' => __('Overview'), 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
        ['route' => 'admin.ai.models',  'label' => __('Models'),   'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
        ['route' => 'admin.ai.prompts', 'label' => __('Prompts'),  'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
        ['route' => 'admin.ai.usage',   'label' => __('Usage'),    'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ['route' => 'admin.ai.tools',   'label' => __('Tools'),    'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
    ];
@endphp

<div class="max-w-6xl mx-auto space-y-6 pb-12" x-data="aiTools()" @keydown.escape.window="closeModal()">

    {{-- ============ HEADER ============ --}}
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rj-purple to-rj-pink text-white flex items-center justify-center shadow-md flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">{{ __('AI Center') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Manage models, prompts, and monitor AI usage across your site.') }}</p>
        </div>
    </div>

    {{-- ============ TABS ============ --}}
    <div class="border-b border-gray-200">
        <nav class="flex gap-1 overflow-x-auto -mb-px">
            @foreach ($tabs as $tab)
                @php $isActive = $tab['route'] === $activeTab; @endphp
                <a href="{{ route($tab['route']) }}"
                   @if ($isActive) aria-current="page" @endif
                   class="px-4 py-3 border-b-2 text-sm flex items-center gap-2 whitespace-nowrap transition
                          {{ $isActive ? 'border-rj-purple text-rj-purple font-semibold' : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300 font-medium' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}"/></svg>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- ============ TOOL CARDS ============ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Content Generator --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col group hover:border-rj-purple/40 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Content Generator') }}</h3>
            <p class="text-sm text-gray-500 mb-6 flex-1 leading-relaxed">{{ __('Generate a full blog post, including title, excerpt, and SEO meta, based on a single topic.') }}</p>
            <button type="button" @click="openModal('content')"
                    class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white font-semibold text-sm hover:opacity-90 active:scale-[.98] transition shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-rj-purple focus-visible:ring-offset-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                {{ __('Generate Post') }}
            </button>
        </div>

        {{-- Bulk Translate --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col group hover:border-rj-purple/40 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Bulk Translate') }}</h3>
            <p class="text-sm text-gray-500 mb-6 flex-1 leading-relaxed">{{ __('Translate all posts or pages of a specific type into another language automatically.') }}</p>
            <button type="button" @click="openModal('translate')"
                    style="background-color:#059669;"
                    class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl text-white font-semibold text-sm hover:opacity-90 active:scale-[.98] transition shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                {{ __('Translate Content') }}
            </button>
        </div>

        {{-- SEO Auditor --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col group hover:border-rj-purple/40 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('SEO Auditor') }}</h3>
            <p class="text-sm text-gray-500 mb-6 flex-1 leading-relaxed">{{ __('Scan your entire site to find published posts missing SEO titles or descriptions.') }}</p>
            <button type="button" @click="runAudit()" :disabled="loading.audit"
                    style="background-color:#d97706;"
                    class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl text-white font-semibold text-sm hover:opacity-90 active:scale-[.98] transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                <svg x-show="!loading.audit" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <svg x-show="loading.audit" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                <span x-text="loading.audit ? @js(__('Scanning...')) : @js(__('Run Audit'))"></span>
            </button>
        </div>
    </div>

    {{-- ============ MODAL ============ --}}
    <div x-show="modal.show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div x-show="modal.show" x-transition.opacity class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeModal()"></div>

        <div x-show="modal.show"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">

            {{-- Modal header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                         :class="{
                            'bg-indigo-50 text-indigo-600': modal.type === 'content',
                            'bg-emerald-50 text-emerald-600': modal.type === 'translate',
                            'bg-amber-50 text-amber-600': modal.type === 'audit'
                         }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 truncate" x-text="modal.title"></h3>
                </div>
                <button type="button" @click="closeModal()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition" aria-label="{{ __('Close') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal body --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-5">

                <div x-show="error" x-cloak role="alert" class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="error"></span>
                </div>

                {{-- Content form --}}
                <template x-if="modal.type === 'content' && !result">
                    <div class="space-y-4">
                        <div>
                            <label for="ai_topic" class="block text-sm font-semibold text-gray-900 mb-1.5">{{ __('Topic') }}</label>
                            <textarea id="ai_topic" x-model="form.topic" rows="3" maxlength="500"
                                      placeholder="{{ __('e.g. The benefits of AI in web design') }}"
                                      @keydown.ctrl.enter="submitForm()"
                                      class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm resize-none"></textarea>
                            <div class="flex justify-end text-[11px] text-gray-400 mt-1 tabular-nums"><span x-text="form.topic.length"></span>/500</div>
                        </div>
                        <div>
                            <label for="ai_tone" class="block text-sm font-semibold text-gray-900 mb-1.5">{{ __('Tone') }}</label>
                            <select id="ai_tone" x-model="form.tone" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm bg-white">
                                <option value="professional">{{ __('Professional') }}</option>
                                <option value="friendly">{{ __('Friendly') }}</option>
                                <option value="casual">{{ __('Casual') }}</option>
                                <option value="formal">{{ __('Formal') }}</option>
                                <option value="persuasive">{{ __('Persuasive') }}</option>
                            </select>
                        </div>
                    </div>
                </template>

                {{-- Translate form --}}
                <template x-if="modal.type === 'translate' && !result">
                    <div class="space-y-4">
                        <div class="flex items-start gap-3 rounded-xl bg-amber-50 border border-amber-100 px-4 py-3">
                            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-amber-800 leading-relaxed">{{ __('All items of the selected type will be queued for translation. This may take a while and use AI credits.') }}</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ai_post_type" class="block text-sm font-semibold text-gray-900 mb-1.5">{{ __('Post Type') }}</label>
                                <select id="ai_post_type" x-model="form.post_type" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm bg-white">
                                    <option value="post">{{ __('Posts') }}</option>
                                    <option value="page">{{ __('Pages') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="ai_locale" class="block text-sm font-semibold text-gray-900 mb-1.5">{{ __('Target Language') }}</label>
                                <select id="ai_locale" x-model="form.target_locale" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm bg-white">
                                    @foreach ($locales as $locale)
                                        <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Content result --}}
                <template x-if="result && modal.type === 'content'">
                    <div class="space-y-4">
                        <div class="bg-emerald-50 text-emerald-800 p-3 rounded-xl text-sm border border-emerald-200 flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('Content generated successfully. Copy the fields below.') }}
                        </div>

                        <template x-for="field in resultFields" :key="field.key">
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="text-xs font-semibold text-gray-500" x-text="field.label"></label>
                                    <div class="flex items-center gap-3">
                                        <span x-show="field.limit" class="text-[11px] tabular-nums"
                                              :class="field.value.length > field.limit ? 'text-amber-600' : 'text-emerald-600'"
                                              x-text="field.value.length + '/' + field.limit"></span>
                                        <button type="button" @click="copyText(field.value, field.key)"
                                                class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 hover:text-rj-purple transition">
                                            <svg x-show="copied !== field.key" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            <svg x-show="copied === field.key" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <span x-text="copied === field.key ? @js(__('Copied')) : @js(__('Copy'))"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-sm text-gray-800 whitespace-pre-line break-words max-h-48 overflow-y-auto"
                                     :class="field.key === 'title' ? 'font-semibold' : ''" x-text="field.value"></div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Translate result --}}
                <template x-if="result && modal.type === 'translate'">
                    <div class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">{{ __('Translation Queued') }}</h4>
                        <p class="text-sm text-gray-500 mt-2" x-text="result.message"></p>
                    </div>
                </template>

                {{-- Audit: scanning --}}
                <div x-show="modal.type === 'audit' && loading.audit" x-cloak class="text-center py-12">
                    <svg class="w-10 h-10 animate-spin text-amber-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    <p class="text-sm font-medium text-gray-700">{{ __('Scanning...') }}</p>
                </div>

                {{-- Audit: results --}}
                <template x-if="modal.type === 'audit' && auditResult && !loading.audit">
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
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-bold text-gray-900">{{ __('Posts Missing SEO Data') }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 ring-1 ring-amber-100 text-xs font-semibold tabular-nums" x-text="auditResult.length"></span>
                                </div>
                                <div class="max-h-80 overflow-y-auto space-y-2 pr-1">
                                    <template x-for="post in auditResult" :key="post.id">
                                        <a :href="post.url" class="flex items-center justify-between gap-3 p-3 rounded-xl border border-gray-200 hover:border-rj-purple/50 hover:bg-gray-50 transition group">
                                            <span class="text-sm font-medium text-gray-900 group-hover:text-rj-purple truncate" x-text="post.title"></span>
                                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 group-hover:text-rj-purple flex-shrink-0">
                                                {{ __('Edit') }}
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Modal footer --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3 flex-wrap">
                <button type="button" @click="closeModal()" x-show="!loading.modal"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition">
                    {{ __('Close') }}
                </button>

                {{-- Audit: re-scan --}}
                <button type="button" x-show="modal.type === 'audit' && !loading.audit" x-cloak @click="runAudit()"
                        style="background-color:#d97706;"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold hover:opacity-90 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ __('Scan again') }}
                </button>

                {{-- Result: start over --}}
                <button type="button" x-show="result && modal.type !== 'audit'" x-cloak @click="resetForm()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition">
                    {{ __('Start over') }}
                </button>

                {{-- Submit --}}
                <button type="button" x-show="modal.type !== 'audit' && !result" x-cloak @click="submitForm()"
                        :disabled="loading.modal || !canSubmit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-semibold hover:opacity-90 active:scale-[.98] transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed min-w-[130px]">
                    <svg x-show="loading.modal" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    <span x-text="loading.modal ? @js(__('Processing...')) : (modal.type === 'translate' ? @js(__('Translate Content')) : @js(__('Generate')))"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ============ TOAST ============ --}}
    <div x-show="toast.show" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-6 right-6 z-[60] px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium max-w-sm"
         :style="'background:' + (toast.type === 'error' ? '#dc2626' : '#059669')"
         x-text="toast.message"></div>

</div>

<script>
function aiTools() {
    const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

    return {
        modal: { show: false, type: '', title: '' },
        loading: { modal: false, audit: false },
        form: { topic: '', tone: 'professional', post_type: 'post', target_locale: @js($locales[0] ?? 'az') },
        result: null,
        auditResult: null,
        error: '',
        copied: null,
        toast: { show: false, message: '', type: 'success' },
        _toastTimer: null,
        _copyTimer: null,

        get canSubmit() {
            if (this.modal.type === 'content') return this.form.topic.trim().length > 0;
            if (this.modal.type === 'translate') return !!this.form.target_locale;
            return false;
        },

        get resultFields() {
            if (!this.result) return [];
            return [
                { key: 'title',           label: @js(__('Title')),           value: this.result.title || '',           limit: 0 },
                { key: 'excerpt',         label: @js(__('Excerpt')),         value: this.result.excerpt || '',         limit: 0 },
                { key: 'seo_title',       label: @js(__('SEO Title')),       value: this.result.seo_title || '',       limit: 60 },
                { key: 'seo_description', label: @js(__('SEO Description')), value: this.result.seo_description || '', limit: 160 },
            ].filter(f => f.value);
        },

        firstError(data, fallback) {
            if (data && data.errors) {
                const first = Object.values(data.errors)[0];
                if (Array.isArray(first) && first.length) return first[0];
            }
            return (data && data.message) || fallback;
        },

        openModal(type) {
            this.modal = {
                show: true,
                type,
                title: type === 'content' ? @js(__('Generate Content')) : @js(__('Bulk Translate'))
            };
            this.resetForm();
            this.auditResult = null;
        },

        resetForm() {
            this.result = null;
            this.error = '';
            this.form.topic = '';
        },

        closeModal() {
            if (!this.modal.show || this.loading.modal) return;
            this.modal.show = false;
            setTimeout(() => {
                if (this.modal.show) return;
                this.result = null;
                this.auditResult = null;
                this.error = '';
            }, 300);
        },

        async submitForm() {
            if (this.loading.modal || !this.canSubmit) return;

            this.loading.modal = true;
            this.error = '';
            this.result = null;

            try {
                const url = this.modal.type === 'content'
                    ? @js(route('admin.ai.tools.generate-content'))
                    : @js(route('admin.ai.tools.bulk-translate'));

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf()
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await res.json().catch(() => ({}));

                if (res.ok && data.success) {
                    this.result = data.data || data;
                } else {
                    this.error = this.firstError(data, @js(__('An error occurred.')));
                }
            } catch (e) {
                this.error = @js(__('Network error. Please try again.'));
            } finally {
                this.loading.modal = false;
            }
        },

        async runAudit() {
            if (this.loading.audit) return;

            this.loading.audit = true;
            this.modal = { show: true, type: 'audit', title: @js(__('SEO Audit Results')) };
            this.auditResult = null;
            this.result = null;
            this.error = '';

            try {
                const res = await fetch(@js(route('admin.ai.tools.seo-audit')), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json().catch(() => ({}));

                if (res.ok && data.success) {
                    this.auditResult = data.missing || [];
                } else {
                    this.error = this.firstError(data, @js(__('Failed to run audit.')));
                }
            } catch (e) {
                this.error = @js(__('Network error. Please try again.'));
            } finally {
                this.loading.audit = false;
            }
        },

        async copyText(text, id) {
            let ok = false;

            try {
                await navigator.clipboard.writeText(text);
                ok = true;
            } catch (e) {
                try {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.setAttribute('readonly', '');
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    ok = document.execCommand('copy');
                    document.body.removeChild(ta);
                } catch (_) {
                    ok = false;
                }
            }

            if (ok) {
                this.copied = id;
                clearTimeout(this._copyTimer);
                this._copyTimer = setTimeout(() => { this.copied = null; }, 1800);
            } else {
                this.showToast(@js(__('Could not copy.')), 'error');
            }
        },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => { this.toast.show = false; }, 3500);
        }
    };
}
</script>
@endsection
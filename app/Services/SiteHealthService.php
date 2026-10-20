@extends('admin.layouts.app')

@section('title', __('Site Health'))

@section('content')
@php
    $i18n = [
        'excellent' => __('Excellent'),
        'good' => __('Good'),
        'fair' => __('Fair'),
        'poor' => __('Poor'),
        'warning' => __('Warning'),
        'critical' => __('Critical'),
        'passing' => __('Good'),
        'issuesCritical' => __(':n critical issue(s) need fixing first.'),
        'issuesWarning' => __(':n warning(s) are worth a look.'),
        'allGood' => __('Everything looks healthy.'),
        'refreshFailed' => __('Refresh failed.'),
        'refreshed' => __('Checks updated.'),
        'networkError' => __('Network error.'),
        'aiFailed' => __('AI analysis failed.'),
        'copied' => __('Copied to clipboard.'),
        'copyFailed' => __('Could not copy.'),
        'aiOff' => __('AI is not configured'),
        'analyze' => __('AI Analysis'),
        'analyzing' => __('Analyzing...'),
        'severity' => [
            'critical' => __('Critical'),
            'high' => __('High'),
            'medium' => __('Medium'),
            'low' => __('Low'),
        ],
    ];

    $urls = [
        'analyze' => route('admin.tools.health.analyze'),
        'refresh' => route('admin.tools.health.refresh'),
    ];
@endphp

<div class="max-w-6xl mx-auto space-y-6 pb-12" x-data="healthTool(@js($boot), @js($i18n), @js($urls))" @keydown.escape.window="drawer = false">

    {{-- HEADER --}}
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('Site Health') }}</h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Automated checks and AI-powered analysis of your site.') }}
                <span class="text-gray-400 block sm:inline sm:ml-2">· {{ __('Last checked') }}: <span x-text="checkedAt"></span></span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="refresh()" :disabled="refreshing"
                    class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition disabled:opacity-60 shadow-sm">
                <svg :class="refreshing ? 'animate-spin' : ''" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                {{ __('Refresh') }}
            </button>

            <button type="button" @click="openAi()" :disabled="!aiEnabled || aiLoading"
                    :title="!aiEnabled ? t.aiOff : ''"
                    class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                <svg x-show="!aiLoading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <svg x-show="aiLoading" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                <span x-text="aiLoading ? t.analyzing : t.analyze"></span>
            </button>
        </div>
    </div>

    @if (!$aiEnabled)
        <div class="flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 shadow-sm">
            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-blue-900 leading-relaxed">
                {{ __('AI analysis is disabled. Configure OpenRouter API key in Settings → AI to unlock detailed analysis.') }}
                <a href="{{ route('admin.settings.ai') }}" class="ml-1 font-bold underline hover:text-blue-700">{{ __('Configure now') }} →</a>
            </p>
        </div>
    @endif

    {{-- OVERVIEW --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            
            {{-- Score Ring --}}
            <div class="md:col-span-4 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 pb-6 md:pb-0 md:pr-6">
                <div class="relative w-40 h-40">
                    <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="#f3f4f6" stroke-width="10"/>
                        <circle cx="60" cy="60" r="52" fill="none" :stroke="scoreColor" stroke-width="10" stroke-linecap="round"
                                :stroke-dasharray="circumference" :stroke-dashoffset="dashOffset" class="health-ring"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-extrabold tabular-nums tracking-tight" :style="'color:' + scoreColor" x-text="summary.score"></span>
                        <span class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mt-1">{{ __('Score') }}</span>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <div class="inline-flex px-3 py-1 rounded-full text-sm font-bold"
                         :class="{
                            'bg-emerald-100 text-emerald-700': summary.score >= 90,
                            'bg-amber-100 text-amber-700': summary.score >= 70 && summary.score < 90,
                            'bg-orange-100 text-orange-700': summary.score >= 50 && summary.score < 70,
                            'bg-red-100 text-red-700': summary.score < 50
                         }"
                         x-text="scoreLabel"></div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="md:col-span-8 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" x-text="verdict"></h3>
                    <p class="text-sm text-gray-500 mt-1" x-text="summary.total + ' {{ __('checks performed') }}'"></p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 tabular-nums" x-text="summary.good"></div>
                            <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">{{ __('Good') }}</div>
                        </div>
                    </div>

                    <div class="bg-amber-50 rounded-xl p-4 border border-amber-100 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 tabular-nums" x-text="summary.warning"></div>
                            <div class="text-xs font-semibold text-amber-700 uppercase tracking-wide">{{ __('Warnings') }}</div>
                        </div>
                    </div>

                    <div class="bg-red-50 rounded-xl p-4 border border-red-100 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 tabular-nums" x-text="summary.critical"></div>
                            <div class="text-xs font-semibold text-red-700 uppercase tracking-wide">{{ __('Critical') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
        <div class="relative w-full sm:max-w-md">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
            <input type="search" x-model.debounce.150ms="query" placeholder="{{ __('Search checks...') }}"
                   class="w-full h-10 pl-9 pr-3 rounded-lg border border-gray-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
        </div>
        
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <button type="button" x-show="status !== 'all' || query" x-cloak @click="status = 'all'; query = ''"
                    class="text-xs font-medium text-indigo-600 hover:underline px-2">
                {{ __('Clear filters') }}
            </button>
            <button type="button" @click="toggleAll()" class="h-10 px-4 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition"
                    x-text="allCollapsed ? '{{ __('Expand all') }}' : '{{ __('Collapse all') }}'"></button>
        </div>
    </div>

    {{-- LIST --}}
    <div class="space-y-4">
        <template x-for="cat in visibleCategories" :key="cat.key">
            <div :id="'cat-' + cat.key" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden scroll-mt-6">
                
                {{-- Category Header --}}
                <button type="button" @click="collapsed[cat.key] = !collapsed[cat.key]"
                        class="w-full px-5 py-4 flex items-center justify-between gap-3 bg-gray-50/50 hover:bg-gray-50 transition text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-html="icon(cat.icon)"></svg>
                        </div>
                        <span class="text-sm font-bold text-gray-900" x-text="cat.label"></span>
                        <span class="inline-flex items-center gap-1.5">
                            <span x-show="cat.counts.critical > 0" class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-100 text-red-700" x-text="cat.counts.critical + ' {{ __('Critical') }}'"></span>
                            <span x-show="cat.counts.warning > 0" class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-700" x-text="cat.counts.warning + ' {{ __('Warning') }}'"></span>
                        </span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="collapsed[cat.key] ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>

                {{-- Category Items --}}
                <ul x-show="!collapsed[cat.key]" class="divide-y divide-gray-100 border-t border-gray-200">
                    <template x-for="item in visibleItems(cat)" :key="item.id">
                        <li class="px-5 py-4 flex flex-col sm:flex-row sm:items-start gap-3 hover:bg-gray-50/30 transition-colors">
                            
                            {{-- Status Icon --}}
                            <div class="flex-shrink-0 mt-0.5">
                                <template x-if="item.status === 'good'">
                                    <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </template>
                                <template x-if="item.status === 'warning'">
                                    <div class="w-5 h-5 rounded-full bg-amber-100 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/></svg>
                                    </div>
                                </template>
                                <template x-if="item.status === 'critical'">
                                    <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </div>
                                </template>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                    <span class="text-sm font-bold text-gray-900" x-text="item.label"></span>
                                    <span class="text-sm text-gray-500 font-mono bg-gray-100 px-1.5 py-0.5 rounded text-xs" x-text="item.message"></span>
                                </div>
                                
                                <p x-show="item.recommendation" class="mt-1.5 text-xs text-amber-700 bg-amber-50 border border-amber-100 p-2 rounded inline-flex items-start gap-1.5 w-full sm:w-auto">
                                    <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span x-text="item.recommendation"></span>
                                </p>
                            </div>

                            {{-- Status Badge --}}
                            <div class="flex-shrink-0 mt-1 sm:mt-0">
                                <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide"
                                      :class="st[item.status].badge" x-text="st[item.status].label"></span>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>
        </template>
        
        {{-- Empty State --}}
        <div x-show="visibleCategories.length === 0" x-cloak class="bg-white rounded-xl border border-dashed border-gray-300 py-16 text-center shadow-sm">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <p class="text-sm font-medium text-gray-900">{{ __('No checks match your filters.') }}</p>
            <button type="button" @click="status = 'all'; query = ''" class="mt-3 text-sm font-bold text-indigo-600 hover:underline">{{ __('Clear all filters') }}</button>
        </div>
    </div>

    {{-- AI DRAWER (Modal) --}}
    <div x-show="drawer" x-cloak class="fixed inset-0 z-50" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" x-show="drawer" x-transition.opacity @click="drawer = false"></div>

        <div class="absolute inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl flex flex-col"
             x-show="drawer"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold">{{ __('AI Health Analysis') }}</h3>
                        <p class="text-xs text-blue-100 mt-0.5" x-text="aiData && aiData.model ? aiData.model : aiModel"></p>
                    </div>
                </div>
                <button type="button" @click="drawer = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6 custom-scrollbar bg-gray-50/50">
                {{-- Loading --}}
                <div x-show="aiLoading" x-cloak class="space-y-4 pt-10 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900">{{ __('Analyzing your site...') }}</h4>
                    <p class="text-sm text-gray-500">{{ __('OpenRouter is reviewing your health checks.') }}</p>
                </div>

                {{-- Error --}}
                <div x-show="aiError && !aiLoading" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800" x-text="aiError"></p>
                    <button type="button" @click="runAi(true)" class="mt-3 px-3 py-1.5 rounded-lg bg-white border border-red-200 text-sm font-bold text-red-700 hover:bg-red-100 transition">{{ __('Try again') }}</button>
                </div>

                {{-- Result --}}
                <template x-if="aiData && !aiLoading && !aiError">
                    <div class="space-y-6">
                        <div class="rounded-xl border p-5 shadow-sm" :class="overallStyle(aiData.overall)">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-white/60 border border-black/5" x-text="aiData.overall"></span>
                                <span class="text-xs font-medium text-gray-600">{{ __('Score') }}: <strong class="text-gray-900" x-text="aiData.score"></strong></span>
                            </div>
                            <h4 class="text-base font-bold text-gray-900 leading-snug" x-text="aiData.headline"></h4>
                            <p class="text-sm text-gray-700 mt-2 leading-relaxed" x-text="aiData.summary"></p>
                        </div>

                        <div x-show="aiData.priorities && aiData.priorities.length">
                            <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                {{ __('Action Plan') }}
                            </h4>
                            <div class="space-y-4">
                                <template x-for="(p, idx) in aiData.priorities" :key="idx">
                                    <div class="rounded-xl border bg-white p-4 shadow-sm" :class="sev[p.severity].border">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                                    <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded" :class="sev[p.severity].badge" x-text="t.severity[p.severity]"></span>
                                                    <span x-show="p.category" class="text-[10px] text-gray-500 uppercase font-semibold" x-text="p.category"></span>
                                                </div>
                                                <h5 class="text-sm font-bold text-gray-900" x-text="p.title"></h5>
                                            </div>
                                        </div>

                                        <p x-show="p.why" class="text-xs text-gray-600 mt-1.5 leading-relaxed" x-text="p.why"></p>

                                        <div x-show="p.action" class="mt-3 rounded-lg bg-gray-900 overflow-hidden">
                                            <div class="flex items-center justify-between px-3 py-1.5 border-b border-white/10">
                                                <span class="text-[10px] font-bold uppercase tracking-wide text-gray-400">{{ __('Action to take') }}</span>
                                                <button type="button" @click="copy(p.action, idx)" class="text-xs text-gray-300 hover:text-white transition font-medium"
                                                        x-text="copiedIdx === idx ? t.copied : '{{ __('Copy') }}'"></button>
                                            </div>
                                            <pre class="text-xs text-gray-100 whitespace-pre-wrap font-mono leading-relaxed p-3 overflow-x-auto" x-text="p.action"></pre>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div x-show="aiData.quick_wins && aiData.quick_wins.length" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                            <h4 class="text-sm font-bold text-emerald-900 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                {{ __('Quick Wins') }}
                            </h4>
                            <ul class="space-y-1.5">
                                <template x-for="(w, i) in aiData.quick_wins" :key="i">
                                    <li class="flex items-start gap-2 text-sm text-emerald-800 font-medium">
                                        <svg class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span x-text="w"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </template>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-white flex items-center justify-between">
                <span class="text-[10px] text-gray-400 max-w-[200px] leading-tight">{{ __('AI suggestions can be wrong. Review commands before running.') }}</span>
                <button type="button" @click="drawer = false" class="px-5 py-2 rounded-lg bg-gray-900 text-white text-sm font-bold hover:bg-gray-800 transition shadow-sm">{{ __('Close') }}</button>
            </div>
        </div>
    </div>

    {{-- TOAST --}}
    <div x-show="toast.show" x-cloak x-transition aria-live="polite"
         class="fixed bottom-6 right-6 px-4 py-3 rounded-xl shadow-lg text-white text-sm font-bold z-[60]"
         :class="toast.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'" x-text="toast.message"></div>
</div>

<script>
function healthTool(boot, t, urls) {
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

    const ICONS = {
        server: '<path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>',
        folder: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>',
        database: '<path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>',
        shield: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
        bolt: '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        document: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        list: '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>',
        plug: '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M7 11v4a5 5 0 0010 0v-4M12 20v1"/>',
        circle: '<circle cx="12" cy="12" r="9"/>',
    };

    return {
        summary: boot.summary,
        categories: boot.categories,
        checkedAt: boot.checkedAt,
        aiEnabled: boot.aiEnabled,
        aiModel: boot.aiModel,
        t: t,

        circumference: 2 * Math.PI * 52,
        animatedScore: 0,

        status: 'all',
        query: '',
        collapsed: {},
        refreshing: false,

        drawer: false,
        aiLoading: false,
        aiError: '',
        aiData: null,
        copiedIdx: null,

        toast: { show: false, message: '', type: 'success' },
        _toastTimer: null,

        st: {
            good: { label: t.passing, dot: 'bg-emerald-500', badge: 'bg-emerald-100 text-emerald-700' },
            warning: { label: t.warning, dot: 'bg-amber-400', badge: 'bg-amber-100 text-amber-700' },
            critical: { label: t.critical, dot: 'bg-red-500', badge: 'bg-red-100 text-red-700' },
        },

        sev: {
            critical: { badge: 'bg-red-100 text-red-700', border: 'border-red-200' },
            high: { badge: 'bg-orange-100 text-orange-700', border: 'border-orange-200' },
            medium: { badge: 'bg-amber-100 text-amber-700', border: 'border-amber-200' },
            low: { badge: 'bg-gray-100 text-gray-600', border: 'border-gray-200' },
        },

        init() {
            this.$nextTick(() => setTimeout(() => { this.animatedScore = this.summary.score; }, 80));
        },

        get dashOffset() {
            return this.circumference * ((100 - this.animatedScore) / 100);
        },

        get scoreColor() {
            const s = this.summary.score;
            if (s >= 90) return '#10b981';
            if (s >= 70) return '#f59e0b';
            if (s >= 50) return '#f97316';
            return '#ef4444';
        },

        get scoreLabel() {
            const s = this.summary.score;
            if (s >= 90) return t.excellent;
            if (s >= 70) return t.good;
            if (s >= 50) return t.fair;
            return t.poor;
        },

        get verdict() {
            if (this.summary.critical > 0) return t.issuesCritical.replace(':n', this.summary.critical);
            if (this.summary.warning > 0) return t.issuesWarning.replace(':n', this.summary.warning);
            return t.allGood;
        },

        get visibleCategories() {
            return this.categories.filter(c => this.visibleItems(c).length > 0);
        },

        get allCollapsed() {
            return this.categories.length > 0 && this.categories.every(c => this.collapsed[c.key]);
        },

        icon(name) {
            return ICONS[name] || ICONS.circle;
        },

        visibleItems(cat) {
            const q = this.query.trim().toLowerCase();
            return cat.items.filter(i => {
                if (this.status !== 'all' && i.status !== this.status) return false;
                if (!q) return true;
                return (i.label + ' ' + i.message + ' ' + (i.recommendation || '')).toLowerCase().includes(q);
            });
        },

        toggleStatus(key) {
            this.status = this.status === key ? 'all' : key;
        },

        toggleAll() {
            const collapse = !this.allCollapsed;
            const next = {};
            this.categories.forEach(c => { next[c.key] = collapse; });
            this.collapsed = next;
        },

        overallStyle(overall) {
            if (overall === 'excellent' || overall === 'good') return 'bg-emerald-50 border-emerald-200';
            if (overall === 'fair') return 'bg-amber-50 border-amber-200';
            return 'bg-red-50 border-red-200';
        },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => { this.toast.show = false; }, 3500);
        },

        async request(url, body) {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body || {}),
            });
            const data = await res.json().catch(() => ({}));
            return { ok: res.ok, data };
        },

        async refresh() {
            if (this.refreshing) return;
            this.refreshing = true;
            try {
                const { ok, data } = await this.request(urls.refresh);
                if (!ok || !data.success) {
                    this.showToast(data.message || t.refreshFailed, 'error');
                    return;
                }
                this.summary = data.summary;
                this.categories = data.categories;
                this.checkedAt = data.checked_at;
                this.animatedScore = data.summary.score;
                this.aiData = null;
                this.showToast(t.refreshed);
            } catch (e) {
                this.showToast(t.refreshFailed, 'error');
            } finally {
                this.refreshing = false;
            }
        },

        openAi() {
            if (!this.aiEnabled || this.aiLoading) return;
            this.drawer = true;
            if (!this.aiData) this.runAi(false);
        },

        async runAi(force) {
            if (this.aiLoading) return;
            this.drawer = true;
            this.aiLoading = true;
            this.aiError = '';
            if (force) this.aiData = null;

            try {
                const { ok, data } = await this.request(urls.analyze, { force: !!force });
                if (ok && data.success) {
                    this.aiData = data.data || null;
                } else {
                    this.aiError = data.message || t.aiFailed;
                }
            } catch (e) {
                this.aiError = t.networkError;
            } finally {
                this.aiLoading = false;
            }
        },

        async copy(text, idx) {
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(text);
                } else {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                }
                this.copiedIdx = idx;
                setTimeout(() => { if (this.copiedIdx === idx) this.copiedIdx = null; }, 1800);
            } catch (e) {
                this.showToast(t.copyFailed, 'error');
            }
        },
    };
}
</script>

<style>
.health-ring { transition: stroke-dashoffset 1.2s cubic-bezier(.4, 0, .2, 1), stroke .3s; }
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
@media (prefers-reduced-motion: reduce) {
    .health-ring { transition: none; }
}
</style>
@endsection
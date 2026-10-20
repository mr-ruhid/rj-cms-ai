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
        'passing' => __('Passing'),
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
<div class="max-w-7xl mx-auto space-y-6" x-data="healthTool(@js($boot), @js($i18n), @js($urls))" @keydown.escape.window="drawer = false">

    {{-- HEADER --}}
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('Site Health') }}</h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Automated checks and AI-powered analysis of your site.') }}
                <span class="text-gray-400">· {{ __('Last checked') }}: <span x-text="checkedAt"></span></span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="refresh()" :disabled="refreshing"
                    class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition disabled:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                <svg :class="refreshing ? 'animate-spin' : ''" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                {{ __('Refresh') }}
            </button>

            <button type="button" @click="openAi()" :disabled="!aiEnabled || aiLoading"
                    :title="!aiEnabled ? t.aiOff : ''"
                    class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500">
                <svg x-show="!aiLoading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                <svg x-show="aiLoading" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                <span x-text="aiLoading ? t.analyzing : t.analyze"></span>
            </button>
        </div>
    </div>

    @if (!$aiEnabled)
        <div class="flex items-start gap-3 rounded-lg border border-indigo-100 bg-indigo-50/60 px-4 py-3">
            <svg class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-indigo-900">
                {{ __('AI analysis is disabled. Configure OpenRouter API key in Settings → AI to unlock detailed analysis and actionable fixes.') }}
                <a href="{{ route('admin.settings.ai') }}" class="font-semibold underline hover:text-indigo-700">{{ __('Configure now') }}</a>
            </p>
        </div>
    @endif

    {{-- OVERVIEW --}}
    <section class="bg-white rounded-xl border border-gray-200 p-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">

        <div class="md:col-span-3 flex flex-col items-center">
            <div class="relative w-36 h-36">
                <svg class="w-36 h-36 -rotate-90" viewBox="0 0 120 120" aria-hidden="true">
                    <circle cx="60" cy="60" r="52" fill="none" stroke="#f3f4f6" stroke-width="9"/>
                    <circle cx="60" cy="60" r="52" fill="none" :stroke="scoreColor" stroke-width="9" stroke-linecap="round"
                            :stroke-dasharray="circumference" :stroke-dashoffset="dashOffset" class="health-ring"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-4xl font-bold tabular-nums" :style="'color:' + scoreColor" x-text="summary.score"></span>
                    <span class="text-xs text-gray-400 mt-0.5">/ 100</span>
                </div>
            </div>
        </div>

        <div class="md:col-span-9 space-y-5">
            <div>
                <div class="flex items-baseline gap-3 flex-wrap">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="scoreLabel"></h3>
                    <span class="text-sm text-gray-400"><span x-text="summary.total"></span> {{ __('checks performed') }}</span>
                </div>
                <p class="text-sm text-gray-600 mt-1" x-text="verdict"></p>
            </div>

            {{-- proportion bar --}}
            <div class="flex h-2 rounded-full overflow-hidden bg-gray-100" role="img" :aria-label="summary.good + ' / ' + summary.warning + ' / ' + summary.critical">
                <div class="bg-emerald-500 transition-all duration-700" :style="'width:' + pct('good') + '%'"></div>
                <div class="bg-amber-400 transition-all duration-700" :style="'width:' + pct('warning') + '%'"></div>
                <div class="bg-red-500 transition-all duration-700" :style="'width:' + pct('critical') + '%'"></div>
            </div>

            {{-- stat filters --}}
            <div class="grid grid-cols-3 gap-3">
                <template x-for="s in stats" :key="s.key">
                    <button type="button" @click="toggleStatus(s.key)"
                            class="text-left rounded-lg border px-4 py-3 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                            :class="status === s.key ? s.active : 'bg-white border-gray-200 hover:border-gray-300'"
                            :aria-pressed="status === s.key">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="s.dot"></span>
                            <span class="text-sm text-gray-600" x-text="s.label"></span>
                        </div>
                        <div class="text-2xl font-bold text-gray-900 mt-1 tabular-nums" x-text="summary[s.key]"></div>
                    </button>
                </template>
            </div>
        </div>
    </section>

    {{-- BODY --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- category nav --}}
        <aside class="lg:col-span-3 lg:sticky lg:top-6">
            <nav class="bg-white rounded-xl border border-gray-200 p-2" aria-label="{{ __('Categories') }}">
                <template x-for="cat in categories" :key="cat.key">
                    <a :href="'#cat-' + cat.key" @click.prevent="jump(cat.key)"
                       class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                        <span class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-html="icon(cat.icon)"></svg>
                            <span class="truncate" x-text="cat.label"></span>
                        </span>
                        <span class="inline-flex items-center gap-1 flex-shrink-0">
                            <span x-show="cat.counts.critical > 0" class="min-w-[1.25rem] text-center text-xs font-semibold px-1.5 py-0.5 rounded bg-red-100 text-red-700" x-text="cat.counts.critical"></span>
                            <span x-show="cat.counts.warning > 0" class="min-w-[1.25rem] text-center text-xs font-semibold px-1.5 py-0.5 rounded bg-amber-100 text-amber-700" x-text="cat.counts.warning"></span>
                            <svg x-show="cat.counts.critical === 0 && cat.counts.warning === 0" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                    </a>
                </template>
            </nav>
        </aside>

        {{-- results --}}
        <div class="lg:col-span-9 space-y-4">

            {{-- toolbar --}}
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                    <input type="search" x-model.debounce.150ms="query" placeholder="{{ __('Search checks...') }}"
                           class="w-full h-10 pl-9 pr-3 rounded-lg border border-gray-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="button" x-show="status !== 'all' || query" x-cloak @click="status = 'all'; query = ''"
                        class="h-10 px-3 rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition">
                    {{ __('Clear filters') }}
                </button>
                <button type="button" @click="toggleAll()" class="h-10 px-3 rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition"
                        x-text="allCollapsed ? '{{ __('Expand all') }}' : '{{ __('Collapse all') }}'"></button>
            </div>

            {{-- empty state --}}
            <div x-show="visibleCategories.length === 0" x-cloak class="bg-white rounded-xl border border-dashed border-gray-300 py-14 text-center">
                <p class="text-sm font-medium text-gray-900">{{ __('No checks match these filters.') }}</p>
                <button type="button" @click="status = 'all'; query = ''" class="mt-2 text-sm text-indigo-600 hover:underline">{{ __('Clear filters') }}</button>
            </div>

            <template x-for="cat in visibleCategories" :key="cat.key">
                <section :id="'cat-' + cat.key" class="bg-white rounded-xl border border-gray-200 overflow-hidden scroll-mt-6">
                    <button type="button" @click="collapsed[cat.key] = !collapsed[cat.key]"
                            class="w-full px-5 py-3.5 flex items-center justify-between gap-3 hover:bg-gray-50 transition text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500"
                            :aria-expanded="!collapsed[cat.key]">
                        <span class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-html="icon(cat.icon)"></svg>
                            </span>
                            <span class="text-sm font-semibold text-gray-900" x-text="cat.label"></span>
                            <span class="text-xs text-gray-400" x-text="visibleItems(cat).length + ' / ' + cat.items.length"></span>
                        </span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="collapsed[cat.key] ? '' : 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <ul x-show="!collapsed[cat.key]" class="divide-y divide-gray-100 border-t border-gray-100">
                        <template x-for="item in visibleItems(cat)" :key="item.id">
                            <li class="px-5 py-3.5 flex items-start gap-3">
                                <span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0" :class="st[item.status].dot"></span>

                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-0.5">
                                        <span class="text-sm font-medium text-gray-900" x-text="item.label"></span>
                                        <span class="text-sm text-gray-500 break-words" x-text="item.message"></span>
                                    </div>
                                    <p x-show="item.recommendation" class="mt-1.5 text-xs leading-relaxed pl-3 border-l-2"
                                       :class="st[item.status].note" x-text="item.recommendation"></p>
                                </div>

                                <span class="text-xs font-medium px-2 py-0.5 rounded flex-shrink-0" :class="st[item.status].badge" x-text="st[item.status].label"></span>
                            </li>
                        </template>
                    </ul>
                </section>
            </template>
        </div>
    </div>

    {{-- AI DRAWER --}}
    <div x-show="drawer" x-cloak class="fixed inset-0 z-50" role="dialog" aria-modal="true" aria-label="{{ __('AI Health Analysis') }}">
        <div class="absolute inset-0 bg-gray-900/40" x-show="drawer" x-transition.opacity @click="drawer = false"></div>

        <div class="absolute inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl flex flex-col"
             x-show="drawer"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">

            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('AI Health Analysis') }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                        <span x-text="aiData && aiData.model ? aiData.model : aiModel"></span>
                        <span x-show="aiCached" x-cloak> · {{ __('cached result') }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" x-show="aiData && !aiLoading" x-cloak @click="runAi(true)"
                            class="h-9 px-3 rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition">{{ __('Re-analyze') }}</button>
                    <button type="button" @click="drawer = false" class="w-9 h-9 rounded-lg text-gray-500 hover:bg-gray-100 flex items-center justify-center transition" aria-label="{{ __('Close') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6 health-scroll">

                {{-- loading --}}
                <div x-show="aiLoading" x-cloak class="space-y-4" aria-live="polite">
                    <p class="text-sm text-gray-600">{{ __('Reviewing your health checks and ranking what to fix first...') }}</p>
                    <div class="space-y-3 animate-pulse">
                        <div class="h-24 rounded-lg bg-gray-100"></div>
                        <div class="h-32 rounded-lg bg-gray-100"></div>
                        <div class="h-32 rounded-lg bg-gray-100"></div>
                    </div>
                </div>

                {{-- error --}}
                <div x-show="aiError && !aiLoading" x-cloak class="rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800" x-text="aiError"></p>
                    <button type="button" @click="runAi(true)" class="mt-3 h-9 px-3 rounded-lg bg-white border border-red-200 text-sm text-red-700 hover:bg-red-100 transition">{{ __('Try again') }}</button>
                </div>

                {{-- result --}}
                <template x-if="aiData && !aiLoading && !aiError">
                    <div class="space-y-6">

                        <div class="rounded-lg border p-5" :class="overallStyle(aiData.overall)">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-white/70 border border-black/5" x-text="aiData.overall"></span>
                                <span class="text-xs text-gray-600">{{ __('Score') }}: <span class="font-semibold" x-text="aiData.score"></span></span>
                            </div>
                            <h4 class="text-base font-semibold text-gray-900 leading-snug" x-text="aiData.headline"></h4>
                            <p class="text-sm text-gray-700 mt-2 leading-relaxed" x-text="aiData.summary"></p>
                        </div>

                        <div x-show="aiData.priorities && aiData.priorities.length">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ __('Action Plan') }}</h4>
                            <ol class="space-y-3">
                                <template x-for="(p, idx) in aiData.priorities" :key="idx">
                                    <li class="rounded-lg border bg-white p-4" :class="sev[p.severity].border">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="text-xs font-semibold px-2 py-0.5 rounded" :class="sev[p.severity].badge" x-text="t.severity[p.severity]"></span>
                                                    <span x-show="p.category" class="text-xs text-gray-500" x-text="p.category"></span>
                                                </div>
                                                <h5 class="text-sm font-semibold text-gray-900 mt-2" x-text="p.title"></h5>
                                            </div>
                                            <span class="text-xs text-gray-400 tabular-nums flex-shrink-0" x-text="'#' + (idx + 1)"></span>
                                        </div>

                                        <p x-show="p.why" class="text-sm text-gray-600 mt-1.5 leading-relaxed" x-text="p.why"></p>

                                        <div x-show="p.action" class="mt-3 rounded-md bg-gray-900 overflow-hidden">
                                            <div class="flex items-center justify-between px-3 py-1.5 border-b border-white/10">
                                                <span class="text-xs text-gray-400">{{ __('Action to take') }}</span>
                                                <button type="button" @click="copy(p.action, idx)" class="text-xs text-gray-300 hover:text-white transition"
                                                        x-text="copiedIdx === idx ? '{{ __('Copied') }}' : '{{ __('Copy') }}'"></button>
                                            </div>
                                            <pre class="text-xs text-gray-100 whitespace-pre-wrap font-mono leading-relaxed p-3 overflow-x-auto" x-text="p.action"></pre>
                                        </div>
                                    </li>
                                </template>
                            </ol>
                        </div>

                        <div x-show="aiData.quick_wins && aiData.quick_wins.length" class="rounded-lg border border-emerald-100 bg-emerald-50/60 p-4">
                            <h4 class="text-sm font-semibold text-emerald-900 mb-2">{{ __('Quick Wins') }}</h4>
                            <ul class="space-y-1.5">
                                <template x-for="(w, i) in aiData.quick_wins" :key="i">
                                    <li class="flex items-start gap-2 text-sm text-emerald-900">
                                        <svg class="w-4 h-4 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span x-text="w"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </template>
            </div>

            <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-500">{{ __('AI suggestions can be wrong. Review commands before running them.') }}</span>
                <button type="button" @click="drawer = false" class="h-9 px-4 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition">{{ __('Close') }}</button>
            </div>
        </div>
    </div>

    {{-- TOAST --}}
    <div x-show="toast.show" x-cloak x-transition aria-live="polite"
         class="fixed bottom-6 right-6 px-4 py-2.5 rounded-lg shadow-lg text-white text-sm font-medium z-[60]"
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
        aiCached: false,
        copiedIdx: null,

        toast: { show: false, message: '', type: 'success' },
        _toastTimer: null,

        st: {
            good: { label: t.passing, dot: 'bg-emerald-500', badge: 'bg-emerald-50 text-emerald-700', note: 'border-emerald-300 text-emerald-800' },
            warning: { label: t.warning, dot: 'bg-amber-400', badge: 'bg-amber-50 text-amber-700', note: 'border-amber-300 text-amber-800' },
            critical: { label: t.critical, dot: 'bg-red-500', badge: 'bg-red-50 text-red-700', note: 'border-red-300 text-red-800' },
        },

        sev: {
            critical: { badge: 'bg-red-100 text-red-700', border: 'border-red-200' },
            high: { badge: 'bg-orange-100 text-orange-700', border: 'border-orange-200' },
            medium: { badge: 'bg-amber-100 text-amber-700', border: 'border-amber-200' },
            low: { badge: 'bg-gray-100 text-gray-600', border: 'border-gray-200' },
        },

        init() {
            // Halqa animasiyası: 0-dan real skora
            this.$nextTick(() => setTimeout(() => { this.animatedScore = this.summary.score; }, 80));
        },

        /* ---------- computed ---------- */

        get stats() {
            return [
                { key: 'good', label: t.passing, dot: 'bg-emerald-500', active: 'bg-emerald-50 border-emerald-300' },
                { key: 'warning', label: t.warning, dot: 'bg-amber-400', active: 'bg-amber-50 border-amber-300' },
                { key: 'critical', label: t.critical, dot: 'bg-red-500', active: 'bg-red-50 border-red-300' },
            ];
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

        /* ---------- helpers ---------- */

        pct(key) {
            return this.summary.total ? (this.summary[key] / this.summary.total) * 100 : 0;
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

        jump(key) {
            this.collapsed[key] = false;
            this.$nextTick(() => {
                document.getElementById('cat-' + key)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
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

        /* ---------- actions ---------- */

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
                this.aiData = null; // köhnə AI nəticəsi artıq aktual deyil
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
                    this.aiCached = !!data.cached;
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
.health-scroll::-webkit-scrollbar { width: 6px; }
.health-scroll::-webkit-scrollbar-track { background: transparent; }
.health-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
.health-scroll::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
@media (prefers-reduced-motion: reduce) {
    .health-ring { transition: none; }
}
</style>
@endsection
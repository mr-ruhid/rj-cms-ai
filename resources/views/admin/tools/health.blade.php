@extends('admin.layouts.app')

@section('title', __('Site Health'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="healthTool(@js($summary), @js($aiEnabled), @js($aiModel))">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Site Health') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Automated checks and AI-powered analysis of your site.') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="refresh()" :disabled="refreshing"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition disabled:opacity-60">
                <svg :class="refreshing ? 'animate-spin' : ''" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                {{ __('Refresh') }}
            </button>

            <button type="button" @click="analyze()" :disabled="!aiEnabled || analyzing"
                    :title="!aiEnabled ? '{{ __('AI is not configured') }}' : ''"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg x-show="!analyzing" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <svg x-show="analyzing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                <span x-text="analyzing ? '{{ __('Analyzing...') }}' : '{{ __('AI Analysis') }}'"></span>
            </button>
        </div>
    </div>

    @if (!$aiEnabled)
        <div class="bg-amber-50 border border-amber-100 rounded-lg p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm text-amber-800 flex-1">
                {{ __('AI analysis is disabled. Configure OpenRouter API key in Settings → AI to unlock detailed analysis.') }}
                <a href="{{ route('admin.settings.ai') }}" class="ml-2 font-medium underline hover:no-underline">{{ __('Configure now') }} →</a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div class="md:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center">
            <div class="relative w-32 h-32">
                <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="52" fill="none" stroke="#f3f4f6" stroke-width="10"/>
                    <circle cx="60" cy="60" r="52" fill="none"
                            :stroke="scoreColor" stroke-width="10" stroke-linecap="round"
                            :stroke-dasharray="circumference"
                            :stroke-dashoffset="dashOffset"
                            style="transition: stroke-dashoffset 1s ease, stroke .3s;"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center flex-col">
                    <div class="text-3xl font-bold" :style="'color:' + scoreColor" x-text="summary.score"></div>
                    <div class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold">{{ __('Score') }}</div>
                </div>
            </div>
            <div class="mt-3 text-center">
                <div class="text-sm font-semibold" :style="'color:' + scoreColor" x-text="scoreLabel"></div>
                <div class="text-xs text-gray-500 mt-0.5">{{ $summary['total'] }} {{ __('checks') }}</div>
            </div>
        </div>

        <div class="md:col-span-3 grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-2 text-green-600 mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs font-semibold uppercase tracking-wide">{{ __('Good') }}</span>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $summary['good'] }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('Passing checks') }}</div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-2 text-amber-600 mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs font-semibold uppercase tracking-wide">{{ __('Warnings') }}</span>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $summary['warning'] }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('Need attention') }}</div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-2 text-red-600 mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs font-semibold uppercase tracking-wide">{{ __('Critical') }}</span>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $summary['critical'] }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ __('Fix immediately') }}</div>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @foreach ($categories as $cat)
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500">
                            @switch($cat['icon'])
                                @case('server')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                                    @break
                                @case('folder')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                    @break
                                @case('database')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                                    @break
                                @case('shield')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    @break
                                @case('bolt')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    @break
                                @case('document')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @break
                                @case('list')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                    @break
                                @case('plug')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    @break
                                @default
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/></svg>
                            @endswitch
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ $cat['label'] }}</h3>
                    </div>

                    <div class="flex items-center gap-1.5">
                        @if ($cat['counts']['good'] > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-semibold">{{ $cat['counts']['good'] }}</span>
                        @endif
                        @if ($cat['counts']['warning'] > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-semibold">{{ $cat['counts']['warning'] }}</span>
                        @endif
                        @if ($cat['counts']['critical'] > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-semibold">{{ $cat['counts']['critical'] }}</span>
                        @endif
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach ($cat['items'] as $check)
                        <div class="px-5 py-3 flex items-start gap-3 hover:bg-gray-50/50 transition">
                            <div class="flex-shrink-0 mt-0.5">
                                @if ($check['status'] === 'good')
                                    <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @elseif ($check['status'] === 'warning')
                                    <div class="w-5 h-5 rounded-full bg-amber-100 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/></svg>
                                    </div>
                                @else
                                    <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $check['label'] }}</span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono bg-gray-100 text-gray-600">
                                        {{ is_scalar($check['message']) ? $check['message'] : json_encode($check['message']) }}
                                    </span>
                                </div>
                                @if ($check['recommendation'])
                                    <p class="text-xs text-amber-700 mt-1 flex items-start gap-1">
                                        <span class="flex-shrink-0">→</span>
                                        <span>{{ $check['recommendation'] }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="showModal = false">
        <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col">

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-t-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold">{{ __('AI Health Analysis') }}</h3>
                        <p class="text-xs opacity-90">
                            <span x-text="'Model: ' + aiModel"></span>
                            <span x-show="aiData.score" x-text="' · Score: ' + aiData.score"></span>
                        </p>
                    </div>
                </div>
                <button type="button" @click="showModal = false" class="text-white/80 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-5">

                <div class="text-center py-6" x-show="aiLoading">
                    <svg class="w-12 h-12 mx-auto text-blue-600 animate-spin mb-3" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    <p class="text-sm text-gray-600">{{ __('Analyzing your site health with AI...') }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ __('This may take 10-20 seconds') }}</p>
                </div>

                <div x-show="aiError && !aiLoading" class="bg-red-50 border border-red-100 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-sm text-red-800" x-text="aiError"></div>
                    </div>
                </div>

                <div x-show="!aiLoading && !aiError && aiData.headline" x-cloak class="space-y-5">

                    <div class="rounded-xl p-5 border-2"
                         :class="{
                            'bg-green-50 border-green-200': aiData.overall === 'excellent' || aiData.overall === 'good',
                            'bg-amber-50 border-amber-200': aiData.overall === 'fair',
                            'bg-orange-50 border-orange-200': aiData.overall === 'poor',
                            'bg-red-50 border-red-200': aiData.overall === 'critical'
                         }">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <template x-if="aiData.overall === 'excellent' || aiData.overall === 'good'">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </template>
                                <template x-if="aiData.overall === 'fair'">
                                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </template>
                                <template x-if="aiData.overall === 'poor' || aiData.overall === 'critical'">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-semibold uppercase tracking-wide opacity-70" x-text="aiData.overall"></div>
                                <div class="text-base font-semibold text-gray-900 mt-0.5" x-text="aiData.headline"></div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-700 mt-3 leading-relaxed" x-text="aiData.summary"></p>
                    </div>

                    <template x-if="aiData.priorities && aiData.priorities.length > 0">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                {{ __('Priorities') }}
                            </h4>
                            <div class="space-y-3">
                                <template x-for="(p, idx) in aiData.priorities" :key="idx">
                                    <div class="border rounded-lg p-4"
                                         :class="{
                                            'border-red-200 bg-red-50/50': p.severity === 'critical',
                                            'border-orange-200 bg-orange-50/50': p.severity === 'high',
                                            'border-amber-200 bg-amber-50/50': p.severity === 'medium',
                                            'border-gray-200 bg-gray-50/50': p.severity === 'low'
                                         }">
                                        <div class="flex items-start gap-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase flex-shrink-0"
                                                  :class="{
                                                     'bg-red-500 text-white': p.severity === 'critical',
                                                     'bg-orange-500 text-white': p.severity === 'high',
                                                     'bg-amber-500 text-white': p.severity === 'medium',
                                                     'bg-gray-500 text-white': p.severity === 'low'
                                                  }"
                                                  x-text="p.severity"></span>
                                            <div class="flex-1 min-w-0">
                                                <h5 class="text-sm font-semibold text-gray-900" x-text="p.title"></h5>
                                                <p class="text-xs text-gray-600 mt-1" x-text="p.why"></p>
                                                <div class="mt-2 bg-white border border-gray-200 rounded p-2.5">
                                                    <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-500 mb-1">{{ __('Action') }}</div>
                                                    <pre class="text-xs text-gray-800 whitespace-pre-wrap font-mono leading-relaxed" x-text="p.action"></pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="aiData.quick_wins && aiData.quick_wins.length > 0">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                {{ __('Quick Wins') }}
                            </h4>
                            <ul class="space-y-1.5">
                                <template x-for="(w, idx) in aiData.quick_wins" :key="idx">
                                    <li class="flex items-start gap-2 text-sm text-gray-700">
                                        <span class="text-emerald-500 flex-shrink-0 mt-0.5">✓</span>
                                        <span x-text="w"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>

                    <template x-if="aiData.positives && aiData.positives.length > 0">
                        <details class="group">
                            <summary class="cursor-pointer text-sm font-semibold text-gray-700 flex items-center gap-2 hover:text-gray-900">
                                <svg class="w-4 h-4 transition group-open:rotate-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                <span x-text="'{{ __('What\'s working well') }} (' + aiData.positives.length + ')'"></span>
                            </summary>
                            <ul class="mt-2 ml-6 space-y-1">
                                <template x-for="(pos, idx) in aiData.positives" :key="idx">
                                    <li class="text-xs text-gray-600 flex items-start gap-2">
                                        <span class="text-green-500 flex-shrink-0">•</span>
                                        <span x-text="pos"></span>
                                    </li>
                                </template>
                            </ul>
                        </details>
                    </template>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50 rounded-b-2xl">
                <span class="text-xs text-gray-500" x-show="aiData.model">
                    <span x-text="'{{ __('Powered by') }} ' + (aiData.model || '')"></span>
                </span>
                <button type="button" @click="showModal = false"
                        class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 transition">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>

    <div x-show="toast.show" x-cloak x-transition
         class="fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium z-50"
         :style="'background:' + (toast.type === 'error' ? '#dc2626' : '#059669')"
         x-text="toast.message"></div>

</div>

<script>
function healthTool(initialSummary, aiEnabled, aiModel) {
    return {
        summary: initialSummary,
        aiEnabled: aiEnabled,
        aiModel: aiModel,
        circumference: 2 * Math.PI * 52,

        showModal: false,
        analyzing: false,
        refreshing: false,
        aiData: {},
        aiError: '',
        aiLoading: false,

        toast: { show: false, message: '', type: 'success' },

        get dashOffset() {
            const ratio = (100 - this.summary.score) / 100;
            return this.circumference * ratio;
        },

        get scoreColor() {
            if (this.summary.score >= 90) return '#10b981';
            if (this.summary.score >= 70) return '#f59e0b';
            if (this.summary.score >= 50) return '#f97316';
            return '#ef4444';
        },

        get scoreLabel() {
            if (this.summary.score >= 90) return '{{ __('Excellent') }}';
            if (this.summary.score >= 70) return '{{ __('Good') }}';
            if (this.summary.score >= 50) return '{{ __('Fair') }}';
            return '{{ __('Poor') }}';
        },

        async analyze() {
            if (!this.aiEnabled || this.analyzing) return;

            this.showModal = true;
            this.analyzing = true;
            this.aiLoading = true;
            this.aiError = '';
            this.aiData = {};

            try {
                const res = await fetch('{{ route('admin.tools.health.analyze') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await res.json().catch(() => ({}));

                if (res.ok && data.success) {
                    this.aiData = data.data || {};
                } else {
                    this.aiError = data.message || '{{ __('AI analysis failed.') }}';
                }
            } catch (e) {
                this.aiError = '{{ __('Network error.') }}';
            } finally {
                this.analyzing = false;
                this.aiLoading = false;
            }
        },

        async refresh() {
            this.refreshing = true;

            try {
                await fetch('{{ route('admin.tools.health.refresh') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                window.location.reload();
            } catch (e) {
                this.refreshing = false;
                this.showToast('{{ __('Refresh failed.') }}', 'error');
            }
        },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            setTimeout(() => { this.toast.show = false; }, 3500);
        },
    };
}
</script>
@endsection
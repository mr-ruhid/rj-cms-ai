@extends('admin.layouts.app')

@section('title', __('Site Health'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="healthTool(@js($summary), @js($aiEnabled), @js($aiModel))">

    <div class="flex items-center justify-between gap-4 flex-wrap mb-2">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('Site Health') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Automated checks and AI-powered analysis of your site.') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="refresh()" :disabled="refreshing"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 hover:text-gray-900 shadow-sm transition disabled:opacity-60">
                <svg :class="refreshing ? 'animate-spin' : ''" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                {{ __('Refresh') }}
            </button>

            <button type="button" @click="analyze()" :disabled="!aiEnabled || analyzing"
                    :title="!aiEnabled ? '{{ __('AI is not configured') }}' : ''"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium hover:opacity-90 shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg x-show="!analyzing" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <svg x-show="analyzing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                <span x-text="analyzing ? '{{ __('Analyzing...') }}' : '{{ __('AI Analysis') }}'"></span>
            </button>
        </div>
    </div>

    @if (!$aiEnabled)
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-sm text-blue-900 flex-1 pt-1">
                {{ __('AI analysis is disabled. Configure OpenRouter API key in Settings → AI to unlock detailed analysis and actionable fixes.') }}
                <a href="{{ route('admin.settings.ai') }}" class="ml-1 font-semibold underline hover:text-blue-700 transition">{{ __('Configure now') }} →</a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <div class="lg:col-span-4 bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col items-center justify-center relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-rj-purple/5 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="relative w-40 h-40">
                <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="52" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                    <circle cx="60" cy="60" r="52" fill="none"
                            :stroke="scoreColor" stroke-width="8" stroke-linecap="round"
                            :stroke-dasharray="circumference"
                            :stroke-dashoffset="dashOffset"
                            style="transition: stroke-dashoffset 1.5s cubic-bezier(0.4, 0, 0.2, 1), stroke .3s;"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center flex-col mt-1">
                    <div class="text-4xl font-extrabold tracking-tight" :style="'color:' + scoreColor" x-text="summary.score"></div>
                    <div class="text-[11px] uppercase tracking-wider text-gray-400 font-bold mt-1">{{ __('Score') }}</div>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold" 
                     :class="{
                        'bg-green-100 text-green-700': summary.score >= 90,
                        'bg-amber-100 text-amber-700': summary.score >= 70 && summary.score < 90,
                        'bg-orange-100 text-orange-700': summary.score >= 50 && summary.score < 70,
                        'bg-red-100 text-red-700': summary.score < 50
                     }"
                     x-text="scoreLabel">
                </div>
                <div class="text-xs text-gray-500 mt-2 font-medium">{{ $summary['total'] }} {{ __('checks performed') }}</div>
            </div>
        </div>

        <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <div class="bg-gradient-to-b from-green-50/80 to-white rounded-2xl border border-green-100 p-5 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition">
                <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition duration-300">
                    <svg class="w-24 h-24 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="text-sm font-bold text-green-800 mb-1 uppercase tracking-wide">{{ __('Good') }}</div>
                    <div class="text-4xl font-extrabold text-gray-900">{{ $summary['good'] }}</div>
                    <div class="text-xs text-gray-500 mt-1 font-medium">{{ __('Passing checks') }}</div>
                </div>
            </div>

            <div class="bg-gradient-to-b from-amber-50/80 to-white rounded-2xl border border-amber-100 p-5 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition">
                <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition duration-300">
                    <svg class="w-24 h-24 text-amber-600" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="text-sm font-bold text-amber-800 mb-1 uppercase tracking-wide">{{ __('Warnings') }}</div>
                    <div class="text-4xl font-extrabold text-gray-900">{{ $summary['warning'] }}</div>
                    <div class="text-xs text-gray-500 mt-1 font-medium">{{ __('Need attention') }}</div>
                </div>
            </div>

            <div class="bg-gradient-to-b from-red-50/80 to-white rounded-2xl border border-red-100 p-5 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition">
                <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition duration-300">
                    <svg class="w-24 h-24 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="text-sm font-bold text-red-800 mb-1 uppercase tracking-wide">{{ __('Critical') }}</div>
                    <div class="text-4xl font-extrabold text-gray-900">{{ $summary['critical'] }}</div>
                    <div class="text-xs text-gray-500 mt-1 font-medium">{{ __('Fix immediately') }}</div>
                </div>
            </div>

        </div>
    </div>

    <div class="space-y-5 mt-8">
        @foreach ($categories as $cat)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-600">
                            @switch($cat['icon'])
                                @case('server')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                                    @break
                                @case('folder')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                    @break
                                @case('database')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                                    @break
                                @case('shield')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    @break
                                @case('bolt')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    @break
                                @case('document')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @break
                                @case('list')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                    @break
                                @case('plug')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    @break
                                @default
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/></svg>
                            @endswitch
                        </div>
                        <h3 class="text-sm font-bold text-gray-800">{{ $cat['label'] }}</h3>
                    </div>

                    <div class="flex items-center gap-1.5">
                        @if ($cat['counts']['good'] > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-green-100 text-green-700 text-xs font-bold">{{ $cat['counts']['good'] }}</span>
                        @endif
                        @if ($cat['counts']['warning'] > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-100 text-amber-700 text-xs font-bold">{{ $cat['counts']['warning'] }}</span>
                        @endif
                        @if ($cat['counts']['critical'] > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-red-100 text-red-700 text-xs font-bold">{{ $cat['counts']['critical'] }}</span>
                        @endif
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach ($cat['items'] as $check)
                        <div class="px-6 py-4 flex items-start gap-4 hover:bg-gray-50/80 transition-colors group">
                            
                            <div class="flex-shrink-0 mt-0.5">
                                @if ($check['status'] === 'good')
                                    <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @elseif ($check['status'] === 'warning')
                                    <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/></svg>
                                    </div>
                                @else
                                    <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-semibold text-gray-900 group-hover:text-rj-purple transition">{{ $check['label'] }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono bg-gray-100 text-gray-600 border border-gray-200">
                                            {{ is_scalar($check['message']) ? $check['message'] : json_encode($check['message']) }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if ($check['recommendation'])
                                    <div class="mt-1.5 bg-amber-50 border border-amber-100 rounded-lg p-2 inline-flex items-start gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-xs text-amber-800 font-medium leading-relaxed">{{ $check['recommendation'] }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- AI MODAL --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="showModal = false">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">{{ __('AI Health Analysis') }}</h3>
                        <p class="text-xs text-blue-100 mt-0.5">
                            <span x-text="'Model: ' + aiModel"></span>
                            <span x-show="aiData.score" x-text="' · Score: ' + aiData.score"></span>
                        </p>
                    </div>
                </div>
                <button type="button" @click="showModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">

                <div class="text-center py-10" x-show="aiLoading">
                    <div class="w-16 h-16 mx-auto rounded-full bg-blue-50 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ __('Analyzing your site...') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('OpenRouter is reviewing your health checks to find priorities.') }}</p>
                </div>

                <div x-show="aiError && !aiLoading" class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-sm text-red-800 font-medium" x-text="aiError"></div>
                    </div>
                </div>

                <div x-show="!aiLoading && !aiError && aiData.headline" x-cloak class="space-y-6">

                    <div class="rounded-2xl p-6 border shadow-sm relative overflow-hidden"
                         :class="{
                            'bg-gradient-to-br from-green-50 to-emerald-50 border-green-200': aiData.overall === 'excellent' || aiData.overall === 'good',
                            'bg-gradient-to-br from-amber-50 to-orange-50 border-amber-200': aiData.overall === 'fair',
                            'bg-gradient-to-br from-red-50 to-rose-50 border-red-200': aiData.overall === 'poor' || aiData.overall === 'critical'
                         }">
                        <div class="flex items-start gap-4 relative z-10">
                            <div class="flex-shrink-0">
                                <template x-if="aiData.overall === 'excellent' || aiData.overall === 'good'">
                                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-sm"><svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                                </template>
                                <template x-if="aiData.overall === 'fair'">
                                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-sm"><svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                                </template>
                                <template x-if="aiData.overall === 'poor' || aiData.overall === 'critical'">
                                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-sm"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0 pt-1">
                                <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-white/60 mb-2 border border-black/5" x-text="aiData.overall"></div>
                                <h3 class="text-lg font-bold text-gray-900 leading-tight" x-text="aiData.headline"></h3>
                                <p class="text-sm text-gray-700 mt-2 leading-relaxed" x-text="aiData.summary"></p>
                            </div>
                        </div>
                    </div>

                    <template x-if="aiData.priorities && aiData.priorities.length > 0">
                        <div>
                            <h4 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                {{ __('Action Plan') }}
                            </h4>
                            <div class="space-y-4">
                                <template x-for="(p, idx) in aiData.priorities" :key="idx">
                                    <div class="bg-white border rounded-xl p-5 shadow-sm hover:shadow-md transition"
                                         :class="{
                                            'border-red-200': p.severity === 'critical',
                                            'border-orange-200': p.severity === 'high',
                                            'border-amber-200': p.severity === 'medium',
                                            'border-gray-200': p.severity === 'low'
                                         }">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-shrink-0 mt-1">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-bold text-white shadow-sm"
                                                      :class="{
                                                         'bg-red-500': p.severity === 'critical',
                                                         'bg-orange-500': p.severity === 'high',
                                                         'bg-amber-500': p.severity === 'medium',
                                                         'bg-gray-400': p.severity === 'low'
                                                      }"
                                                      x-text="idx + 1"></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h5 class="text-sm font-bold text-gray-900" x-text="p.title"></h5>
                                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-gray-100 text-gray-600" x-text="p.category"></span>
                                                </div>
                                                <p class="text-xs text-gray-600 mb-3" x-text="p.why"></p>
                                                
                                                <div class="bg-gray-900 rounded-lg p-3 relative group">
                                                    <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 flex items-center justify-between">
                                                        <span>{{ __('Action to take') }}</span>
                                                        <svg class="w-3.5 h-3.5 text-gray-500 opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    </div>
                                                    <pre class="text-xs text-gray-200 whitespace-pre-wrap font-mono leading-relaxed overflow-x-auto" x-text="p.action"></pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="aiData.quick_wins && aiData.quick_wins.length > 0">
                        <div class="bg-emerald-50/50 rounded-xl p-5 border border-emerald-100">
                            <h4 class="text-sm font-bold text-emerald-900 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                {{ __('Quick Wins') }}
                            </h4>
                            <ul class="space-y-2">
                                <template x-for="(w, idx) in aiData.quick_wins" :key="idx">
                                    <li class="flex items-start gap-2.5 text-sm text-emerald-800">
                                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span x-text="w"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>

                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                <span class="text-xs text-gray-500 font-medium" x-show="aiData.model">
                    <span x-text="'{{ __('Powered by') }} ' + (aiData.model || '')"></span>
                </span>
                <button type="button" @click="showModal = false"
                        class="px-6 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 transition shadow-sm">
                    {{ __('Close Analysis') }}
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

<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
@endsection
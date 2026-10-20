@extends('admin.layouts.app')

@section('title', __('Backup Manager'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12"
     x-data="backupTool(@js($settings), {{ count($localBackups) }}, {{ count($downloadBackups) }})"
     @keydown.escape.window="dialog.show = false">

    {{-- ============ HEADER ============ --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rj-purple to-rj-pink p-6 sm:p-8 shadow-lg">
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -left-10 -bottom-20 w-56 h-56 rounded-full bg-black/10 blur-2xl pointer-events-none"></div>

        <div class="relative flex items-center justify-between gap-6 flex-wrap">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 ring-1 ring-white/30 backdrop-blur flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">{{ __('Backup Manager') }}</h2>
                    <p class="text-sm text-white/80 mt-1">{{ __('Create, download, and restore your site data.') }}</p>
                </div>
            </div>

            <button type="button" @click="createBackup('local')" :disabled="creating"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-gray-900 text-sm font-semibold hover:bg-gray-50 active:scale-[.98] transition shadow-md disabled:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-rj-purple">
                <svg x-show="!creating" class="w-4 h-4 text-rj-purple" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <svg x-show="creating" x-cloak class="w-4 h-4 animate-spin text-rj-purple" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                <span x-text="creating ? @js(__('Creating...')) : @js(__('Create Backup Now'))"></span>
            </button>
        </div>

        {{-- Quick stats --}}
        <div class="relative grid grid-cols-1 sm:grid-cols-3 gap-3 mt-6">
            <div class="rounded-xl bg-white/15 ring-1 ring-white/20 backdrop-blur px-4 py-3">
                <div class="text-xs text-white/70">{{ __('Local Backups (Server)') }}</div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-white tabular-nums" x-text="counts.local"></span>
                    <span class="text-sm text-white/70">/ <span class="tabular-nums" x-text="settings.local_limit"></span></span>
                </div>
            </div>
            <div class="rounded-xl bg-white/15 ring-1 ring-white/20 backdrop-blur px-4 py-3">
                <div class="text-xs text-white/70">{{ __('Downloadable Backups') }}</div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-white tabular-nums" x-text="counts.download"></span>
                    <span class="text-sm text-white/70">/ <span class="tabular-nums" x-text="settings.download_limit"></span></span>
                </div>
            </div>
            <div class="rounded-xl bg-white/15 ring-1 ring-white/20 backdrop-blur px-4 py-3">
                <div class="text-xs text-white/70">{{ __('Backup Mode') }}</div>
                <div class="mt-1 flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span x-show="settings.mode === 'auto'" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5" :class="settings.mode === 'auto' ? 'bg-emerald-300' : 'bg-white/50'"></span>
                    </span>
                    <span class="text-lg font-bold text-white"
                          x-text="settings.mode === 'auto' ? @js(__('Automatic')) : @js(__('Manual'))"></span>
                    <span x-show="settings.mode === 'auto'" x-cloak class="text-sm text-white/70"
                          x-text="'· ' + ({daily: @js(__('Daily')), weekly: @js(__('Weekly')), monthly: @js(__('Monthly'))}[settings.schedule] || '')"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ SETTINGS ============ --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">{{ __('Backup Settings') }}</h3>
        </div>

        <form @submit.prevent="saveSettings()" class="p-6 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- Mode --}}
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-3">{{ __('Backup Mode') }}</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" x-model="settings.mode" value="manual" class="peer sr-only">
                                <div class="flex items-center gap-3 rounded-xl border-2 border-gray-200 px-4 py-3 transition hover:border-gray-300
                                            peer-checked:border-rj-purple peer-checked:bg-rj-purple/5 peer-focus-visible:ring-2 peer-focus-visible:ring-rj-purple/40">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition"
                                         :class="settings.mode === 'manual' ? 'bg-rj-purple text-white' : 'bg-gray-100 text-gray-500'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"/></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ __('Manual') }}</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" x-model="settings.mode" value="auto" class="peer sr-only">
                                <div class="flex items-center gap-3 rounded-xl border-2 border-gray-200 px-4 py-3 transition hover:border-gray-300
                                            peer-checked:border-rj-purple peer-checked:bg-rj-purple/5 peer-focus-visible:ring-2 peer-focus-visible:ring-rj-purple/40">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center transition"
                                         :class="settings.mode === 'auto' ? 'bg-rj-purple text-white' : 'bg-gray-100 text-gray-500'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ __('Automatic') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div x-show="settings.mode === 'auto'" x-collapse x-cloak>
                        <label class="block text-sm font-semibold text-gray-900 mb-3">{{ __('Auto Backup Schedule') }}</label>
                        <div class="inline-flex p-1 rounded-xl bg-gray-100 w-full sm:w-auto">
                            @foreach (['daily' => __('Daily'), 'weekly' => __('Weekly'), 'monthly' => __('Monthly')] as $value => $label)
                                <label class="flex-1 sm:flex-none cursor-pointer">
                                    <input type="radio" x-model="settings.schedule" value="{{ $value }}" class="peer sr-only">
                                    <span class="block text-center px-5 py-1.5 rounded-lg text-sm font-medium text-gray-600 transition
                                                 peer-checked:bg-white peer-checked:text-gray-900 peer-checked:shadow-sm peer-focus-visible:ring-2 peer-focus-visible:ring-rj-purple/40">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-3 flex items-start gap-2 rounded-lg bg-amber-50 border border-amber-100 px-3 py-2">
                            <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-amber-800">{{ __('Requires system cron job to be configured.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Retention --}}
                <div class="space-y-6">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-semibold text-gray-900">{{ __('Local Backups Retention') }}</label>
                            <div class="flex items-center gap-1.5">
                                <input type="number" x-model.number="settings.local_limit" min="1" max="20"
                                       class="w-16 px-2 py-1 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm text-center font-semibold tabular-nums">
                                <span class="text-xs text-gray-500">{{ __('files') }}</span>
                            </div>
                        </div>
                        <input type="range" x-model.number="settings.local_limit" min="1" max="20" class="w-full h-2 rounded-full cursor-pointer accent-rj-purple">
                        <div class="flex justify-between text-[11px] text-gray-400 mt-1 tabular-nums"><span>1</span><span>20</span></div>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Keep the last N backups on the server. (Default: 5, Max: 20)') }}</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-semibold text-gray-900">{{ __('Download Backups Retention') }}</label>
                            <div class="flex items-center gap-1.5">
                                <input type="number" x-model.number="settings.download_limit" min="1" max="3"
                                       class="w-16 px-2 py-1 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm text-center font-semibold tabular-nums">
                                <span class="text-xs text-gray-500">{{ __('files') }}</span>
                            </div>
                        </div>
                        <input type="range" x-model.number="settings.download_limit" min="1" max="3" class="w-full h-2 rounded-full cursor-pointer accent-rj-purple">
                        <div class="flex justify-between text-[11px] text-gray-400 mt-1 tabular-nums"><span>1</span><span>3</span></div>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Backups saved for downloading. (Default: 2, Max: 3)') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-5 border-t border-gray-100">
                <button type="submit" :disabled="saving"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 active:scale-[.98] transition disabled:opacity-60 shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2">
                    <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="saving" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    <span x-text="saving ? @js(__('Saving...')) : @js(__('Save Settings'))"></span>
                </button>
            </div>
        </form>
    </div>

    {{-- ============ LOCAL BACKUPS ============ --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">{{ __('Local Backups (Server)') }}</h3>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-xs font-semibold text-gray-600 tabular-nums">
                    {{ count($localBackups) }} / <span x-text="settings.local_limit" class="ml-0.5"></span>&nbsp;{{ __('saved') }}
                </span>
            </div>
            <div class="mt-3 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-rj-purple transition-all duration-500"
                     :style="'width:' + Math.min(100, (counts.local / Math.max(1, settings.local_limit)) * 100) + '%'"></div>
            </div>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse ($localBackups as $backup)
                <div class="group px-6 py-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4 hover:bg-gray-50/70 transition">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold text-gray-900 truncate max-w-[16rem] sm:max-w-md" title="{{ $backup['filename'] }}">{{ $backup['filename'] }}</span>
                                @if ($loop->first)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold ring-1 ring-emerald-100">{{ __('Latest') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $backup['created_at'] }}
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    {{ $backup['size_human'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap lg:flex-nowrap lg:flex-shrink-0">
                        <form method="POST" action="{{ route('admin.tools.backup.restore', $backup['filename']) }}"
                              @submit.prevent="confirmForm($el, {
                                  title: @js(__('Restore this backup?')),
                                  message: @js(__('WARNING: This will overwrite your current database and files. Are you sure?')),
                                  button: @js(__('Restore')),
                                  tone: 'warning'
                              })">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-amber-50 text-amber-700 ring-1 ring-amber-100 text-xs font-semibold hover:bg-amber-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                {{ __('Restore') }}
                            </button>
                        </form>

                        <a href="{{ route('admin.tools.backup.download', ['filename' => $backup['filename'], 'type' => 'local']) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-gray-100 text-gray-700 ring-1 ring-gray-200 text-xs font-semibold hover:bg-gray-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            {{ __('Download') }}
                        </a>

                        <form method="POST" action="{{ route('admin.tools.backup.delete', $backup['filename']) }}"
                              @submit.prevent="confirmForm($el, {
                                  title: @js(__('Delete backup?')),
                                  message: @js(__('Delete this backup permanently?')),
                                  button: @js(__('Delete')),
                                  tone: 'danger'
                              })">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="type" value="local">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-red-50 text-red-600 ring-1 ring-red-100 text-xs font-semibold hover:bg-red-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-14 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-50 ring-1 ring-gray-100 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700">{{ __('No local backups found.') }}</p>
                    <button type="button" @click="createBackup('local')" :disabled="creating"
                            class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-rj-purple/10 text-rj-purple text-sm font-semibold hover:bg-rj-purple/20 transition disabled:opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Create Backup Now') }}
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ============ DOWNLOADABLE BACKUPS ============ --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">{{ __('Downloadable Backups') }}</h3>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-xs font-semibold text-gray-600 tabular-nums">
                    {{ count($downloadBackups) }} / <span x-text="settings.download_limit" class="ml-0.5"></span>&nbsp;{{ __('saved') }}
                </span>
            </div>
            <div class="mt-3 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500"
                     :style="'width:' + Math.min(100, (counts.download / Math.max(1, settings.download_limit)) * 100) + '%'"></div>
            </div>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse ($downloadBackups as $backup)
                <div class="px-6 py-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4 hover:bg-gray-50/70 transition">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold text-gray-900 truncate max-w-[16rem] sm:max-w-md" title="{{ $backup['filename'] }}">{{ $backup['filename'] }}</span>
                                @if ($loop->first)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold ring-1 ring-emerald-100">{{ __('Latest') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $backup['created_at'] }}
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    {{ $backup['size_human'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap lg:flex-nowrap lg:flex-shrink-0">
                        <a href="{{ route('admin.tools.backup.download', ['filename' => $backup['filename'], 'type' => 'download']) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-semibold hover:opacity-90 active:scale-[.98] transition shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            {{ __('Download to PC') }}
                        </a>

                        <form method="POST" action="{{ route('admin.tools.backup.delete', $backup['filename']) }}"
                              @submit.prevent="confirmForm($el, {
                                  title: @js(__('Delete backup?')),
                                  message: @js(__('Delete this backup permanently?')),
                                  button: @js(__('Delete')),
                                  tone: 'danger'
                              })">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="type" value="download">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-red-50 text-red-600 ring-1 ring-red-100 text-xs font-semibold hover:bg-red-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-14 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-50 ring-1 ring-gray-100 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700">{{ __('No downloadable backups found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ============ CONFIRM DIALOG ============ --}}
    <div x-show="dialog.show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div x-show="dialog.show" x-transition.opacity @click="dialog.show = false"
             class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

        <div x-show="dialog.show"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0"
                     :class="{
                        'bg-red-100 text-red-600': dialog.tone === 'danger',
                        'bg-amber-100 text-amber-600': dialog.tone === 'warning',
                        'bg-rj-purple/10 text-rj-purple': dialog.tone === 'brand'
                     }">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="min-w-0">
                    <h4 class="text-base font-bold text-gray-900" x-text="dialog.title"></h4>
                    <p class="text-sm text-gray-600 mt-1.5" x-text="dialog.message"></p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-6">
                <button type="button" @click="dialog.show = false"
                        class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 transition">
                    {{ __('Cancel') }}
                </button>
                <button type="button" @click="runDialog()"
                        class="px-4 py-2 rounded-lg text-white text-sm font-semibold transition shadow-sm"
                        :class="{
                            'bg-red-600 hover:bg-red-700': dialog.tone === 'danger',
                            'bg-amber-500 hover:bg-amber-600': dialog.tone === 'warning',
                            'bg-gradient-to-r from-rj-purple to-rj-pink hover:opacity-90': dialog.tone === 'brand'
                        }"
                        x-text="dialog.button"></button>
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
         class="fixed bottom-6 right-6 z-[60] flex items-center gap-3 pl-4 pr-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium max-w-sm"
         :class="toast.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'">
        <svg x-show="toast.type !== 'error'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <svg x-show="toast.type === 'error'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span x-text="toast.message"></span>
    </div>
</div>

<script>
function backupTool(initialSettings, localCount, downloadCount) {
    return {
        settings: initialSettings,
        counts: { local: localCount, download: downloadCount },
        creating: false,
        saving: false,
        toast: { show: false, message: '', type: 'success' },
        dialog: { show: false, title: '', message: '', button: '', tone: 'danger', onConfirm: null },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            setTimeout(() => { this.toast.show = false; }, 3500);
        },

        ask(opts) {
            this.dialog = {
                show: true,
                title: opts.title,
                message: opts.message,
                button: opts.button,
                tone: opts.tone || 'danger',
                onConfirm: opts.onConfirm,
            };
        },

        runDialog() {
            const fn = this.dialog.onConfirm;
            this.dialog.show = false;
            if (typeof fn === 'function') fn();
        },

        confirmForm(form, opts) {
            this.ask({ ...opts, onConfirm: () => form.submit() });
        },

        async saveSettings() {
            this.saving = true;
            try {
                const res = await fetch('{{ route('admin.tools.backup.settings') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.settings),
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    this.showToast(data.message || @js(__('Settings saved.')), 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.showToast(data.message || @js(__('Save failed.')), 'error');
                }
            } catch (e) {
                this.showToast(@js(__('Network error.')), 'error');
            } finally {
                this.saving = false;
            }
        },

        createBackup(type) {
            this.ask({
                title: @js(__('Create Backup Now')),
                message: @js(__('Create a new backup now? This may take a few moments.')),
                button: @js(__('Create Backup Now')),
                tone: 'brand',
                onConfirm: () => this.runCreate(type),
            });
        },

        async runCreate(type) {
            this.creating = true;
            try {
                const res = await fetch('{{ route('admin.tools.backup.create') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ type: type }),
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    this.showToast(data.message || @js(__('Backup created.')), 'success');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    this.showToast(data.message || @js(__('Creation failed.')), 'error');
                }
            } catch (e) {
                this.showToast(@js(__('Network error.')), 'error');
            } finally {
                this.creating = false;
            }
        }
    };
}
</script>
@endsection
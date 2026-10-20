@extends('admin.settings.layout')

@section('title', __('Notifications'))

@section('settings')
@php
    $events = [
        'new_comment' => [
            'title' => __('New Comment'),
            'desc'  => __('Get an email when someone leaves a comment on your site.'),
            'icon'  => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
            'tone'  => 'bg-sky-50 text-sky-600 ring-sky-100',
        ],
        'new_user' => [
            'title' => __('New User Registration'),
            'desc'  => __('Get an email when a new user registers on your site.'),
            'icon'  => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
            'tone'  => 'bg-violet-50 text-violet-600 ring-violet-100',
        ],
        'post_published' => [
            'title' => __('Post Published'),
            'desc'  => __('Get an email when a new post or page is published.'),
            'icon'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'tone'  => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
        ],
        'update_available' => [
            'title' => __('Update Available'),
            'desc'  => __('Get an email when RJ CMS, themes, or plugins have updates available.'),
            'icon'  => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            'tone'  => 'bg-amber-50 text-amber-600 ring-amber-100',
        ],
        'failed_login' => [
            'title' => __('Failed Login Attempt'),
            'desc'  => __('Get an email when someone fails to log in to the admin panel.'),
            'icon'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'tone'  => 'bg-red-50 text-red-600 ring-red-100',
        ],
        'form_submission' => [
            'title' => __('Form Submission'),
            'desc'  => __('Get an email when a user submits a form on your site.'),
            'icon'  => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
            'tone'  => 'bg-indigo-50 text-indigo-600 ring-indigo-100',
        ],
    ];

    $initial = [];
    foreach ($events as $key => $meta) {
        $initial[$key] = (bool) old('events.' . $key, $settings['events'][$key] ?? false);
    }
@endphp

<div class="space-y-6" x-data="notificationsForm(@js($initial))">

    {{-- HEADER --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 tracking-tight">{{ __('Notifications') }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('Configure email alerts for various events on your site.') }}</p>
    </div>

    {{-- SUCCESS --}}
    @if (session('success'))
        <div x-data="{ open: true }" x-init="setTimeout(() => open = false, 5000)"
             x-show="open" x-transition.opacity.duration.300ms role="status"
             class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium flex-1">{{ session('success') }}</p>
            <button type="button" @click="open = false" class="text-emerald-500 hover:text-emerald-700 transition" aria-label="{{ __('Close') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- ERRORS --}}
    @if ($errors->any())
        <div role="alert" class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.notifications.update') }}" class="space-y-6" @submit="saving = true">
        @csrf
        @method('PUT')

        {{-- RECIPIENT ADDRESS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-rj-purple/10 text-rj-purple flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900">{{ __('Recipient Address') }}</h3>
            </div>

            <div class="p-6">
                <div class="max-w-md">
                    <label for="admin_email" class="block text-sm font-semibold text-gray-900 mb-2">{{ __('Admin Email Address') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </span>
                        <input id="admin_email" type="email" name="admin_email" required autocomplete="email"
                               value="{{ old('admin_email', $settings['admin_email'] ?? '') }}"
                               placeholder="admin@example.com"
                               class="w-full pl-9 pr-3 py-2.5 rounded-lg border text-sm transition focus:ring-rj-purple focus:border-rj-purple @error('admin_email') border-red-400 bg-red-50/40 @else border-gray-300 @enderror">
                    </div>
                    @error('admin_email')
                        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                    @else
                        <p class="text-xs text-gray-500 mt-2">{{ __('Where should system notifications be sent?') }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- EVENTS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-rj-pink/10 text-rj-pink flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">{{ __('Email Notifications') }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ __('Toggle the events you want to be notified about.') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-xs font-semibold text-gray-600 tabular-nums">
                            <span x-text="enabledCount"></span>&nbsp;/ {{ count($events) }}
                        </span>
                        <div class="inline-flex rounded-lg bg-gray-100 p-0.5 text-xs font-medium">
                            <button type="button" @click="setAll(true)"
                                    class="px-3 py-1.5 rounded-md text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm transition">{{ __('Enable all') }}</button>
                            <button type="button" @click="setAll(false)"
                                    class="px-3 py-1.5 rounded-md text-gray-600 hover:bg-white hover:text-gray-900 hover:shadow-sm transition">{{ __('Disable all') }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach ($events as $key => $event)
                    <label for="event_{{ $key }}" class="flex items-center justify-between gap-4 px-6 py-5 cursor-pointer hover:bg-gray-50/70 transition">
                        <div class="flex items-start gap-4 min-w-0">
                            <div class="w-10 h-10 rounded-xl ring-1 flex items-center justify-center flex-shrink-0 transition {{ $event['tone'] }}"
                                 :class="events.{{ $key }} ? '' : 'opacity-50 grayscale'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $event['icon'] }}"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-gray-900">{{ $event['title'] }}</div>
                                <div class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $event['desc'] }}</div>
                            </div>
                        </div>

                        <div class="relative flex-shrink-0">
                            <input type="hidden" name="events[{{ $key }}]" value="0">
                            <input id="event_{{ $key }}" type="checkbox" name="events[{{ $key }}]" value="1"
                                   x-model="events.{{ $key }}" role="switch" class="peer sr-only">
                            <span class="block w-11 h-6 rounded-full transition-colors duration-200 peer-focus-visible:ring-4 peer-focus-visible:ring-rj-purple/30"
                                  :class="events.{{ $key }} ? 'bg-rj-purple' : 'bg-gray-300'"></span>
                            <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow pointer-events-none"
                                  style="transition: transform .2s ease;"
                                  :style="'transform: translateX(' + (events.{{ $key }} ? '20px' : '0px') + ')'"></span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex items-center justify-end">
            <button type="submit" :disabled="saving"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-semibold hover:opacity-90 active:scale-[.98] transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-rj-purple focus-visible:ring-offset-2">
                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="saving" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                <span x-text="saving ? @js(__('Saving...')) : @js(__('Save Changes'))"></span>
            </button>
        </div>
    </form>
</div>

<script>
function notificationsForm(initialEvents) {
    return {
        events: initialEvents,
        saving: false,

        get enabledCount() {
            return Object.values(this.events).filter(Boolean).length;
        },

        setAll(value) {
            Object.keys(this.events).forEach(key => { this.events[key] = value; });
        }
    };
}
</script>
@endsection
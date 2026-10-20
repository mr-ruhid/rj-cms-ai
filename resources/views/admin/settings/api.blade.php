@extends('admin.settings.layout')

@section('title', __('API & Integrations'))

@section('settings')
@php
    $methodStyles = [
        'GET'    => 'bg-sky-50 text-sky-700 ring-sky-200',
        'POST'   => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'PUT'    => 'bg-amber-50 text-amber-700 ring-amber-200',
        'PATCH'  => 'bg-violet-50 text-violet-700 ring-violet-200',
        'DELETE' => 'bg-red-50 text-red-700 ring-red-200',
    ];
    $endpointList = collect($availableEndpoints ?? []);
@endphp

<div class="space-y-6" x-data="apiSettings()" @keydown.escape.window="revoke.show = false">

    {{-- HEADER --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 tracking-tight">{{ __('API & Integrations') }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('Manage API keys to allow external applications to access your site data.') }}</p>
    </div>

    {{-- SUCCESS --}}
    @if (session('success'))
        <div x-data="{ open: true }" x-init="setTimeout(() => open = false, 5000)"
             x-show="open" x-transition.opacity.duration.300ms role="status"
             class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium flex-1">{{ session('success') }}</p>
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

    {{-- ============ API KEYS ============ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">{{ __('Active API Keys') }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('Use these keys to authenticate your external requests.') }}</p>
                </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-xs font-semibold text-gray-600 tabular-nums" x-text="keys.length"></span>
        </div>

        <div class="p-6 space-y-6">

            {{-- Generate form --}}
            <form @submit.prevent="generateKey()" class="rounded-xl bg-gray-50 border border-gray-200 p-4">
                <label for="key_name" class="block text-sm font-semibold text-gray-900 mb-2">{{ __('Application Name') }}</label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 w-10 flex items-center justify-center text-gray-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </span>
                        <input id="key_name" type="text" x-model="newKeyName" required maxlength="60"
                               placeholder="{{ __('e.g. Mobile App, Zapier') }}"
                               class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-300 bg-white focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                    </div>
                    <button type="submit" :disabled="generating || !newKeyName.trim()"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-semibold hover:opacity-90 active:scale-[.98] transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-rj-purple focus-visible:ring-offset-2">
                        <svg x-show="!generating" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <svg x-show="generating" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                        <span x-text="generating ? @js(__('Generating...')) : @js(__('Generate Key'))"></span>
                    </button>
                </div>
            </form>

            {{-- Empty state --}}
            <div x-show="keys.length === 0" x-cloak class="text-center py-12 border-2 border-dashed border-gray-200 rounded-xl">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 ring-1 ring-gray-100 mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-900">{{ __('No API keys generated yet.') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ __('Create one above to start using the API.') }}</p>
            </div>

            {{-- Keys list --}}
            <div class="space-y-4" x-show="keys.length > 0" x-cloak>
                <template x-for="key in keys" :key="key.id">
                    <div class="border border-gray-200 rounded-xl p-5 hover:border-indigo-200 hover:shadow-sm transition bg-white">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-rj-purple text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                                    <span x-text="(key.name || '?').substring(0, 1).toUpperCase()"></span>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-gray-900 truncate" x-text="key.name"></h4>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ __('Created') }}: <span x-text="key.created_at"></span>
                                    </p>
                                </div>
                            </div>

                            <button type="button" @click="askRevoke(key)"
                                    class="inline-flex items-center gap-1.5 text-red-600 text-xs font-semibold px-3 py-2 rounded-lg bg-red-50 ring-1 ring-red-100 hover:bg-red-100 transition flex-shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                {{ __('Revoke') }}
                            </button>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Public --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">{{ __('Public Key') }}</label>
                                <div class="flex items-center gap-2 bg-gray-50 rounded-lg pl-3 pr-1.5 py-1.5 border border-gray-200">
                                    <code class="text-xs text-gray-800 font-mono flex-1 truncate" x-text="key.public_key"></code>
                                    <button type="button" @click="copyText(key.public_key, key.id + ':public')"
                                            class="p-1.5 rounded-md text-gray-400 hover:text-gray-700 hover:bg-white transition" title="{{ __('Copy') }}" aria-label="{{ __('Copy') }}">
                                        <svg x-show="copied !== key.id + ':public'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <svg x-show="copied === key.id + ':public'" x-cloak class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Private --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">{{ __('Private Key') }}</label>
                                <div class="flex items-center gap-1 bg-gray-50 rounded-lg pl-3 pr-1.5 py-1.5 border border-gray-200">
                                    <code class="text-xs text-gray-800 font-mono flex-1 truncate"
                                          x-text="showPrivate[key.id] ? key.private_key : '••••••••••••••••••••••••'"></code>
                                    <button type="button" @click="showPrivate[key.id] = !showPrivate[key.id]"
                                            class="p-1.5 rounded-md text-gray-400 hover:text-gray-700 hover:bg-white transition" title="{{ __('Toggle visibility') }}" aria-label="{{ __('Toggle visibility') }}">
                                        <svg x-show="!showPrivate[key.id]" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showPrivate[key.id]" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                    <button type="button" @click="copyText(key.private_key, key.id + ':private')"
                                            class="p-1.5 rounded-md text-gray-400 hover:text-gray-700 hover:bg-white transition" title="{{ __('Copy') }}" aria-label="{{ __('Copy') }}">
                                        <svg x-show="copied !== key.id + ':private'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <svg x-show="copied === key.id + ':private'" x-cloak class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ============ DOCUMENTATION ============ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">{{ __('API Documentation') }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('Available endpoints for external integrations.') }}</p>
                </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-xs font-semibold text-gray-600 tabular-nums">{{ $endpointList->count() }}</span>
        </div>

        <div class="p-6 space-y-6">

            {{-- Auth example --}}
            <div class="rounded-xl bg-gray-900 overflow-hidden ring-1 ring-gray-800">
                <div class="flex items-center justify-between px-4 py-2.5 bg-gray-800/70 border-b border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="flex gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-400/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400/80"></span>
                        </div>
                        <span class="text-xs font-medium text-gray-400">{{ __('Authentication') }}</span>
                    </div>
                    <button type="button" @click="copyText($refs.curl.innerText, 'curl')"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-white px-2 py-1 rounded-md hover:bg-white/10 transition">
                        <svg x-show="copied !== 'curl'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <svg x-show="copied === 'curl'" x-cloak class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="copied === 'curl' ? @js(__('Copied')) : @js(__('Copy'))"></span>
                    </button>
                </div>
                <div class="p-4 overflow-x-auto">
<pre x-ref="curl" class="text-xs text-emerald-300 font-mono leading-relaxed">curl -X GET "{{ url('/api/v1/posts') }}" \
  -H "X-Public-Key: YOUR_PUBLIC_KEY" \
  -H "X-Private-Key: YOUR_PRIVATE_KEY" \
  -H "Accept: application/json"</pre>
                </div>
            </div>

            {{-- Endpoints --}}
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-sm min-w-[32rem]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left font-semibold text-gray-600 px-4 py-3 w-28">{{ __('Method') }}</th>
                            <th class="text-left font-semibold text-gray-600 px-4 py-3">{{ __('Endpoint') }}</th>
                            <th class="text-left font-semibold text-gray-600 px-4 py-3">{{ __('Description') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($endpointList as $endpoint)
                            @php $method = strtoupper($endpoint['method'] ?? 'GET'); @endphp
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-[11px] font-bold ring-1 {{ $methodStyles[$method] ?? 'bg-gray-50 text-gray-700 ring-gray-200' }}">
                                        {{ $method }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-800 whitespace-nowrap">{{ $endpoint['uri'] ?? '' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $endpoint['desc'] ?? '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('No endpoints available.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============ REVOKE DIALOG ============ --}}
    <div x-show="revoke.show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div x-show="revoke.show" x-transition.opacity @click="!revoke.loading && (revoke.show = false)"
             class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

        <div x-show="revoke.show"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="min-w-0">
                    <h4 class="text-base font-bold text-gray-900">{{ __('Revoke API key?') }}</h4>
                    <p class="text-sm font-medium text-gray-700 mt-1 truncate" x-text="revoke.name"></p>
                    <p class="text-sm text-gray-600 mt-1.5">{{ __('Are you sure you want to revoke this API key? External applications using it will lose access immediately.') }}</p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-6">
                <button type="button" @click="revoke.show = false" :disabled="revoke.loading"
                        class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 transition disabled:opacity-50">
                    {{ __('Cancel') }}
                </button>
                <button type="button" @click="revokeKey()" :disabled="revoke.loading"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition shadow-sm disabled:opacity-60">
                    <svg x-show="revoke.loading" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    {{ __('Revoke') }}
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
         class="fixed bottom-6 right-6 z-[60] flex items-center gap-3 pl-4 pr-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium max-w-sm"
         :class="toast.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'">
        <svg x-show="toast.type !== 'error'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <svg x-show="toast.type === 'error'" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span x-text="toast.message"></span>
    </div>

</div>

<script>
    function apiSettings() {
        const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

        return {
            keys: @js($apiKeys),
            newKeyName: '',
            generating: false,
            showPrivate: {},
            copied: null,
            revoke: { show: false, id: null, name: '', loading: false },
            toast: { show: false, message: '', type: 'success' },
            _toastTimer: null,
            _copyTimer: null,

            init() {
                this.keys.forEach(k => { this.showPrivate[k.id] = false; });
            },

            firstError(data, fallback) {
                if (data && data.errors) {
                    const first = Object.values(data.errors)[0];
                    if (Array.isArray(first) && first.length) return first[0];
                }
                return (data && data.message) || fallback;
            },

            async generateKey() {
                const name = this.newKeyName.trim();
                if (!name || this.generating) return;

                this.generating = true;

                try {
                    const res = await fetch('{{ route('admin.settings.api.generate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf()
                        },
                        body: JSON.stringify({ name })
                    });

                    const data = await res.json().catch(() => ({}));

                    if (res.ok && data.success) {
                        this.keys.push(data.key);
                        this.showPrivate[data.key.id] = false;
                        this.newKeyName = '';
                        this.showToast(data.message || @js(__('Key generated.')), 'success');
                    } else {
                        this.showToast(this.firstError(data, @js(__('Failed to generate key.'))), 'error');
                    }
                } catch (e) {
                    this.showToast(@js(__('Network error.')), 'error');
                } finally {
                    this.generating = false;
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
                    this.showToast(@js(__('Copied to clipboard.')), 'success');
                } else {
                    this.showToast(@js(__('Could not copy.')), 'error');
                }
            },

            askRevoke(key) {
                this.revoke = { show: true, id: key.id, name: key.name, loading: false };
            },

            async revokeKey() {
                if (this.revoke.loading || this.revoke.id === null) return;

                this.revoke.loading = true;
                const id = this.revoke.id;

                try {
                    const res = await fetch(`{{ url('/admin/settings/api') }}/${id}/revoke`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf(),
                            'X-HTTP-Method-Override': 'DELETE'
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    });

                    const data = await res.json().catch(() => ({}));

                    if (res.ok && data.success) {
                        this.keys = this.keys.filter(k => k.id !== id);
                        delete this.showPrivate[id];
                        this.revoke.show = false;
                        this.showToast(data.message || @js(__('Key revoked.')), 'success');
                    } else {
                        this.showToast(this.firstError(data, @js(__('Failed to revoke key.'))), 'error');
                    }
                } catch (e) {
                    this.showToast(@js(__('Network error.')), 'error');
                } finally {
                    this.revoke.loading = false;
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
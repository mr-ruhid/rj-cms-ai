@extends('admin.settings.layout')

@section('settings')
<div class="space-y-6" x-data="{ region: 'all' }">

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">{{ __('Social Login') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Let users log in with their social accounts. Enable only the providers you need and enter API keys.') }}</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.social.update') }}" class="p-6 space-y-5" id="socialForm">
            @csrf
            @method('PUT')

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="social_enabled" value="1"
                       {{ ($options['social_enabled'] ?? '0') === '1' ? 'checked' : '' }}
                       class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Enable social login') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Master switch for all social login providers.') }}</span>
                </span>
            </label>

            <div class="border-t border-gray-100 pt-5 space-y-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="social_auto_register" value="1"
                           {{ ($options['social_auto_register'] ?? '1') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Auto-register new users') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Create account automatically on first social login.') }}</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="social_auto_link" value="1"
                           {{ ($options['social_auto_link'] ?? '1') === '1' ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Auto-link by email') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Link social account to existing user with same email.') }}</span>
                    </span>
                </label>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default role for new users') }}</label>
                    <select name="social_default_role" class="w-full md:w-64 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="user" {{ ($options['social_default_role'] ?? 'user') === 'user' ? 'selected' : '' }}>{{ __('User') }}</option>
                        <option value="editor" {{ ($options['social_default_role'] ?? 'user') === 'editor' ? 'selected' : '' }}>{{ __('Editor') }}</option>
                        <option value="admin" {{ ($options['social_default_role'] ?? 'user') === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                    </select>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5 flex flex-wrap items-center gap-2">
                <span class="text-xs text-gray-500 mr-2">{{ __('Filter by region:') }}</span>

                <button type="button" @click="region = 'all'"
                        :class="region === 'all' ? 'bg-rj-purple text-white border-rj-purple' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                        class="px-3 py-1.5 rounded-lg border text-xs font-medium transition">
                    {{ __('All') }}
                </button>

                @foreach ($data['regions'] as $key => $region)
                    <button type="button" @click="region = '{{ $key }}'"
                            :class="region === '{{ $key }}' ? 'bg-rj-purple text-white border-rj-purple' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                            class="px-3 py-1.5 rounded-lg border text-xs font-medium transition">
                        {{ $region['flag'] }} {{ __($region['label']) }}
                    </button>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 pt-2">
                @foreach ($data['providers'] as $key => $provider)
                    @php
                        $enabled = ($options['social_' . $key . '_enabled'] ?? '0') === '1';
                    @endphp

                    <div x-show="region === 'all' || region === '{{ $provider['region'] }}'"
                         x-data="{ open: {{ $enabled ? 'true' : 'false' }} }"
                         class="border border-gray-200 rounded-xl overflow-hidden transition hover:border-gray-300">

                        <div class="flex items-center justify-between gap-3 px-4 py-3 bg-gray-50/50">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-white font-bold text-sm"
                                     style="background-color: {{ $provider['color'] }};">
                                    {{ strtoupper(substr($provider['label'], 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ $provider['label'] }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $data['regions'][$provider['region']]['flag'] }}
                                        {{ __($data['regions'][$provider['region']]['label']) }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="{{ $provider['docs'] }}" target="_blank"
                                   class="text-xs text-rj-purple hover:underline flex items-center gap-1">
                                    {{ __('Docs') }}
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>

                                <button type="button" @click="open = !open"
                                        class="text-gray-400 hover:text-gray-600 transition">
                                    <svg class="w-4 h-4 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between bg-white">
                            <span class="text-xs text-gray-500">
                                <span x-show="!open">{{ __('Disabled — click to configure') }}</span>
                                <span x-show="open" x-cloak>{{ __('Configure API credentials below') }}</span>
                            </span>

                            <label class="inline-flex items-center cursor-pointer">
                                <input type="hidden" name="providers[{{ $key }}][enabled]" :value="open ? '0' : '0'">
                                <input type="checkbox" name="providers[{{ $key }}][enabled]" value="1"
                                       x-model="open"
                                       {{ $enabled ? 'checked' : '' }}
                                       class="sr-only peer">
                                <span class="relative w-9 h-5 bg-gray-300 rounded-full peer-checked:bg-rj-purple transition">
                                    <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></span>
                                </span>
                            </label>
                        </div>

                        <div x-show="open" x-cloak x-transition class="px-4 py-4 bg-gray-50/30 border-t border-gray-100 space-y-3">
                            @foreach ($provider['fields'] as $field => $meta)
                                @php
                                    $fieldName = 'social_' . $key . '_' . $field;
                                    $fieldValue = $options[$fieldName] ?? '';
                                @endphp

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __($meta['label']) }}</label>

                                    @if ($meta['type'] === 'textarea')
                                        <textarea name="providers[{{ $key }}][{{ $field }}]" rows="3"
                                                  class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs font-mono">{{ $fieldValue }}</textarea>
                                    @else
                                        <input type="{{ $meta['type'] }}"
                                               name="providers[{{ $key }}][{{ $field }}]"
                                               value="{{ $fieldValue }}"
                                               autocomplete="off"
                                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs font-mono">
                                    @endif
                                </div>
                            @endforeach

                            @if ($key === 'google')
                                <p class="text-xs text-gray-500 pt-1">
                                    {{ __('Redirect URI:') }}
                                    <code class="font-mono bg-white px-1.5 py-0.5 rounded border border-gray-200">{{ url('/auth/google/callback') }}</code>
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-gray-100 pt-5 flex items-center gap-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                    {{ __('Save Changes') }}
                </button>
                <span class="text-xs text-gray-500">{{ __('Only enabled providers will appear on the login page.') }}</span>
            </div>

        </form>
    </div>

</div>
@endsection
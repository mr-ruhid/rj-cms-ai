@extends('admin.settings.layout')

@section('settings')
<div class="space-y-6">

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900">{{ __('Languages') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Manage installed languages and translate them.') }}</p>
            </div>
        </div>

        <div class="divide-y divide-gray-100">
            @foreach ($data['locales'] as $loc)
                <div class="px-6 py-4" x-data="{ nameOpen: false, flagOpen: false }">
                    <div class="flex items-center justify-between gap-4">

                        <div class="flex items-center gap-4 min-w-0">
                            <div class="flex-shrink-0">
                                @php $flag = rj_lang_flag_url($loc); @endphp
                                @if ($flag)
                                    <img src="{{ $flag }}" alt="{{ strtoupper($loc) }}" class="w-10 h-7 object-cover rounded shadow-sm border border-gray-200">
                                @else
                                    <div class="w-10 h-7 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-xs text-gray-400 font-medium">
                                        {{ strtoupper($loc) }}
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ rj_lang_name($loc) }}
                                    <span class="text-xs font-normal text-gray-400 ml-1">({{ strtoupper($loc) }})</span>
                                </div>
                                @if ($loc === config('app.locale'))
                                    <div class="text-xs text-gray-400">{{ __('Default language') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button type="button" @click="flagOpen = !flagOpen; nameOpen = false"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ __('Flag') }}
                            </button>

                            <button type="button" @click="nameOpen = !nameOpen; flagOpen = false"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                {{ __('Name') }}
                            </button>

                            <a href="{{ route('admin.settings.language.translations', $loc) }}"
                               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-rj-purple/30 text-xs text-rj-purple hover:bg-rj-purple/5 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                                {{ __('Translate') }}
                            </a>

                            @if ($loc !== config('app.locale'))
                                <form method="POST" action="{{ route('admin.settings.language.remove', $loc) }}"
                                      onsubmit="return confirm('{{ __('Remove this language? This cannot be undone.') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 transition" title="{{ __('Remove') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div x-show="flagOpen" x-cloak x-transition class="mt-3 pl-14">
                        <form method="POST" action="{{ route('admin.settings.language.meta', $loc) }}" enctype="multipart/form-data" class="flex items-center gap-3">
                            @csrf
                            <input type="file" name="flag" accept=".svg,.png,.jpg,.jpeg,.webp" required
                                   class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-rj-purple/10 file:text-rj-purple hover:file:bg-rj-purple/20 cursor-pointer">
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-medium hover:bg-gray-800 transition whitespace-nowrap">
                                {{ __('Upload') }}
                            </button>
                        </form>
                        <p class="text-xs text-gray-400 mt-1">{{ __('Recommended: SVG or PNG, max 512KB.') }}</p>
                    </div>

                    <div x-show="nameOpen" x-cloak x-transition class="mt-3 pl-14">
                        <form method="POST" action="{{ route('admin.settings.language.meta', $loc) }}" class="flex items-center gap-3">
                            @csrf
                            <input type="text" name="name" value="{{ rj_get_option('lang_name_' . $loc, '') }}"
                                   placeholder="{{ __('e.g. Azərbaycan dili') }}"
                                   class="flex-1 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-medium hover:bg-gray-800 transition whitespace-nowrap">
                                {{ __('Save') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <form method="POST" action="{{ route('admin.settings.language.add') }}" class="flex items-center gap-3">
                @csrf
                <input type="text" name="locale" required placeholder="{{ __('e.g. de, fr, es') }}"
                       pattern="[a-z]{2}(_[A-Z]{2})?"
                       class="w-40 rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono lowercase">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Add Language') }}
                </button>
                <p class="text-xs text-gray-500">{{ __('Use ISO 639-1 code (lowercase), e.g. de, fr, es, ja') }}</p>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.language.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">{{ __('Language Settings') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Configure admin and frontend languages.') }}</p>
            </div>

            <div class="p-6 space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Admin Language') }}</label>
                        <select name="admin_locale" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            @foreach ($data['locales'] as $loc)
                                <option value="{{ $loc }}" {{ ($options['admin_locale'] ?? config('app.locale')) === $loc ? 'selected' : '' }}>
                                    {{ rj_lang_name($loc) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Frontend Language') }}</label>
                        <select name="frontend_locale" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            @foreach ($data['locales'] as $loc)
                                <option value="{{ $loc }}" {{ ($options['frontend_locale'] ?? config('app.locale')) === $loc ? 'selected' : '' }}>
                                    {{ rj_lang_name($loc) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-5 space-y-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="auto_detect_locale" value="1" {{ ($options['auto_detect_locale'] ?? '0') === '1' ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span>
                            <span class="block text-sm font-medium text-gray-700">{{ __('Auto-detect browser language') }}</span>
                            <span class="block text-xs text-gray-500 mt-0.5">{{ __('Automatically set locale based on visitor browser.') }}</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="rtl_support" value="1" {{ ($options['rtl_support'] ?? '0') === '1' ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        <span>
                            <span class="block text-sm font-medium text-gray-700">{{ __('Enable RTL support') }}</span>
                            <span class="block text-xs text-gray-500 mt-0.5">{{ __('For right-to-left languages like Arabic, Hebrew.') }}</span>
                        </span>
                    </label>
                </div>

            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">{{ __('AI Translation') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Translate missing strings automatically using AI.') }}</p>
            </div>

            <div class="p-6 space-y-5">

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="ai_translation_enabled" value="1" {{ ($options['ai_translation_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Enable AI translation') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Use OpenRouter to translate missing translation keys.') }}</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="ai_translation_auto" value="1" {{ ($options['ai_translation_auto'] ?? '0') === '1' ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span>
                        <span class="block text-sm font-medium text-gray-700">{{ __('Auto-translate on save') }}</span>
                        <span class="block text-xs text-gray-500 mt-0.5">{{ __('Translate new keys immediately when they are added.') }}</span>
                    </span>
                </label>

                <p class="text-xs text-gray-500 border-t border-gray-100 pt-4">
                    {{ __('AI provider is configured in') }}
                    <a href="{{ route('admin.settings.ai') }}" class="text-rj-purple hover:underline">{{ __('Settings → AI') }}</a>
                </p>

            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                {{ __('Save Changes') }}
            </button>
        </div>

    </form>

</div>
@endsection

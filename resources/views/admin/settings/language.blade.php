@extends('admin.settings.layout')

@section('settings')
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
                        @foreach ($data['locales'] as $locale)
                            <option value="{{ $locale }}" {{ ($options['admin_locale'] ?? config('app.locale')) === $locale ? 'selected' : '' }}>
                                {{ strtoupper($locale) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Frontend Language') }}</label>
                    <select name="frontend_locale" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        @foreach ($data['locales'] as $locale)
                            <option value="{{ $locale }}" {{ ($options['frontend_locale'] ?? config('app.locale')) === $locale ? 'selected' : '' }}>
                                {{ strtoupper($locale) }}
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
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Use AI provider to translate missing translation keys.') }}</span>
                </span>
            </label>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('AI Provider') }}</label>
                <select name="ai_translation_provider" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <option value="openai" {{ ($options['ai_translation_provider'] ?? '') === 'openai' ? 'selected' : '' }}>OpenAI</option>
                    <option value="anthropic" {{ ($options['ai_translation_provider'] ?? '') === 'anthropic' ? 'selected' : '' }}>Anthropic</option>
                    <option value="gemini" {{ ($options['ai_translation_provider'] ?? '') === 'gemini' ? 'selected' : '' }}>Google Gemini</option>
                    <option value="deepl" {{ ($options['ai_translation_provider'] ?? '') === 'deepl' ? 'selected' : '' }}>DeepL</option>
                </select>
            </div>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="ai_translation_auto" value="1" {{ ($options['ai_translation_auto'] ?? '0') === '1' ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                <span>
                    <span class="block text-sm font-medium text-gray-700">{{ __('Auto-translate on save') }}</span>
                    <span class="block text-xs text-gray-500 mt-0.5">{{ __('Translate new keys immediately when they are added.') }}</span>
                </span>
            </label>

        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            {{ __('Save Changes') }}
        </button>
    </div>

</form>
@endsection

@extends('admin.settings.layout')

@section('settings')
    <form method="POST" action="{{ route('admin.settings.general.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Site Identity') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Basic information about your site.') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Site Name') }}</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $options['site_name'] ?? '') }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('site_name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Tagline') }}</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $options['site_tagline'] ?? '') }}" placeholder="{{ __('A short description of your site') }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('site_tagline') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Site URL') }}</label>
                    <input type="text" name="site_url" value="{{ old('site_url', $options['site_url'] ?? '') }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('site_url') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Admin Email') }}</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email', $options['admin_email'] ?? '') }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('admin_email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Localization') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Timezone, language, and date format.') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Timezone') }}</label>
                    <select name="timezone" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                        @foreach ($data['timezones'] as $tz)
                            <option value="{{ $tz }}" @selected(old('timezone', $options['timezone'] ?? 'UTC') === $tz)>{{ $tz }}</option>
                        @endforeach
                    </select>
                    @error('timezone') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Language') }}</label>
                    <select name="locale" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                        @foreach ($data['locales'] as $locale)
                            <option value="{{ $locale }}" @selected(old('locale', $options['locale'] ?? app()->getLocale()) === $locale)>{{ strtoupper($locale) }}</option>
                        @endforeach
                    </select>
                    @error('locale') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Date Format') }}</label>
                    <select name="date_format" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                        @foreach ($data['dateFormats'] as $format => $example)
                            <option value="{{ $format }}" @selected(old('date_format', $options['date_format'] ?? 'Y-m-d') === $format)>{{ $example }} ({{ $format }})</option>
                        @endforeach
                    </select>
                    @error('date_format') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Time Format') }}</label>
                    <select name="time_format" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                        @foreach ($data['timeFormats'] as $format => $example)
                            <option value="{{ $format }}" @selected(old('time_format', $options['time_format'] ?? 'H:i') === $format)>{{ $example }} ({{ $format }})</option>
                        @endforeach
                    </select>
                    @error('time_format') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Week Starts On') }}</label>
                    <select name="week_starts_on" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                        @foreach ($data['weekdays'] as $num => $label)
                            <option value="{{ $num }}" @selected((int) old('week_starts_on', $options['week_starts_on'] ?? 1) === $num)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('week_starts_on') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Currency') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('How prices and amounts are displayed.') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Currency') }}</label>
                    <select name="currency" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                        @foreach ($data['currencies'] as $code => $label)
                            <option value="{{ $code }}" @selected(old('currency', $options['currency'] ?? 'USD') === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('currency') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Currency Position') }}</label>
                    <select name="currency_position" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                        <option value="left" @selected(old('currency_position', $options['currency_position'] ?? 'left') === 'left')>{{ __('Left ($100)') }}</option>
                        <option value="right" @selected(old('currency_position', $options['currency_position'] ?? '') === 'right')>{{ __('Right (100$)') }}</option>
                    </select>
                    @error('currency_position') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Thousands Separator') }}</label>
                    <input type="text" name="thousands_separator" value="{{ old('thousands_separator', $options['thousands_separator'] ?? ',') }}" maxlength="3" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('thousands_separator') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Decimal Separator') }}</label>
                    <input type="text" name="decimal_separator" value="{{ old('decimal_separator', $options['decimal_separator'] ?? '.') }}" maxlength="3" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('decimal_separator') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Decimal Digits') }}</label>
                    <input type="number" name="decimal_digits" value="{{ old('decimal_digits', $options['decimal_digits'] ?? 2) }}" min="0" max="6" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('decimal_digits') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-gradient-to-r from-rj-purple to-rj-pink text-white px-5 py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                {{ __('Save') }}
            </button>
            <a href="{{ route('admin.settings.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                {{ __('Back') }}
            </a>
        </div>
    </form>
@endsection

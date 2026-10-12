@extends('admin.settings.layout')

@section('settings')
    <div x-data="{ showTest: false }" class="space-y-6">

        <form method="POST" action="{{ route('admin.settings.email.update') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Email / SMTP') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Configure how your site sends emails.') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Mail Driver') }}</label>
                    <select name="mail_driver" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                        @foreach (['smtp', 'sendmail', 'mailgun', 'ses', 'postmark', 'log', 'array'] as $driver)
                            <option value="{{ $driver }}" @selected(old('mail_driver', $options['mail_driver'] ?? 'smtp') === $driver)>{{ $driver }}</option>
                        @endforeach
                    </select>
                    @error('mail_driver') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Mail Host') }}</label>
                    <input type="text" name="mail_host" value="{{ old('mail_host', $options['mail_host'] ?? '') }}" placeholder="smtp.mailtrap.io" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('mail_host') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Mail Port') }}</label>
                    <input type="number" name="mail_port" value="{{ old('mail_port', $options['mail_port'] ?? '') }}" placeholder="587" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('mail_port') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Mail Username') }}</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username', $options['mail_username'] ?? '') }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('mail_username') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Mail Password') }}</label>
                    <input type="password" name="mail_password" value="{{ old('mail_password', $options['mail_password'] ?? '') }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('mail_password') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Mail Encryption') }}</label>
                    <select name="mail_encryption" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                        <option value="null" @selected(old('mail_encryption', $options['mail_encryption'] ?? 'null') === 'null')>{{ __('None') }}</option>
                        <option value="tls" @selected(old('mail_encryption', $options['mail_encryption'] ?? '') === 'tls')>TLS</option>
                        <option value="ssl" @selected(old('mail_encryption', $options['mail_encryption'] ?? '') === 'ssl')>SSL</option>
                    </select>
                    @error('mail_encryption') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('From Address') }}</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $options['mail_from_address'] ?? '') }}" placeholder="noreply@example.com" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('mail_from_address') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('From Name') }}</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $options['mail_from_name'] ?? '') }}" placeholder="RJ CMS" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    @error('mail_from_name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-gradient-to-r from-rj-purple to-rj-pink text-white px-5 py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                    {{ __('Save') }}
                </button>

                <button type="button" @click="showTest = true" class="border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition">
                    {{ __('Send Test Email') }}
                </button>
            </div>
        </form>

        <div x-show="showTest" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div @click.away="showTest = false" class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Send Test Email') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('Enter an email address to send a test message to.') }}</p>

                <form method="POST" action="{{ route('admin.settings.email.test') }}" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Recipient Email') }}</label>
                        <input type="email" name="test_email" value="{{ auth()->user()->email }}" required class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showTest = false" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="bg-gradient-to-r from-rj-purple to-rj-pink text-white px-4 py-2 rounded-lg font-medium hover:opacity-90">
                            {{ __('Send') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@extends('admin.settings.layout')

@section('settings')
    <form method="POST" action="{{ route('admin.settings.security.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Admin Security') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Protect admin panel access.') }}</p>
            </div>

            <div class="flex items-center justify-between py-3 border-t border-gray-100">
                <div>
                    <div class="text-sm font-medium text-gray-900">{{ __('Two-Factor Authentication') }}</div>
                    <div class="text-xs text-gray-500">{{ __('Require 2FA for all admin users.') }}</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="admin_2fa_enabled" value="0">
                    <input type="checkbox" name="admin_2fa_enabled" value="1" @checked(old('admin_2fa_enabled', $options['admin_2fa_enabled'] ?? '0') === '1') class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-gradient-to-r peer-checked:from-rj-purple peer-checked:to-rj-pink transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('2FA Method') }}</label>
                <select name="admin_2fa_method" class="w-full max-w-xs rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    <option value="authenticator" @selected(old('admin_2fa_method', $options['admin_2fa_method'] ?? 'authenticator') === 'authenticator')>{{ __('Authenticator App') }}</option>
                    <option value="email" @selected(old('admin_2fa_method', $options['admin_2fa_method'] ?? '') === 'email')>{{ __('Email') }}</option>
                    <option value="sms" @selected(old('admin_2fa_method', $options['admin_2fa_method'] ?? '') === 'sms')>{{ __('SMS') }}</option>
                </select>
                <div class="text-xs text-gray-500 mt-1">{{ __('Note: SMS requires an external gateway configuration.') }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Frontend Security') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Protect user accounts on the public site.') }}</p>
            </div>

            <div class="flex items-center justify-between py-3 border-t border-gray-100">
                <div>
                    <div class="text-sm font-medium text-gray-900">{{ __('Enable 2FA for Users') }}</div>
                    <div class="text-xs text-gray-500">{{ __('Allow users to secure their accounts.') }}</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="frontend_2fa_enabled" value="0">
                    <input type="checkbox" name="frontend_2fa_enabled" value="1" @checked(old('frontend_2fa_enabled', $options['frontend_2fa_enabled'] ?? '0') === '1') class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-gradient-to-r peer-checked:from-rj-purple peer-checked:to-rj-pink transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pl-0 sm:pl-4">
                <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 hover:border-rj-purple/40 cursor-pointer">
                    <input type="hidden" name="frontend_2fa_email" value="0">
                    <input type="checkbox" name="frontend_2fa_email" value="1" @checked(old('frontend_2fa_email', $options['frontend_2fa_email'] ?? '0') === '1') class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ __('Email Code') }}</div>
                        <div class="text-xs text-gray-500">{{ __('Send code to email') }}</div>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 hover:border-rj-purple/40 cursor-pointer">
                    <input type="hidden" name="frontend_2fa_mobile" value="0">
                    <input type="checkbox" name="frontend_2fa_mobile" value="1" @checked(old('frontend_2fa_mobile', $options['frontend_2fa_mobile'] ?? '0') === '1') class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ __('SMS Code') }}</div>
                        <div class="text-xs text-gray-500">{{ __('Requires gateway') }}</div>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 hover:border-rj-purple/40 cursor-pointer">
                    <input type="hidden" name="frontend_2fa_authenticator" value="0">
                    <input type="checkbox" name="frontend_2fa_authenticator" value="1" @checked(old('frontend_2fa_authenticator', $options['frontend_2fa_authenticator'] ?? '0') === '1') class="mt-0.5 rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ __('Authenticator') }}</div>
                        <div class="text-xs text-gray-500">{{ __('Google, Authy and more') }}</div>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-between py-3 border-t border-gray-100">
                <div>
                    <div class="text-sm font-medium text-gray-900">{{ __('Require Email Verification') }}</div>
                    <div class="text-xs text-gray-500">{{ __('Users must verify their email before logging in.') }}</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="require_email_verification" value="0">
                    <input type="checkbox" name="require_email_verification" value="1" @checked(old('require_email_verification', $options['require_email_verification'] ?? '0') === '1') class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-gradient-to-r peer-checked:from-rj-purple peer-checked:to-rj-pink transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5" x-data="{ kycDriver: '{{ old('kyc_driver', $options['kyc_driver'] ?? 'manual') }}' }">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('KYC Verification') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Request identity verification from users.') }}</p>
            </div>

            <div class="flex items-center justify-between py-3 border-t border-gray-100">
                <div>
                    <div class="text-sm font-medium text-gray-900">{{ __('Enable KYC') }}</div>
                    <div class="text-xs text-gray-500">{{ __('Ask users to verify their identity.') }}</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="kyc_enabled" value="0">
                    <input type="checkbox" name="kyc_enabled" value="1" @checked(old('kyc_enabled', $options['kyc_enabled'] ?? '0') === '1') class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-gradient-to-r peer-checked:from-rj-purple peer-checked:to-rj-pink transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('KYC Driver') }}</label>
                <select name="kyc_driver" x-model="kycDriver" class="w-full max-w-xs rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    <option value="manual">{{ __('Manual Review (Admin)') }}</option>
                    <option value="api">{{ __('External API (Sumsub, Onfido)') }}</option>
                </select>
            </div>

            <div x-show="kycDriver === 'api'" x-cloak class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('API Endpoint') }}</label>
                    <input type="url" name="kyc_api_endpoint" value="{{ old('kyc_api_endpoint', $options['kyc_api_endpoint'] ?? '') }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple" placeholder="https://api.sumsub.com">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('API Key') }}</label>
                        <input type="text" name="kyc_api_key" value="{{ old('kyc_api_key', $options['kyc_api_key'] ?? '') }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple font-mono text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('API Secret') }}</label>
                        <input type="password" name="kyc_api_secret" value="{{ old('kyc_api_secret', $options['kyc_api_secret'] ?? '') }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple font-mono text-sm">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('KYC Level') }}</label>
                <select name="kyc_level" class="w-full max-w-xs rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                    <option value="none" @selected(old('kyc_level', $options['kyc_level'] ?? 'none') === 'none')>{{ __('None') }}</option>
                    <option value="basic" @selected(old('kyc_level', $options['kyc_level'] ?? '') === 'basic')>{{ __('Basic') }}</option>
                    <option value="standard" @selected(old('kyc_level', $options['kyc_level'] ?? '') === 'standard')>{{ __('Standard') }}</option>
                    <option value="advanced" @selected(old('kyc_level', $options['kyc_level'] ?? '') === 'advanced')>{{ __('Advanced') }}</option>
                </select>
            </div>

            @php
                $kycFields = json_decode($options['kyc_required_fields'] ?? '[]', true) ?: [];
            @endphp

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Required Fields') }}</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="kyc_required_fields[]" value="full_name" @checked(in_array('full_name', $kycFields)) class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        {{ __('Full Name') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="kyc_required_fields[]" value="phone" @checked(in_array('phone', $kycFields)) class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        {{ __('Phone') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="kyc_required_fields[]" value="address" @checked(in_array('address', $kycFields)) class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        {{ __('Address') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="kyc_required_fields[]" value="id_card" @checked(in_array('id_card', $kycFields)) class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        {{ __('ID Card') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="kyc_required_fields[]" value="passport" @checked(in_array('passport', $kycFields)) class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        {{ __('Passport') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="kyc_required_fields[]" value="selfie" @checked(in_array('selfie', $kycFields)) class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                        {{ __('Selfie') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Advanced Protection') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Additional security layers.') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Login Attempt Limit') }}</label>
                    <input type="number" name="login_limit" value="{{ old('login_limit', $options['login_limit'] ?? 5) }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Lockout Duration (minutes)') }}</label>
                    <input type="number" name="login_lockout_minutes" value="{{ old('login_lockout_minutes', $options['login_lockout_minutes'] ?? 15) }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                </div>
            </div>

            <div class="space-y-3 pt-2">
                <label class="flex items-center justify-between py-2 border-t border-gray-100">
                    <span class="text-sm text-gray-700">{{ __('Force HTTPS') }}</span>
                    <input type="hidden" name="force_https" value="0">
                    <input type="checkbox" name="force_https" value="1" @checked(old('force_https', $options['force_https'] ?? '0') === '1') class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                </label>

                <label class="flex items-center justify-between py-2 border-t border-gray-100">
                    <span class="text-sm text-gray-700">{{ __('Enable CAPTCHA') }}</span>
                    <input type="hidden" name="captcha_enabled" value="0">
                    <input type="checkbox" name="captcha_enabled" value="1" @checked(old('captcha_enabled', $options['captcha_enabled'] ?? '0') === '1') class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                </label>

                <label class="flex items-center justify-between py-2 border-t border-gray-100">
                    <span class="text-sm text-gray-700">{{ __('IP Whitelist') }}</span>
                    <input type="hidden" name="ip_whitelist_enabled" value="0">
                    <input type="checkbox" name="ip_whitelist_enabled" value="1" @checked(old('ip_whitelist_enabled', $options['ip_whitelist_enabled'] ?? '0') === '1') class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Allowed IPs') }}</label>
                <textarea name="ip_whitelist" rows="3" placeholder="192.168.1.1&#10;10.0.0.1" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">{{ old('ip_whitelist', $options['ip_whitelist'] ?? '') }}</textarea>
                <div class="text-xs text-gray-500 mt-1">{{ __('One IP per line.') }}</div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-gradient-to-r from-rj-purple to-rj-pink text-white px-5 py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                {{ __('Save') }}
            </button>
        </div>
    </form>
@endsection

@extends('admin.layouts.app')

@section('title', __('Profile'))

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Profile Information') }}</h3>
        <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4 max-w-xl">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rj-purple focus:ring-rj-purple sm:text-sm" required>
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rj-purple focus:ring-rj-purple sm:text-sm" required>
                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 text-sm font-medium">{{ __('Save') }}</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Update Password') }}</h3>
        <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4 max-w-xl">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Current Password') }}</label>
                <input type="password" name="current_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rj-purple focus:ring-rj-purple sm:text-sm" required>
                @error('current_password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('New Password') }}</label>
                <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rj-purple focus:ring-rj-purple sm:text-sm" required>
                @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
                <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rj-purple focus:ring-rj-purple sm:text-sm" required>
            </div>
            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 text-sm font-medium">{{ __('Update Password') }}</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Two-Factor Authentication') }}</h3>
        
        @php $user = auth()->user(); @endphp

        @if (!$user->hasEnabledTwoFactorAuthentication())
            @if ($user->two_factor_secret)
                <div x-data="{ showSecret: false }" class="max-w-md">
                    <p class="text-sm text-gray-600 mb-4">{{ __('Scan this QR code with your authenticator app (like Google Authenticator).') }}</p>
                    @php
                        $qrCodeUrl = app(\PragmaRX\Google2FA\Google2FA::class)->getQRCodeUrl(config('app.name'), $user->email, $user->two_factor_secret);
                    @endphp
                    <div class="mb-4 bg-white p-2 inline-block border rounded-lg">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code" class="w-48 h-48">
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ __('Or enter this secret manually:') }}</p>
                    <button @click="showSecret = !showSecret" type="button" class="text-sm text-rj-purple underline mb-4 hover:text-rj-pink">{{ __('Toggle Secret') }}</button>
                    <div x-show="showSecret" x-cloak class="bg-gray-50 p-2 rounded font-mono text-sm mb-4 break-all border">{{ $user->two_factor_secret }}</div>
                    
                    <form action="{{ route('admin.profile.two-factor.confirm') }}" method="POST">
                        @csrf
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('6-digit Code') }}</label>
                        <input type="text" name="code" class="w-full rounded-md border-gray-300 shadow-sm focus:border-rj-purple focus:ring-rj-purple sm:text-sm" required autofocus placeholder="000000">
                        @error('code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        <button type="submit" class="mt-3 bg-rj-purple text-white px-4 py-2 rounded-lg hover:bg-rj-purple/90 text-sm font-medium">{{ __('Confirm') }}</button>
                    </form>
                </div>
            @else
                <form action="{{ route('admin.profile.two-factor.enable') }}" method="POST">
                    @csrf
                    <p class="text-sm text-gray-600 mb-4">{{ __('Add an extra layer of security to your account by requiring a 6-digit code from your authenticator app.') }}</p>
                    <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 text-sm font-medium">{{ __('Enable 2FA') }}</button>
                </form>
            @endif
        @else
            <div>
                <p class="text-sm text-green-600 font-medium mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('Two-factor authentication is enabled.') }}
                </p>
                
                <div x-data="{ showRecovery: false }" class="mb-6">
                    <button @click="showRecovery = !showRecovery" type="button" class="text-sm text-rj-purple underline hover:text-rj-pink">{{ __('Show Recovery Codes') }}</button>
                    <div x-show="showRecovery" x-cloak class="mt-2 bg-gray-50 p-4 rounded-lg text-sm font-mono border border-gray-200">
                        <p class="text-gray-500 mb-2 font-sans">{{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your 2FA device is lost.') }}</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($user->two_factor_recovery_codes as $code)
                                <div>{{ $code }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('admin.profile.two-factor.disable') }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to disable 2FA?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-lg hover:bg-red-100 text-sm font-medium">{{ __('Disable 2FA') }}</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
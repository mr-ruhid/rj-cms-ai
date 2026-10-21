@extends('admin.layouts.app')

@section('title', __('Profile'))

@section('content')
    <div x-data="{ tab: 'info' }" class="max-w-4xl">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="flex border-b border-gray-100 overflow-x-auto rj-scrollbar">
                <button @click="tab = 'info'" :class="tab === 'info' ? 'border-b-2 border-rj-purple text-rj-purple' : 'text-gray-500 hover:text-gray-800'" class="px-6 py-4 text-sm font-medium transition whitespace-nowrap">
                    {{ __('Profile Information') }}
                </button>
                <button @click="tab = 'password'" :class="tab === 'password' ? 'border-b-2 border-rj-purple text-rj-purple' : 'text-gray-500 hover:text-gray-800'" class="px-6 py-4 text-sm font-medium transition whitespace-nowrap">
                    {{ __('Password') }}
                </button>
                <button @click="tab = '2fa'" :class="tab === '2fa' ? 'border-b-2 border-rj-purple text-rj-purple' : 'text-gray-500 hover:text-gray-800'" class="px-6 py-4 text-sm font-medium transition whitespace-nowrap">
                    {{ __('Two-Factor Auth') }}
                </button>
                <button @click="tab = 'verification'" :class="tab === 'verification' ? 'border-b-2 border-rj-purple text-rj-purple' : 'text-gray-500 hover:text-gray-800'" class="px-6 py-4 text-sm font-medium transition whitespace-nowrap">
                    {{ __('Email Verification') }}
                </button>
            </div>

            <div class="p-6">

                <div x-show="tab === 'info'" class="rj-fade-in">
                    <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-5 max-w-xl">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Name') }}</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                            @error('name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Email') }}</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                            @error('email') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="bg-gradient-to-r from-rj-purple to-rj-pink text-white px-5 py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                            {{ __('Save') }}
                        </button>
                    </form>
                </div>

                <div x-show="tab === 'password'" x-cloak class="rj-fade-in">
                    <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-5 max-w-xl">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Current Password') }}</label>
                            <input type="password" name="current_password" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                            @error('current_password', 'updatePassword') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('New Password') }}</label>
                            <input type="password" name="password" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                            @error('password', 'updatePassword') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirmation" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple">
                        </div>

                        <button type="submit" class="bg-gradient-to-r from-rj-purple to-rj-pink text-white px-5 py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                            {{ __('Update Password') }}
                        </button>
                    </form>
                </div>

                <div x-show="tab === '2fa'" x-cloak class="rj-fade-in">
                    @php $user = auth()->user(); @endphp

                    @if (!$user->hasEnabledTwoFactorAuthentication())
                        @if ($user->two_factor_secret)
                            <div x-data="{ showSecret: false }" class="max-w-xl">
                                <p class="text-sm text-gray-600 mb-4">{{ __('Scan this QR code with your authenticator app (like Google Authenticator).') }}</p>
                                @php
                                    $qrCodeUrl = app(\PragmaRX\Google2FA\Google2FA::class)->getQRCodeUrl(config('app.name'), $user->email, $user->two_factor_secret);
                                @endphp
                                <div class="mb-4 bg-white p-2 inline-block border border-gray-200 rounded-lg">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code" class="w-48 h-48">
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ __('Or enter this secret manually:') }}</p>
                                <button @click="showSecret = !showSecret" type="button" class="text-sm text-rj-purple underline mb-4 hover:text-rj-pink">{{ __('Toggle Secret') }}</button>
                                <div x-show="showSecret" x-cloak class="bg-gray-50 p-2 rounded-lg font-mono text-sm mb-5 break-all border border-gray-200">{{ $user->two_factor_secret }}</div>
                                
                                <form action="{{ route('admin.profile.two-factor.confirm') }}" method="POST" class="space-y-5">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('6-digit Code') }}</label>
                                        <input type="text" name="code" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple" required autofocus placeholder="000000">
                                        @error('code') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <button type="submit" class="bg-gradient-to-r from-rj-purple to-rj-pink text-white px-5 py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                                        {{ __('Confirm & Enable') }}
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('admin.profile.two-factor.enable') }}" method="POST" class="max-w-xl">
                                @csrf
                                <p class="text-sm text-gray-600 mb-5">{{ __('Add an extra layer of security to your account by requiring a 6-digit code from your authenticator app.') }}</p>
                                <button type="submit" class="bg-gradient-to-r from-rj-purple to-rj-pink text-white px-5 py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                                    {{ __('Enable 2FA') }}
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="max-w-xl">
                            <p class="text-sm text-green-600 font-medium mb-5 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ __('Two-factor authentication is enabled.') }}
                            </p>
                            
                            <div x-data="{ showRecovery: false }" class="mb-6">
                                <button @click="showRecovery = !showRecovery" type="button" class="text-sm text-rj-purple underline hover:text-rj-pink">{{ __('Show Recovery Codes') }}</button>
                                <div x-show="showRecovery" x-cloak class="mt-3 bg-gray-50 p-4 rounded-lg text-sm font-mono border border-gray-200">
                                    <p class="text-gray-500 mb-3 font-sans">{{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your 2FA device is lost.') }}</p>
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
                                <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-5 py-2.5 rounded-lg hover:bg-red-100 font-medium transition">
                                    {{ __('Disable 2FA') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <div x-show="tab === 'verification'" x-cloak class="rj-fade-in">
                    <div class="max-w-xl">
                        @if (auth()->user()->hasVerifiedEmail())
                            <p class="text-sm text-green-600 font-medium mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ __('Your email address is verified.') }}
                            </p>
                        @else
                            <p class="text-sm text-gray-600 mb-5">
                                {{ __('Your email address is unverified. Please check your inbox for the verification link.') }}
                            </p>

                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="bg-gradient-to-r from-rj-purple to-rj-pink text-white px-5 py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                                    {{ __('Resend Verification Email') }}
                                </button>
                            </form>

                            @if (session('status') == 'verification-link-sent')
                                <div class="mt-4 text-sm text-green-600 bg-green-50 p-3 rounded-lg border border-green-200">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Two-Factor Authentication') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Add an extra layer of security to your account using an authenticator app.') }}
                            </p>
                        </header>

                        <div class="mt-6">
                            @php $user = auth()->user(); @endphp

                            @if (!$user->hasEnabledTwoFactorAuthentication())
                                @if ($user->two_factor_secret)
                                    <div x-data="{ showSecret: false }">
                                        @php
                                            $qrCodeUrl = app(\PragmaRX\Google2FA\Google2FA::class)->getQRCodeUrl(config('app.name'), $user->email, $user->two_factor_secret);
                                        @endphp
                                        <div class="mb-4">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code" class="border rounded-lg p-2 bg-white">
                                        </div>
                                        <button @click="showSecret = !showSecret" type="button" class="text-sm text-indigo-600 underline mb-4">{{ __('Toggle Secret') }}</button>
                                        <div x-show="showSecret" x-cloak class="bg-gray-50 p-2 rounded font-mono text-sm mb-4 break-all border">{{ $user->two_factor_secret }}</div>
                                        
                                        <form action="{{ route('admin.profile.two-factor.confirm') }}" method="POST" class="mt-4">
                                            @csrf
                                            <div>
                                                <x-input-label for="code" :value="__('6-digit Code')" />
                                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" required autofocus placeholder="000000" />
                                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                                            </div>
                                            <div class="flex items-center gap-4 mt-4">
                                                <x-primary-button>{{ __('Confirm') }}</x-primary-button>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <form action="{{ route('admin.profile.two-factor.enable') }}" method="POST">
                                        @csrf
                                        <x-primary-button>{{ __('Enable 2FA') }}</x-primary-button>
                                    </form>
                                @endif
                            @else
                                <div>
                                    <p class="text-sm text-green-600 font-medium mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ __('Two-factor authentication is enabled.') }}
                                    </p>
                                    
                                    <div x-data="{ showRecovery: false }" class="mb-6">
                                        <button @click="showRecovery = !showRecovery" type="button" class="text-sm text-indigo-600 underline">{{ __('Show Recovery Codes') }}</button>
                                        <div x-show="showRecovery" x-cloak class="mt-2 bg-gray-50 p-4 rounded-lg text-sm font-mono border border-gray-200">
                                            <p class="text-gray-500 mb-2 font-sans">{{ __('Store these recovery codes in a secure password manager.') }}</p>
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
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('Disable 2FA') }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
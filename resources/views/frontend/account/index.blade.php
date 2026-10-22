@extends('frontend.account.layout')

@section('title', __('My Account'))

@section('account_content')
<div class="space-y-6">

    <div class="bg-gradient-to-r from-rj-dark-1 to-rj-dark-2 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-rj-purple rounded-full mix-blend-multiply filter blur-2xl opacity-50"></div>
        <div class="relative z-10">
            <h1 class="text-2xl font-bold mb-2">{{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}! 👋</h1>
            <p class="text-gray-300 text-sm max-w-lg">{{ __('From your account dashboard you can view your recent activity, manage your security settings, and verify your identity.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-rj-purple/10 text-rj-purple flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ __('Identity Verification') }}</h3>

            @if(auth()->user()->kyc_status === 'approved')
                <p class="text-sm text-green-600 font-medium mb-4 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Your identity is verified.') }}
                </p>
                <div class="mt-auto">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-green-50 text-green-700 text-xs font-bold uppercase tracking-wide border border-green-200">
                        {{ __('Verified') }}
                    </span>
                </div>
            @elseif(auth()->user()->kyc_status === 'pending')
                <p class="text-sm text-yellow-600 font-medium mb-4 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('Documents under review.') }}
                </p>
                <div class="mt-auto">
                    <a href="{{ route('frontend.kyc.index') }}" class="text-sm font-medium text-rj-purple hover:underline">{{ __('View Status →') }}</a>
                </div>
            @else
                <p class="text-sm text-gray-500 mb-4">{{ __('Verify your identity to unlock all features and increase account security.') }}</p>
                <div class="mt-auto">
                    <a href="{{ route('frontend.kyc.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 bg-gray-900 text-white rounded-xl text-sm font-medium hover:bg-gray-800 transition">
                        {{ __('Verify Now') }}
                    </a>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ __('Security') }}</h3>
            <p class="text-sm text-gray-500 mb-4">{{ __('Manage your password and secure your account with 2FA.') }}</p>
            <div class="mt-auto">
                <a href="#" class="text-sm font-medium text-rj-purple hover:underline">{{ __('Manage Security →') }}</a>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ __('Profile Details') }}</h3>
            <p class="text-sm text-gray-500 mb-4">{{ __('Update your name, email address, and profile information.') }}</p>
            <div class="mt-auto">
                <a href="#" class="text-sm font-medium text-rj-purple hover:underline">{{ __('Edit Profile →') }}</a>
            </div>
        </div>

    </div>
</div>
@endsection

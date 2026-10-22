@extends('frontend.account.layout')

@section('title', __('Account Settings'))

@section('account_content')
<div class="space-y-6">

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 md:p-8 border-b border-gray-100 bg-gradient-to-b from-gray-50 to-white">
            <h1 class="text-xl font-bold text-gray-900">{{ __('Profile Information') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Update your account\'s profile information and email address.') }}</p>
        </div>

        <div class="p-6 md:p-8">
            <form method="POST" action="{{ route('frontend.account.settings.update') }}" class="max-w-xl space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Name') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                           class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple px-4 py-2.5">
                    @error('name') <div class="text-red-500 text-sm mt-1.5">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Email Address') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                           class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple px-4 py-2.5">
                    @error('email') <div class="text-red-500 text-sm mt-1.5">{{ $message }}</div> @enderror
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition shadow-sm">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 md:p-8 border-b border-gray-100 bg-gradient-to-b from-gray-50 to-white">
            <h1 class="text-xl font-bold text-gray-900">{{ __('Update Password') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
        </div>

        <div class="p-6 md:p-8">
            <form method="POST" action="{{ route('frontend.account.password.update') }}" class="max-w-xl space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Current Password') }}</label>
                    <input id="current_password" type="password" name="current_password" autocomplete="current-password"
                           class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple px-4 py-2.5">
                    @error('current_password', 'updatePassword') <div class="text-red-500 text-sm mt-1.5">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('New Password') }}</label>
                    <input id="password" type="password" name="password" autocomplete="new-password"
                           class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple px-4 py-2.5">
                    @error('password', 'updatePassword') <div class="text-red-500 text-sm mt-1.5">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                           class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple px-4 py-2.5">
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-rj-purple to-rj-pink text-white font-semibold rounded-xl hover:opacity-90 transition shadow-sm">
                        {{ __('Update Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@extends('admin.layouts.app')

@section('title', __('Profile'))

@section('content')
    <div x-data="{ tab: 'info' }" class="max-w-4xl">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="flex border-b border-gray-100">
                <button @click="tab = 'info'" :class="tab === 'info' ? 'border-b-2 border-rj-purple text-rj-purple' : 'text-gray-500 hover:text-gray-800'" class="px-6 py-4 text-sm font-medium transition">
                    {{ __('Profile Information') }}
                </button>
                <button @click="tab = 'password'" :class="tab === 'password' ? 'border-b-2 border-rj-purple text-rj-purple' : 'text-gray-500 hover:text-gray-800'" class="px-6 py-4 text-sm font-medium transition">
                    {{ __('Password') }}
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

            </div>
        </div>
    </div>
@endsection

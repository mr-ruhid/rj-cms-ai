<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Welcome back') }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('Sign in to your account to continue') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="email" class="block mt-1.5 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="password" class="block mt-1.5 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-900" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 hover:text-gray-900 underline" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-medium py-2.5 rounded-lg transition">
            {{ __('Log in') }}
        </button>
    </form>

    @if (Route::has('register'))
        <p class="text-center text-sm text-gray-600 mt-6">
            {{ __("Don't have an account?") }}
            <a href="{{ route('register') }}" class="text-gray-900 font-medium hover:underline">{{ __('Register') }}</a>
        </p>
    @endif
</x-guest-layout>

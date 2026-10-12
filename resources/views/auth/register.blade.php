<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Create your account') }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('Join us and start building today') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="name" class="block mt-1.5 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="email" class="block mt-1.5 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="password" class="block mt-1.5 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="password_confirmation" class="block mt-1.5 w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <label for="terms" class="flex items-start gap-2 text-sm text-gray-600">
            <input id="terms" type="checkbox" class="mt-0.5 rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-900" required>
            <span>{{ __('I agree to the') }} <a href="#" class="text-gray-900 font-medium hover:underline">{{ __('Terms') }}</a> {{ __('and') }} <a href="#" class="text-gray-900 font-medium hover:underline">{{ __('Privacy Policy') }}</a></span>
        </label>

        <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-medium py-2.5 rounded-lg transition">
            {{ __('Create account') }}
        </button>
    </form>

    <p class="text-center text-sm text-gray-600 mt-6">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="text-gray-900 font-medium hover:underline">{{ __('Sign in') }}</a>
    </p>
</x-guest-layout>

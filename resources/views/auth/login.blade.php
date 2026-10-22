<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Log in') }} — {{ rj_get_option('site_name', config('app.name')) }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Figtree', sans-serif;
            overflow: hidden; /* Dalğalar ekrandan çıxmasın */
            background-color: #f8fafc;
        }
        :root { {!! rj_css_variables() !!} }

        /* ===== Dalğa Animasiyası ===== */
        .wave-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 200%;
            height: 100%;
            background-repeat: repeat-x;
            background-position: 0 bottom;
            transform-origin: center bottom;
        }

        /* 1-ci Dalğa (Əsas rəng) */
        .wave-1 {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118.08,130.83,115.36,192,96.7,236,83,276.9,68.54,321.39,56.44Z' fill='%238b5cf6' fill-opacity='0.15'%3E%3C/path%3E%3C/svg%3E");
            background-size: 50% 100%;
            animation: waveMove 15s linear infinite;
            z-index: 3;
        }

        /* 2-ci Dalğa (Çəhrayı) */
        .wave-2 {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118.08,130.83,115.36,192,96.7,236,83,276.9,68.54,321.39,56.44Z' fill='%23ec4899' fill-opacity='0.1'%3E%3C/path%3E%3C/svg%3E");
            background-size: 50% 100%;
            animation: waveMove 20s linear infinite reverse;
            z-index: 2;
        }

        /* 3-cü Dalğa (Ən arxa, solğun) */
        .wave-3 {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118.08,130.83,115.36,192,96.7,236,83,276.9,68.54,321.39,56.44Z' fill='%23a855f7' fill-opacity='0.05'%3E%3C/path%3E%3C/svg%3E");
            background-size: 50% 100%;
            animation: waveMove 30s linear infinite;
            z-index: 1;
        }

        @keyframes waveMove {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* ===== Form Elementləri ===== */
        .glass-input {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
            border-color: #8b5cf6;
        }
    </style>
</head>
<body class="h-full text-gray-900 antialiased flex flex-col justify-center items-center p-4">

    <!-- Arxa Fon Dalğaları -->
    <div class="wave-container">
        <div class="wave wave-3"></div>
        <div class="wave wave-2"></div>
        <div class="wave wave-1"></div>
    </div>

    <!-- Əsas Form Sahəsi (Kartsız, birbaşa səhifədə) -->
    <div class="w-full max-w-md relative z-10">

        <div class="text-center mb-10">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2">
                @php $logoMask = rj_logo_mask_url(); @endphp
                @if ($logoMask)
                    <div class="h-14 w-48 bg-rj-dark-1" style="-webkit-mask-image: url('{{ $logoMask }}'); mask-image: url('{{ $logoMask }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center;"></div>
                @else
                    <span class="text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-rj-purple to-rj-pink">{{ rj_get_option('site_name', 'RJ CMS') }}</span>
                @endif
            </a>
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 text-center tracking-tight">{{ __('Welcome back') }}</h1>
            <p class="text-base text-gray-500 mt-2 text-center">{{ __('Sign in to your account to continue') }}</p>
        </div>

        @if (session('status'))
            <div class="mb-6 text-sm font-medium text-green-700 bg-green-50/80 backdrop-blur-sm p-3 rounded-xl border border-green-200 text-center">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50/80 backdrop-blur-sm text-red-600 p-4 rounded-xl border border-red-100 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Email Address') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="glass-input w-full rounded-2xl px-5 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none"
                       placeholder="name@example.com">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-semibold text-gray-700">{{ __('Password') }}</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-rj-purple hover:text-rj-pink transition">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="glass-input w-full rounded-2xl px-5 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember"
                           class="w-5 h-5 rounded-md border-gray-300 text-rj-purple shadow-sm focus:ring-rj-purple focus:ring-offset-0 bg-white/50">
                    <span class="ms-2.5 text-sm font-medium text-gray-700">{{ __('Remember me') }}</span>
                </label>
            </div>

            <button type="submit" class="w-full flex justify-center py-4 px-4 rounded-2xl shadow-lg shadow-rj-purple/30 text-base font-bold text-white bg-gradient-to-r from-rj-purple to-rj-pink hover:opacity-90 focus:outline-none focus:ring-4 focus:ring-rj-purple/30 transition-all active:scale-[.98]">
                {{ __('Sign In') }}
            </button>
        </form>

        @if (Route::has('register'))
            <div class="mt-8 text-center">
                <p class="text-sm font-medium text-gray-600">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="font-bold text-rj-purple hover:text-rj-pink transition ml-1">{{ __('Create one now') }}</a>
                </p>
            </div>
        @endif

        <div class="text-center mt-10">
            <a href="{{ url('/') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ __('Back to website') }}
            </a>
        </div>
    </div>

</body>
</html>

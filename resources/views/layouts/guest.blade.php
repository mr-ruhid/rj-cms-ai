<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RJ CMS') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white flex-col justify-between p-12">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-10 w-auto">
                <span class="text-xl font-semibold">{{ rj_get_option('site_name', 'RJ CMS') }}</span>
            </div>

            <div>
                <h1 class="text-4xl font-bold leading-tight mb-4">{{ __('Build something amazing.') }}</h1>
                <p class="text-gray-400 max-w-md">{{ __('A modern content management system built for speed, flexibility, and scale.') }}</p>
            </div>

            <div class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ rj_get_option('site_name', 'RJ CMS') }}</div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 bg-gray-50">
            <div class="w-full max-w-md">
                <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-10 w-auto">
                    <span class="text-xl font-semibold text-gray-900">{{ rj_get_option('site_name', 'RJ CMS') }}</span>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>

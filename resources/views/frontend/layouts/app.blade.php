<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900 antialiased">
    <header class="border-b">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-lg font-semibold">{{ config('app.name') }}</a>
            <nav class="flex items-center gap-6 text-sm">
                <a href="{{ route('home') }}" class="hover:underline">Home</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="hover:underline">Admin</a>
                @else
                    <a href="{{ route('login') }}" class="hover:underline">Login</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t mt-16">
        <div class="max-w-6xl mx-auto px-4 py-6 text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </footer>
</body>
</html>

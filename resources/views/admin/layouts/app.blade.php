<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dashboard')) - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-900 text-gray-100 flex-shrink-0">
            <div class="h-16 flex items-center px-6 border-b border-gray-800">
                <span class="text-lg font-semibold">{{ rj_get_option('site_name', 'RJ CMS') }}</span>
            </div>
            <nav class="p-4 space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">{{ __('Dashboard') }}</a>
                <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.users.*') ? 'bg-gray-800' : '' }}">{{ __('Users') }}</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-800">{{ __('Themes') }}</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-800">{{ __('Posts') }}</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-800">{{ __('Pages') }}</a>
                <a href="{{ route('admin.settings.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-800' : '' }}">{{ __('Settings') }}</a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="h-16 bg-white border-b flex items-center justify-between px-6">
                <div class="font-medium">@yield('title', __('Dashboard'))</div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Logout') }}</button>
                    </form>
                </div>
            </header>

            <main class="p-6 flex-1">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

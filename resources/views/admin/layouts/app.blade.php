<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-900 text-gray-100 flex-shrink-0">
            <div class="h-16 flex items-center px-6 border-b border-gray-800">
                <span class="text-lg font-semibold">RJ CMS</span>
            </div>
            <nav class="p-4 space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('admin.users.*') ? 'bg-gray-800' : '' }}">Users</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-800">Themes</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-800">Posts</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-800">Pages</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-800">Settings</a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="h-16 bg-white border-b flex items-center justify-between px-6">
                <div class="font-medium">@yield('title', 'Dashboard')</div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">Logout</button>
                    </form>
                </div>
            </header>

            <main class="p-6 flex-1">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

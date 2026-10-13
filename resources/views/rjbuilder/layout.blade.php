<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('RJBuilder')) - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
    <style>
        :root { {!! rj_css_variables() !!} }
        html, body { height: 100%; overflow: hidden; margin: 0; }
        [x-cloak] { display: none !important; }
        .rj-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .rj-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .rj-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 3px; }
        .rj-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.25); }
    </style>
</head>
<body class="font-sans bg-gray-100 text-gray-900 antialiased h-screen overflow-hidden">
    @yield('content')
</body>
</html>
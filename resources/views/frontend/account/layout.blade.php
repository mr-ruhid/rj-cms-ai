<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('My Account')) — {{ rj_get_option('site_name', config('app.name')) }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Figtree', sans-serif; }
        :root { {!! rj_css_variables() !!} }
    </style>
    @stack('styles')
</head>
<body class="h-full text-gray-900 antialiased flex flex-col bg-gray-50">

    <header class="w-full bg-white border-b border-gray-200 py-4 px-6 flex items-center justify-between sticky top-0 z-30">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
            @php $logoMask = rj_logo_mask_url(); @endphp
            @if ($logoMask)
                <div class="h-8 w-28 bg-rj-dark-1" style="-webkit-mask-image: url('{{ $logoMask }}'); mask-image: url('{{ $logoMask }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center;"></div>
            @else
                <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rj-purple to-rj-pink">{{ rj_get_option('site_name', 'RJ CMS') }}</span>
            @endif
        </a>
        <div class="flex items-center gap-4">
            <a href="{{ url('/') }}" class="text-sm font-medium text-gray-500 hover:text-rj-purple transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                {{ __('Back to Site') }}
            </a>
        </div>
    </header>

    <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">

            <aside class="w-full lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">

                    <div class="p-6 border-b border-gray-100 bg-gradient-to-b from-gray-50 to-white text-center">
                        <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center text-white text-2xl font-bold mb-3 shadow-md">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 truncate">{{ auth()->user()->name }}</h2>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>

                        @if(auth()->user()->kyc_status === 'approved')
                            <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-[10px] font-bold uppercase tracking-wider border border-green-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ __('Verified') }}
                            </span>
                        @endif
                    </div>

                    <nav class="p-3 space-y-1">
                        <a href="{{ route('frontend.account.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('frontend.account.index') ? 'bg-rj-purple/10 text-rj-purple' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"/></svg>
                            {{ __('Dashboard') }}
                        </a>

                        <a href="{{ route('frontend.kyc.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('frontend.kyc.*') ? 'bg-rj-purple/10 text-rj-purple' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                            {{ __('Identity Verification') }}
                            @if(auth()->user()->kyc_status === 'pending')
                                <span class="w-2 h-2 rounded-full bg-yellow-400 ml-auto"></span>
                            @elseif(auth()->user()->kyc_status === 'approved')
                                <svg class="w-4 h-4 text-green-500 ml-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>

                        <a href="{{ route('frontend.account.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('frontend.account.settings') ? 'bg-rj-purple/10 text-rj-purple' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ __('Account Settings') }}
                        </a>

                        @php
                            $pluginMenuItems = rj_apply_filters('user_panel_menu', []) ?? [];
                        @endphp

                        @if(!empty($pluginMenuItems) && is_array($pluginMenuItems))
                            <div class="pt-2 mt-2 border-t border-gray-100">
                                @foreach($pluginMenuItems as $item)
                                    <a href="{{ $item['url'] ?? '#' }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition">
                                        @if(!empty($item['icon']))
                                            {!! $item['icon'] !!}
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        @endif
                                        {{ $item['label'] ?? '' }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </nav>

                    <div class="p-3 border-t border-gray-100 bg-gray-50 mt-auto">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                {{ __('Logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <main class="flex-1 min-w-0">
                @yield('account_content')
            </main>

        </div>
    </div>

    @stack('scripts')
</body>
</html>

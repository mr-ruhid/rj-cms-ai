<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', rj_get_option('site_name', config('app.name')))</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            900: '#4c1d95',
                        },
                        dark: '#0F172A'
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 flex flex-col min-h-screen selection:bg-brand-500 selection:text-white">

    @include('theme::partials.header')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('theme::partials.footer')

    @if(auth()->check() && auth()->user()->email === 'admin@admin.com')
        @php
            $currentStyle = rj_get_option('velvet_header_style', 'style-1');
        @endphp
        <div x-data="{ open: false }" class="fixed bottom-6 left-6 z-[9999] font-sans">
            <button @click="open = !open" class="bg-brand-600 text-white p-3.5 rounded-full shadow-2xl flex items-center justify-center hover:bg-brand-900 transition-all hover:scale-105 active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </button>
            <div x-show="open" x-cloak x-transition class="absolute bottom-16 left-0 bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 w-64">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Header Style') }}</h3>
                    <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full uppercase tracking-wider font-bold">{{ __('Admin') }}</span>
                </div>
                <form action="{{ url('/admin/theme/velvet-aura/header') }}" method="POST" class="space-y-2">
                    @csrf
                    <button type="submit" name="style" value="style-1" class="w-full text-left px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $currentStyle === 'style-1' ? 'bg-brand-50 text-brand-600 ring-1 ring-brand-500' : 'text-slate-600 hover:bg-slate-50' }}">
                        Style 1 (Luxury Dark)
                    </button>
                    <button type="submit" name="style" value="style-2" class="w-full text-left px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $currentStyle === 'style-2' ? 'bg-brand-50 text-brand-600 ring-1 ring-brand-500' : 'text-slate-600 hover:bg-slate-50' }}">
                        Style 2 (Elegant Light)
                    </button>
                    <button type="submit" name="style" value="style-3" class="w-full text-left px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $currentStyle === 'style-3' ? 'bg-brand-50 text-brand-600 ring-1 ring-brand-500' : 'text-slate-600 hover:bg-slate-50' }}">
                        Style 3 (Editorial)
                    </button>
                </form>
            </div>
        </div>
    @endif

    @stack('scripts')
</body>
</html>

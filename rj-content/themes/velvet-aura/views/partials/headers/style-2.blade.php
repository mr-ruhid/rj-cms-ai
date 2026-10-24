@php
    $logoUrl = $customLogo ?? rj_get_option('site_logo');

    if (!$logoUrl) {
        $logoUrl = 'https://raw.githubusercontent.com/rj-nexus/velvet-aura/main/assets/images/velvetlogo_dark.png';
    }

    $allMenus = function_exists('rj_menu_tree') ? rj_menu_tree() : [];
    $menuItems = [];

    foreach ($allMenus as $menu) {
        if (in_array(strtolower($menu['location'] ?? ''), ['header', 'main', 'primary'])) {
            $menuItems = $menu['items'];
            break;
        }
    }

    if (empty($menuItems) && !empty($allMenus)) {
        $menuItems = reset($allMenus)['items'] ?? [];
    }
@endphp

<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition-all duration-300" x-data="{ mobileMenuOpen: false, searchOpen: false }">
    <div class="bg-[#f8f6f0] text-center py-2 px-4 border-b border-slate-200/50">
        <p class="text-[10px] tracking-[0.2em] uppercase font-semibold text-slate-700">
            {{ $announcement ?? rj_get_option('header_announcement', 'Complimentary shipping on all orders over $150') }}
        </p>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 md:h-24">

            <div class="flex-1 flex items-center justify-start">
                <a href="{{ url('/') }}" class="block flex-shrink-0">
                    <img src="{{ $logoUrl }}" alt="{{ rj_get_option('site_name', 'VelvetAura') }}" class="h-8 md:h-10 w-auto object-contain">
                </a>
            </div>

            <nav class="hidden md:flex flex-1 items-center justify-center gap-10">
                @if(!empty($menuItems))
                    @foreach($menuItems as $item)
                        <a href="{{ $item['url'] ?? '#' }}" target="{{ $item['target'] ?? '_self' }}" class="text-[11px] font-bold tracking-[0.15em] uppercase text-slate-800 hover:text-brand-600 transition-colors">
                            {{ $item['label'] ?? '' }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ url('/') }}" class="text-[11px] font-bold tracking-[0.15em] uppercase text-slate-800 hover:text-brand-600 transition-colors">{{ __('Home') }}</a>
                    @if(function_exists('rj_commerce_url'))
                        <a href="{{ rj_commerce_url('shop') }}" class="text-[11px] font-bold tracking-[0.15em] uppercase text-slate-800 hover:text-brand-600 transition-colors">{{ __('Shop') }}</a>
                    @endif
                    <a href="{{ url('/collections') }}" class="text-[11px] font-bold tracking-[0.15em] uppercase text-slate-800 hover:text-brand-600 transition-colors">{{ __('Collections') }}</a>
                @endif
            </nav>

            <div class="flex-1 flex items-center justify-end gap-5 sm:gap-7">
                <button @click="searchOpen = !searchOpen" class="text-slate-800 hover:text-brand-600 transition-colors hidden sm:block">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>

                <a href="{{ route('login') }}" class="text-slate-800 hover:text-brand-600 transition-colors hidden sm:block">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>

                @if(function_exists('rj_commerce_url'))
                    <a href="{{ rj_commerce_url('cart') }}" class="relative text-slate-800 hover:text-brand-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @if(rj_commerce_get_cart_count() > 0)
                            <span class="absolute -top-1.5 -right-2 inline-flex items-center justify-center min-w-[16px] h-[16px] px-1 text-[9px] font-bold text-white bg-brand-600 rounded-full">
                                {{ rj_commerce_get_cart_count() }}
                            </span>
                        @endif
                    </a>
                @endif

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-slate-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="searchOpen" x-cloak x-transition.opacity class="absolute top-full left-0 w-full bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-3xl mx-auto px-4 py-4">
            <form action="{{ url('/search') }}" method="GET" class="relative">
                <input type="text" name="q" placeholder="{{ __('Search for products...') }}" class="w-full bg-transparent border-none text-xl font-light focus:ring-0 text-slate-900 placeholder-slate-400">
                <button type="submit" class="absolute right-0 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </form>
        </div>
    </div>

    <div x-show="mobileMenuOpen" x-cloak x-transition class="md:hidden bg-white border-t border-slate-100 absolute w-full shadow-2xl h-screen">
        <div class="px-6 pt-4 pb-20 space-y-2">
            @if(!empty($menuItems))
                @foreach($menuItems as $item)
                    <a href="{{ $item['url'] ?? '#' }}" target="{{ $item['target'] ?? '_self' }}" class="block py-3 border-b border-slate-50 text-sm font-bold tracking-widest uppercase text-slate-900">
                        {{ $item['label'] ?? '' }}
                    </a>
                @endforeach
            @else
                <a href="{{ url('/') }}" class="block py-3 border-b border-slate-50 text-sm font-bold tracking-widest uppercase text-slate-900">{{ __('Home') }}</a>
                @if(function_exists('rj_commerce_url'))
                    <a href="{{ rj_commerce_url('shop') }}" class="block py-3 border-b border-slate-50 text-sm font-bold tracking-widest uppercase text-slate-900">{{ __('Shop') }}</a>
                @endif
                <a href="{{ url('/collections') }}" class="block py-3 border-b border-slate-50 text-sm font-bold tracking-widest uppercase text-slate-900">{{ __('Collections') }}</a>
            @endif
            <a href="{{ route('login') }}" class="block py-3 text-sm font-bold tracking-widest uppercase text-brand-600">{{ __('Login / Register') }}</a>
        </div>
    </div>
</header>

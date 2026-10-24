@php
    $logoUrl = $customLogo ?? rj_get_option('site_logo');
    if (!$logoUrl) {
        $logoUrl = 'https://raw.githubusercontent.com/rj-nexus/velvet-aura/main/assets/images/velvetlogo.png';
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

<header class="bg-[#111111] text-white relative z-50">
    <div class="bg-black text-center py-2 px-4">
        <p class="text-[10px] tracking-[0.2em] uppercase font-medium text-slate-300">
            {{ rj_get_option('header_announcement', 'Enjoy 2 samples and complimentary shipping with every order.') }}
        </p>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 items-center h-24">

            <div class="flex items-center gap-6 justify-start">
                <button class="text-white hover:text-slate-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <div class="hidden sm:flex items-center gap-2 cursor-pointer text-xs tracking-widest font-medium hover:text-slate-300 transition-colors">
                    <span>$ US</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            <div class="flex justify-center">
                <a href="{{ url('/') }}" class="block relative text-center">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ rj_get_option('site_name', 'VelvetAura') }}" class="h-12 w-auto object-contain brightness-0 invert mx-auto">
                    @else
                        <span class="text-2xl font-black text-white tracking-tighter">{{ rj_get_option('site_name', 'VelvetAura') }}<span class="text-[#d4af37]">.</span></span>
                    @endif
                </a>
            </div>

            <div class="flex items-center gap-6 justify-end">
                <a href="#" class="hidden sm:block text-white hover:text-slate-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </a>
                <a href="{{ route('login') }}" class="hidden sm:block text-white hover:text-slate-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
                @if(function_exists('rj_commerce_url'))
                    <a href="{{ rj_commerce_url('cart') }}" class="relative text-white hover:text-slate-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @if(rj_commerce_get_cart_count() > 0)
                            <span class="absolute -top-1.5 -right-2 inline-flex items-center justify-center min-w-[16px] h-[16px] px-1 text-[9px] font-bold text-black bg-white rounded-full">
                                {{ rj_commerce_get_cart_count() }}
                            </span>
                        @endif
                    </a>
                @endif

                <button class="md:hidden text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <nav class="hidden md:flex items-center justify-center gap-10 pb-6">
            @if(!empty($menuItems))
                @foreach($menuItems as $item)
                    <a href="{{ $item['url'] ?? '#' }}" target="{{ $item['target'] ?? '_self' }}" class="text-[11px] font-semibold tracking-[0.15em] uppercase text-white hover:text-slate-400 transition-colors">
                        {{ $item['label'] ?? '' }}
                    </a>
                @endforeach
            @else
                <a href="{{ url('/') }}" class="text-[11px] font-semibold tracking-[0.15em] uppercase text-white hover:text-slate-400 transition-colors">{{ __('Home') }}</a>
                @if(function_exists('rj_commerce_url'))
                    <a href="{{ rj_commerce_url('shop') }}" class="text-[11px] font-semibold tracking-[0.15em] uppercase text-white hover:text-slate-400 transition-colors">{{ __('Shop') }}</a>
                @endif
                <a href="{{ url('/about') }}" class="text-[11px] font-semibold tracking-[0.15em] uppercase text-white hover:text-slate-400 transition-colors">{{ __('About') }}</a>
            @endif
            <a href="#" class="text-[11px] font-semibold tracking-[0.15em] uppercase text-slate-400 italic hover:text-white transition-colors">{{ __('Need Help ?') }}</a>
        </nav>
    </div>
</header>

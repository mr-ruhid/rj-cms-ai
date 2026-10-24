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

<header class="bg-[#faf9f6] border-b border-[#e8e4db] relative z-50" x-data="{ mobileMenuOpen: false }">
    <div class="bg-[#1a1a1a] text-[#d4af37] text-center py-2 px-4">
        <p class="text-[10px] tracking-[0.3em] uppercase font-bold">
            {{ rj_get_option('header_announcement', 'Complimentary Worldwide Express Shipping') }}
        </p>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 md:h-24">

            <div class="flex-1 flex items-center justify-start gap-8">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-slate-900 hover:text-brand-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <nav class="hidden md:flex items-center gap-8">
                    @if(!empty($menuItems))
                        @foreach($menuItems as $item)
                            <a href="{{ $item['url'] ?? '#' }}" target="{{ $item['target'] ?? '_self' }}" class="text-[11px] font-bold tracking-[0.2em] uppercase text-slate-900 hover:text-[#d4af37] transition-colors">
                                {{ $item['label'] ?? '' }}
                            </a>
                        @endforeach
                    @else
                        @if(function_exists('rj_commerce_url'))
                            <a href="{{ rj_commerce_url('shop') }}" class="text-[11px] font-bold tracking-[0.2em] uppercase text-slate-900 hover:text-[#d4af37] transition-colors">{{ __('Shop') }}</a>
                        @endif
                        <a href="{{ url('/collections') }}" class="text-[11px] font-bold tracking-[0.2em] uppercase text-slate-900 hover:text-[#d4af37] transition-colors">{{ __('New Arrivals') }}</a>
                    @endif
                </nav>
            </div>

            <div class="flex-shrink-0 flex items-center justify-center">
                <a href="{{ url('/') }}" class="block">
                    <img src="{{ $logoUrl }}" alt="{{ rj_get_option('site_name', 'VelvetAura') }}" class="h-10 md:h-14 w-auto object-contain">
                </a>
            </div>

            <div class="flex-1 flex items-center justify-end gap-6">
                <button class="text-slate-900 hover:text-[#d4af37] transition-colors hidden sm:block">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>

                <a href="{{ route('login') }}" class="text-slate-900 hover:text-[#d4af37] transition-colors hidden sm:block">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>

                @if(function_exists('rj_commerce_url'))
                    <a href="{{ rj_commerce_url('cart') }}" class="relative text-slate-900 hover:text-[#d4af37] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @if(rj_commerce_get_cart_count() > 0)
                            <span class="absolute -top-1.5 -right-2 inline-flex items-center justify-center min-w-[16px] h-[16px] px-1 text-[9px] font-bold text-white bg-[#1a1a1a] rounded-full">
                                {{ rj_commerce_get_cart_count() }}
                            </span>
                        @endif
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen" x-cloak x-transition.opacity class="md:hidden bg-[#faf9f6] border-t border-[#e8e4db] absolute w-full shadow-2xl">
        <div class="px-6 pt-4 pb-8 space-y-1">
            @if(!empty($menuItems))
                @foreach($menuItems as $item)
                    <a href="{{ $item['url'] ?? '#' }}" target="{{ $item['target'] ?? '_self' }}" class="block py-4 border-b border-[#e8e4db] text-sm font-bold tracking-[0.2em] uppercase text-slate-900">
                        {{ $item['label'] ?? '' }}
                    </a>
                @endforeach
            @else
                @if(function_exists('rj_commerce_url'))
                    <a href="{{ rj_commerce_url('shop') }}" class="block py-4 border-b border-[#e8e4db] text-sm font-bold tracking-[0.2em] uppercase text-slate-900">{{ __('Shop') }}</a>
                @endif
                <a href="{{ url('/collections') }}" class="block py-4 border-b border-[#e8e4db] text-sm font-bold tracking-[0.2em] uppercase text-slate-900">{{ __('New Arrivals') }}</a>
            @endif
            <a href="{{ route('login') }}" class="block py-4 text-sm font-bold tracking-[0.2em] uppercase text-[#d4af37]">{{ __('Login / Register') }}</a>
        </div>
    </div>
</header>

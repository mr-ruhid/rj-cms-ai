@php
    $logoUrl = $customLogo ?? rj_get_option('site_logo');
    if (!$logoUrl) {
        $logoUrl = 'https://raw.githubusercontent.com/rj-nexus/velvet-aura/main/assets/images/velvetlogo.png';
    }

    $allMenus = function_exists('rj_menu_tree') ? rj_menu_tree() : [];
    $shopMenu = [];
    $companyMenu = [];

    foreach ($allMenus as $menu) {
        $loc = strtolower($menu['location'] ?? '');
        if (in_array($loc, ['footer-shop', 'shop', 'footer-1'])) {
            $shopMenu = $menu['items'];
        } elseif (in_array($loc, ['footer-company', 'company', 'footer-3', 'footer-care', 'care'])) {
            $companyMenu = $menu['items'];
        }
    }

    if (empty($shopMenu) && !empty($allMenus)) {
        $shopMenu = reset($allMenus)['items'] ?? [];
    }
@endphp

<footer class="bg-[#f4f1ea] border-t border-[#e5dfd3] pt-16 mt-auto font-sans relative overflow-hidden">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 pb-16 border-b border-[#e5dfd3]">

            <div class="lg:col-span-4 space-y-6">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ rj_get_option('site_name', 'VelvetAura') }}" class="h-12 w-auto object-contain">
                @else
                    <span class="text-3xl font-serif font-bold text-slate-900 tracking-tight">{{ rj_get_option('site_name', 'VelvetAura') }}</span>
                @endif
                <p class="text-sm leading-relaxed text-slate-600 max-w-sm">
                    {{ __('Elevating everyday essentials with a touch of modern luxury. Discover our carefully curated collections.') }}
                </p>
            </div>

            <div class="lg:col-span-2">
                <h4 class="text-[11px] font-bold tracking-[0.2em] uppercase text-slate-900 mb-6">{{ __('Shop') }}</h4>
                <ul class="space-y-3 text-sm text-slate-600">
                    @if(!empty($shopMenu))
                        @foreach($shopMenu as $item)
                            <li><a href="{{ $item['url'] ?? '#' }}" target="{{ $item['target'] ?? '_self' }}" class="hover:text-[#d4af37] transition-colors">{{ $item['label'] ?? '' }}</a></li>
                        @endforeach
                    @else
                        @if(function_exists('rj_commerce_url'))
                            <li><a href="{{ rj_commerce_url('shop') }}" class="hover:text-[#d4af37] transition-colors">{{ __('All Products') }}</a></li>
                        @endif
                        <li><a href="#" class="hover:text-[#d4af37] transition-colors">{{ __('New Arrivals') }}</a></li>
                        <li><a href="#" class="hover:text-[#d4af37] transition-colors">{{ __('Bestsellers') }}</a></li>
                    @endif
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h4 class="text-[11px] font-bold tracking-[0.2em] uppercase text-slate-900 mb-6">{{ __('Company') }}</h4>
                <ul class="space-y-3 text-sm text-slate-600">
                    @if(!empty($companyMenu))
                        @foreach($companyMenu as $item)
                            <li><a href="{{ $item['url'] ?? '#' }}" target="{{ $item['target'] ?? '_self' }}" class="hover:text-[#d4af37] transition-colors">{{ $item['label'] ?? '' }}</a></li>
                        @endforeach
                    @else
                        <li><a href="{{ url('/about') }}" class="hover:text-[#d4af37] transition-colors">{{ __('Our Story') }}</a></li>
                        <li><a href="#" class="hover:text-[#d4af37] transition-colors">{{ __('Careers') }}</a></li>
                        <li><a href="{{ url('/contact') }}" class="hover:text-[#d4af37] transition-colors">{{ __('Contact') }}</a></li>
                    @endif
                </ul>
            </div>

            <div class="lg:col-span-4">
                <h4 class="text-[11px] font-bold tracking-[0.2em] uppercase text-slate-900 mb-6">{{ __('The Velvet Journal') }}</h4>
                <p class="text-sm text-slate-600 mb-4">{{ __('Subscribe to receive our latest stories and product launches.') }}</p>
                <form action="#" method="POST" class="relative">
                    @csrf
                    <input type="email" placeholder="{{ __('Email address') }}" required class="w-full bg-transparent border-b border-slate-400 py-2 pr-10 text-sm text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#d4af37] transition-colors">
                    <button type="submit" class="absolute right-0 bottom-2 text-slate-900 hover:text-[#d4af37] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>
        </div>

        <div class="py-8 flex flex-col md:flex-row items-center justify-between gap-4 relative z-10">
            <p class="text-xs text-slate-500">
                &copy; {{ date('Y') }} {{ rj_get_option('site_name', 'VelvetAura') }}. {{ __('All rights reserved.') }}
            </p>
            <div class="flex items-center gap-6 text-xs text-slate-500 font-medium">
                <a href="#" class="hover:text-slate-900 transition-colors">{{ __('Privacy') }}</a>
                <a href="#" class="hover:text-slate-900 transition-colors">{{ __('Terms') }}</a>
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none flex justify-center opacity-5 pointer-events-none translate-y-1/4">
        <span class="text-[15vw] font-serif font-black text-slate-900 tracking-tighter whitespace-nowrap">
            {{ rj_get_option('site_name', 'VelvetAura') }}
        </span>
    </div>
</footer>

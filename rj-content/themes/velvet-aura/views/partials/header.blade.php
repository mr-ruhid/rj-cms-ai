<header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/60 transition-all duration-300" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">

            <div class="flex-shrink-0 flex items-center">
                <a href="{{ url('/') }}" class="text-2xl font-black text-slate-900 tracking-tighter hover:text-brand-600 transition-colors">
                    @if(rj_get_option('site_logo'))
                        <img src="{{ rj_get_option('site_logo') }}" alt="{{ rj_get_option('site_name', 'Logo') }}" class="h-8 w-auto">
                    @else
                        {{ rj_get_option('site_name', 'VelvetAura') }}<span class="text-brand-600">.</span>
                    @endif
                </a>
            </div>

            <nav class="hidden md:flex items-center space-x-8">
                <a href="{{ url('/') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors">{{ __('Home') }}</a>
                @if(function_exists('rj_commerce_url'))
                    <a href="{{ rj_commerce_url('shop') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors">{{ __('Shop') }}</a>
                @endif
                <a href="{{ url('/about') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors">{{ __('About') }}</a>
                <a href="{{ url('/contact') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors">{{ __('Contact') }}</a>
            </nav>

            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('login') }}" class="text-slate-500 hover:text-slate-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>

                @if(function_exists('rj_commerce_url'))
                    <a href="{{ rj_commerce_url('cart') }}" class="relative text-slate-500 hover:text-slate-900 transition-colors group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        @if(rj_commerce_get_cart_count() > 0)
                            <span class="absolute -top-1.5 -right-2 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-brand-600 rounded-full ring-2 ring-white">
                                {{ rj_commerce_get_cart_count() }}
                            </span>
                        @endif
                    </a>
                @endif
            </div>

            <div class="flex md:hidden items-center gap-4">
                @if(function_exists('rj_commerce_url'))
                    <a href="{{ rj_commerce_url('cart') }}" class="relative text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        @if(rj_commerce_get_cart_count() > 0)
                            <span class="absolute -top-1.5 -right-2 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-brand-600 rounded-full ring-2 ring-white">
                                {{ rj_commerce_get_cart_count() }}
                            </span>
                        @endif
                    </a>
                @endif

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 hover:text-slate-900 focus:outline-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen" x-cloak x-transition.opacity class="md:hidden bg-white border-t border-slate-100 absolute w-full shadow-xl">
        <div class="px-4 pt-2 pb-6 space-y-1">
            <a href="{{ url('/') }}" class="block px-3 py-3 rounded-xl text-base font-semibold text-slate-900 hover:bg-slate-50">{{ __('Home') }}</a>
            @if(function_exists('rj_commerce_url'))
                <a href="{{ rj_commerce_url('shop') }}" class="block px-3 py-3 rounded-xl text-base font-semibold text-slate-900 hover:bg-slate-50">{{ __('Shop') }}</a>
            @endif
            <a href="{{ url('/about') }}" class="block px-3 py-3 rounded-xl text-base font-semibold text-slate-900 hover:bg-slate-50">{{ __('About') }}</a>
            <a href="{{ url('/contact') }}" class="block px-3 py-3 rounded-xl text-base font-semibold text-slate-900 hover:bg-slate-50">{{ __('Contact') }}</a>
            <div class="border-t border-slate-100 mt-2 pt-2">
                <a href="{{ route('login') }}" class="block px-3 py-3 rounded-xl text-base font-semibold text-brand-600 hover:bg-brand-50">{{ __('Login / Register') }}</a>
            </div>
        </div>
    </div>
</header>

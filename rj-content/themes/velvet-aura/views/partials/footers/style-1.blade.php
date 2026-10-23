@php
    $logoUrl = rj_get_option('site_logo');
@endphp

<footer class="bg-[#111111] text-slate-300 pt-20 pb-10 border-t border-white/10 mt-auto font-sans">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 mb-16">

            <div class="space-y-6">
                <a href="{{ url('/') }}" class="block">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ rj_get_option('site_name', 'VelvetAura') }}" class="h-10 w-auto object-contain brightness-0 invert">
                    @else
                        <span class="text-2xl font-black text-white tracking-tighter">{{ rj_get_option('site_name', 'VelvetAura') }}<span class="text-[#d4af37]">.</span></span>
                    @endif
                </a>
                <p class="text-sm leading-relaxed text-slate-400">
                    {{ __('Curating exceptional fragrances and premium essentials for the modern individual. Experience the art of fine living.') }}
                </p>
                <div class="flex items-center gap-4 pt-2">
                    <a href="#" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center text-white hover:border-[#d4af37] hover:text-[#d4af37] transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center text-white hover:border-[#d4af37] hover:text-[#d4af37] transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="text-white text-xs font-bold tracking-[0.2em] uppercase mb-6">{{ __('Shop') }}</h4>
                <ul class="space-y-4 text-sm">
                    @if(function_exists('rj_commerce_url'))
                        <li><a href="{{ rj_commerce_url('shop') }}" class="hover:text-[#d4af37] transition-colors">{{ __('All Products') }}</a></li>
                    @endif
                    <li><a href="#" class="hover:text-[#d4af37] transition-colors">{{ __('New Arrivals') }}</a></li>
                    <li><a href="#" class="hover:text-[#d4af37] transition-colors">{{ __('Bestsellers') }}</a></li>
                    <li><a href="#" class="hover:text-[#d4af37] transition-colors">{{ __('Gift Cards') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white text-xs font-bold tracking-[0.2em] uppercase mb-6">{{ __('Customer Care') }}</h4>
                <ul class="space-y-4 text-sm">
                    <li><a href="{{ url('/contact') }}" class="hover:text-[#d4af37] transition-colors">{{ __('Contact Us') }}</a></li>
                    <li><a href="#" class="hover:text-[#d4af37] transition-colors">{{ __('Shipping & Returns') }}</a></li>
                    <li><a href="#" class="hover:text-[#d4af37] transition-colors">{{ __('FAQ') }}</a></li>
                    <li><a href="#" class="hover:text-[#d4af37] transition-colors">{{ __('Track Order') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white text-xs font-bold tracking-[0.2em] uppercase mb-6">{{ __('Join The Club') }}</h4>
                <p class="text-sm text-slate-400 mb-4">{{ __('Subscribe to receive exclusive offers and early access to new collections.') }}</p>
                <form action="#" method="POST" class="relative">
                    @csrf
                    <input type="email" placeholder="{{ __('Your email address') }}" required class="w-full bg-transparent border-b border-white/20 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[#d4af37] transition-colors">
                    <button type="submit" class="absolute right-0 top-1/2 -translate-y-1/2 text-white hover:text-[#d4af37] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>
        </div>

        <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-xs text-slate-500">
                &copy; {{ date('Y') }} {{ rj_get_option('site_name', 'VelvetAura') }}. {{ __('All rights reserved.') }}
            </p>
            <div class="flex items-center gap-6 text-xs text-slate-500">
                <a href="#" class="hover:text-white transition-colors">{{ __('Privacy Policy') }}</a>
                <a href="#" class="hover:text-white transition-colors">{{ __('Terms of Service') }}</a>
            </div>
        </div>
    </div>
</footer>

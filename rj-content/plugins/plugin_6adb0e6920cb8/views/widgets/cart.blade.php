@if($displayType === 'icon')
    <a href="{{ route('commerce.cart.index') }}" class="relative inline-flex items-center justify-center p-2.5 text-slate-600 hover:text-slate-900 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        @if($itemCount > 0)
            <span class="absolute -top-1.5 -right-1.5 flex items-center justify-center min-w-[20px] h-5 px-1 text-[10px] font-bold text-white bg-rj-purple rounded-full ring-2 ring-white">
                {{ $itemCount > 99 ? '99+' : $itemCount }}
            </span>
        @endif
    </a>
@else
    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-200/50 w-full max-w-sm overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ __('Your Cart') }}</h3>
            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-xs font-bold">{{ $itemCount }}</span>
        </div>

        <div class="p-6 flex-1 max-h-[400px] overflow-y-auto custom-scrollbar">
            @if(empty($cart))
                <div class="text-center py-8">
                    <div class="w-16 h-16 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-4 ring-1 ring-slate-100">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <p class="text-sm text-slate-500">{{ __('Your cart is empty.') }}</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($cart as $key => $item)
                        <div class="flex items-center gap-4 group">
                            <div class="w-16 h-16 flex-shrink-0 bg-slate-50 rounded-xl overflow-hidden ring-1 ring-slate-900/5">
                                @if(!empty($item['image']))
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ $item['url'] }}" class="text-sm font-bold text-slate-900 hover:text-rj-purple transition-colors line-clamp-1">
                                    {{ $item['name'] }}
                                </a>
                                @if(!empty($item['variation_name']))
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $item['variation_name'] }}</div>
                                @endif
                                <div class="text-xs font-semibold text-slate-600 mt-1">
                                    {{ $item['quantity'] }} &times; ${{ number_format($item['price'], 2) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if(!empty($cart))
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-semibold text-slate-600">{{ __('Subtotal') }}</span>
                    <span class="text-lg font-extrabold text-slate-900">${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('commerce.cart.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                        {{ __('View Cart') }}
                    </a>
                    <a href="{{ route('commerce.checkout.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition shadow-lg shadow-slate-900/20">
                        {{ __('Checkout') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
@endif

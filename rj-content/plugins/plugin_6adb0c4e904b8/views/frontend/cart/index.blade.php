@extends('theme::layout')

@section('title', __('Shopping Cart') . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">{{ __('Shopping Cart') }}</h1>
                <p class="text-sm text-slate-500 mt-2">{{ __('Review your items and proceed to checkout.') }}</p>
            </div>
            <a href="{{ route('commerce.shop.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-900 transition-colors bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm hover:shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ __('Continue Shopping') }}
            </a>
        </div>

        @if(empty($cart))
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 p-16 text-center max-w-2xl mx-auto">
                <div class="w-24 h-24 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-6 ring-1 ring-slate-100">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ __('Your cart is empty') }}</h2>
                <p class="text-slate-500 mb-8 max-w-md mx-auto">{{ __('Looks like you haven\'t added anything to your cart yet. Explore our shop and find something you love.') }}</p>
                <a href="{{ route('commerce.shop.index') }}" class="inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-900/20 active:scale-95">
                    {{ __('Start Shopping') }}
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-8 space-y-4">
                    @foreach($cart as $key => $item)
                        <div class="bg-white rounded-3xl border border-slate-200/60 p-4 sm:p-6 shadow-sm flex flex-col sm:flex-row items-center gap-6" id="item-{{ md5($key) }}">

                            <div class="w-28 h-28 flex-shrink-0 bg-slate-50 rounded-2xl overflow-hidden ring-1 ring-slate-900/5">
                                @if(!empty($item['image']))
                                    <img src="{{ $item['image'] }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 text-center sm:text-left">
                                <a href="{{ $item['url'] }}" class="text-lg font-bold text-slate-900 hover:text-slate-700 transition-colors line-clamp-1">{{ $item['name'] }}</a>
                                @if(!empty($item['variation_name']))
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 mt-2">{{ $item['variation_name'] }}</div>
                                @endif
                                <div class="text-sm font-semibold text-slate-500 mt-2">${{ number_format($item['price'], 2) }}</div>
                            </div>

                            <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                                <div class="flex items-center rounded-xl border border-slate-300 bg-white shadow-sm">
                                    <button type="button" onclick="updateCart('{{ $key }}', {{ $item['quantity'] - 1 }})" class="px-3 py-2 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-l-xl transition" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </button>
                                    <input type="number" value="{{ $item['quantity'] }}" onchange="updateCart('{{ $key }}', this.value)" min="1" class="w-14 text-center border-0 focus:ring-0 text-slate-900 font-semibold bg-transparent py-2 px-0">
                                    <button type="button" onclick="updateCart('{{ $key }}', {{ $item['quantity'] + 1 }})" class="px-3 py-2 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>

                                <div class="w-24 text-right font-bold text-slate-900 text-lg">${{ number_format($item['price'] * $item['quantity'], 2) }}</div>

                                <button type="button" onclick="removeCart('{{ $key }}')" class="p-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/60 p-6 sm:p-8 shadow-xl shadow-slate-200/50 sticky top-8">
                    <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-4 mb-6">{{ __('Order Summary') }}</h2>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-slate-600">
                            <span class="font-medium">{{ __('Subtotal') }}</span>
                            <span class="font-bold text-slate-900">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span class="font-medium">{{ __('Shipping') }}</span>
                            <span class="text-sm text-slate-400 font-medium">{{ __('Calculated at checkout') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span class="font-medium">{{ __('Tax') }}</span>
                            <span class="text-sm text-slate-400 font-medium">{{ __('Calculated at checkout') }}</span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-6 mb-8">
                        <div class="flex justify-between items-end">
                            <span class="text-lg font-bold text-slate-900">{{ __('Total') }}</span>
                            <span class="text-3xl font-extrabold text-slate-900">${{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('commerce.checkout.index') }}" class="w-full py-4 rounded-xl bg-slate-900 text-white font-bold text-lg hover:bg-slate-800 transition-all duration-200 shadow-lg shadow-slate-900/20 active:scale-[0.98] flex items-center justify-center gap-2">
                        {{ __('Proceed to Checkout') }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    const csrfToken = '{{ csrf_token() }}';

    function updateCart(key, qty) {
        qty = parseInt(qty);
        if (qty < 1 || isNaN(qty)) return;

        fetch('{{ route('commerce.cart.update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ cart_key: key, quantity: qty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Xəta baş verdi');
            }
        })
        .catch(err => console.error(err));
    }

    function removeCart(key) {
        if (!confirm('{{ __("Are you sure you want to remove this item?") }}')) return;

        fetch('{{ route('commerce.cart.remove') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ cart_key: key })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Xəta baş verdi');
            }
        })
        .catch(err => console.error(err));
    }
</script>
@endsection

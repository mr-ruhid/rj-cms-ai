@extends('theme::layout')

@section('title', __('Shopping Cart') . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')
<div class="bg-slate-50 min-h-screen py-12" x-data="cartPage()">
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

        <div x-show="Object.keys(items).length === 0" class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 p-16 text-center">
            <div class="w-24 h-24 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-6 ring-1 ring-slate-100">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ __('Your cart is empty') }}</h2>
            <p class="text-slate-500 mb-8 max-w-md mx-auto">{{ __('Looks like you haven\'t added anything to your cart yet. Explore our shop and find something you love.') }}</p>
            <a href="{{ route('commerce.shop.index') }}" class="inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-900/20 active:scale-95">
                {{ __('Start Shopping') }}
            </a>
        </div>

        <div x-show="Object.keys(items).length > 0" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <div class="lg:col-span-8 space-y-4">
                <template x-for="[key, item] in Object.entries(items)" :key="key">
                    <div class="bg-white rounded-3xl border border-slate-200/60 p-4 sm:p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col sm:flex-row items-center gap-6">

                        <div class="w-28 h-28 flex-shrink-0 bg-slate-50 rounded-2xl overflow-hidden ring-1 ring-slate-900/5">
                            <template x-if="item.image">
                                <img :src="item.image" class="w-full h-full object-cover" alt="">
                            </template>
                            <template x-if="!item.image">
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </template>
                        </div>

                        <div class="flex-1 text-center sm:text-left">
                            <a :href="item.url" class="text-lg font-bold text-slate-900 hover:text-slate-700 transition-colors line-clamp-1" x-text="item.name"></a>
                            <template x-if="item.variation_name">
                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 mt-2" x-text="item.variation_name"></div>
                            </template>
                            <div class="text-sm font-semibold text-slate-500 mt-2" x-text="formatPrice(item.price)"></div>
                        </div>

                        <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                            <div class="flex items-center rounded-xl border border-slate-300 bg-white shadow-sm">
                                <button type="button" @click="updateQty(key, item.quantity - 1)" class="px-3 py-2 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-l-xl transition disabled:opacity-50 disabled:cursor-not-allowed" :disabled="item.quantity <= 1 || isUpdating[key]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <input type="number" x-model.number="item.quantity" @change="updateQty(key, $event.target.value)" min="1" class="w-14 text-center border-0 focus:ring-0 text-slate-900 font-semibold bg-transparent py-2 px-0" :disabled="isUpdating[key]">
                                <button type="button" @click="updateQty(key, item.quantity + 1)" class="px-3 py-2 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition disabled:opacity-50 disabled:cursor-not-allowed" :disabled="isUpdating[key]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>

                            <div class="w-24 text-right font-bold text-slate-900 text-lg" x-text="formatPrice(item.price * item.quantity)"></div>

                            <button type="button" @click="removeItem(key)" class="p-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors" :disabled="isUpdating[key]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/60 p-6 sm:p-8 shadow-xl shadow-slate-200/50 sticky top-8">
                <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-4 mb-6">{{ __('Order Summary') }}</h2>

                <div class="space-y-4 mb-6">
                    <div class="flex justify-between text-slate-600">
                        <span class="font-medium">{{ __('Subtotal') }}</span>
                        <span class="font-bold text-slate-900" x-text="formatPrice(subtotal)"></span>
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
                        <span class="text-3xl font-extrabold text-slate-900" x-text="formatPrice(subtotal)"></span>
                    </div>
                </div>

                <button type="button" @click="checkout()" class="w-full py-4 rounded-xl bg-slate-900 text-white font-bold text-lg hover:bg-slate-800 transition-all duration-200 shadow-lg shadow-slate-900/20 active:scale-[0.98] flex items-center justify-center gap-2">
                    {{ __('Proceed to Checkout') }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>

                <div class="mt-6 flex items-center justify-center gap-4 text-xs text-slate-500">
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg> {{ __('Secure') }}</span>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg> {{ __('Fast') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function cartPage() {
        return {
            items: @json($cart ?? []),
            subtotal: {{ $subtotal ?? 0 }},
            csrf: '{{ csrf_token() }}',
            isUpdating: {},

            formatPrice(amount) {
                return '$' + parseFloat(amount).toFixed(2);
            },

            calculateSubtotal() {
                let total = 0;
                for (let key in this.items) {
                    total += this.items[key].price * this.items[key].quantity;
                }
                this.subtotal = total;
            },

            async updateQty(key, qty) {
                qty = parseInt(qty);
                if (qty < 1 || isNaN(qty)) return;

                const oldQty = this.items[key].quantity;
                this.items[key].quantity = qty;
                this.calculateSubtotal();
                this.isUpdating[key] = true;

                try {
                    const res = await fetch('{{ route('commerce.cart.update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ cart_key: key, quantity: qty })
                    });

                    const data = await res.json();
                    if (!data.success) throw new Error(data.message);

                } catch (e) {
                    console.error(e);
                    this.items[key].quantity = oldQty;
                    this.calculateSubtotal();
                    alert('{{ __("Failed to update quantity. Please try again.") }}');
                } finally {
                    this.isUpdating[key] = false;
                }
            },

            async removeItem(key) {
                if (!confirm('{{ __("Are you sure you want to remove this item?") }}')) return;

                this.isUpdating[key] = true;
                const itemToRemove = this.items[key];
                delete this.items[key];
                this.calculateSubtotal();

                try {
                    const res = await fetch('{{ route('commerce.cart.remove') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ cart_key: key })
                    });

                    const data = await res.json();
                    if (!data.success) throw new Error(data.message);

                } catch (e) {
                    console.error(e);
                    this.items[key] = itemToRemove;
                    this.calculateSubtotal();
                    alert('{{ __("Failed to remove item. Please try again.") }}');
                } finally {
                    this.isUpdating[key] = false;
                }
            },

            checkout() {
                window.location.href = '{{ route('commerce.checkout.index') }}';
            }
        }
    }
</script>
@endsection

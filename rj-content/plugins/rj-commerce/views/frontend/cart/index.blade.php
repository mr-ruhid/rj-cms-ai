<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Shopping Cart') }} — {{ rj_get_option('site_name', config('app.name')) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style> body { font-family: 'Figtree', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen" x-data="cartPage()">

    <header class="bg-white border-b border-gray-200 py-4 px-6 sticky top-0 z-30 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rj-purple to-rj-pink">
            {{ rj_get_option('site_name', 'RJ CMS') }}
        </a>
        <a href="{{ route('commerce.shop.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">{{ __('Continue Shopping') }}</a>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8">{{ __('Shopping Cart') }}</h1>

        <div x-show="Object.keys(items).length === 0" class="bg-white rounded-2xl border border-gray-100 p-16 text-center shadow-sm">
            <div class="w-20 h-20 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('Your cart is empty') }}</h2>
            <p class="text-gray-500 mb-6">{{ __('Looks like you haven\'t added anything to your cart yet.') }}</p>
            <a href="{{ route('commerce.shop.index') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gray-900 text-white font-medium hover:bg-gray-800 transition">
                {{ __('Start Shopping') }}
            </a>
        </div>

        <div x-show="Object.keys(items).length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            <div class="lg:col-span-2 space-y-4">
                <template x-for="[key, item] in Object.entries(items)" :key="key">
                    <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-6 shadow-sm flex flex-col sm:flex-row items-center gap-6">
                        <div class="w-24 h-24 flex-shrink-0 bg-gray-50 rounded-xl overflow-hidden border border-gray-100">
                            <template x-if="item.image">
                                <img :src="item.image" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!item.image">
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </template>
                        </div>

                        <div class="flex-1 text-center sm:text-left">
                            <a :href="item.url" class="text-lg font-bold text-gray-900 hover:text-rj-purple transition" x-text="item.name"></a>
                            <template x-if="item.variation_name">
                                <div class="text-sm text-gray-500 mt-1" x-text="'Option: ' + item.variation_name"></div>
                            </template>
                            <div class="text-sm font-medium text-gray-500 mt-2" x-text="formatPrice(item.price)"></div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                <button type="button" @click="updateQty(key, item.quantity - 1)" class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-600 transition" :disabled="item.quantity <= 1">-</button>
                                <input type="number" x-model.number="item.quantity" @change="updateQty(key, $event.target.value)" min="1" class="w-12 text-center border-x border-gray-300 py-1.5 text-sm focus:outline-none">
                                <button type="button" @click="updateQty(key, item.quantity + 1)" class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-600 transition">+</button>
                            </div>

                            <div class="w-24 text-right font-bold text-gray-900" x-text="formatPrice(item.price * item.quantity)"></div>

                            <button type="button" @click="removeItem(key)" class="p-2 text-gray-400 hover:text-red-500 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm sticky top-24">
                <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-4 mb-4">{{ __('Order Summary') }}</h2>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>{{ __('Subtotal') }}</span>
                        <span class="font-medium text-gray-900" x-text="formatPrice(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>{{ __('Shipping') }}</span>
                        <span class="text-sm text-gray-500">{{ __('Calculated at checkout') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>{{ __('Tax') }}</span>
                        <span class="text-sm text-gray-500">{{ __('Calculated at checkout') }}</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 mb-6">
                    <div class="flex justify-between items-end">
                        <span class="text-base font-bold text-gray-900">{{ __('Total') }}</span>
                        <span class="text-2xl font-extrabold text-gray-900" x-text="formatPrice(subtotal)"></span>
                    </div>
                </div>

                <button type="button" @click="checkout()" class="w-full py-4 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white font-bold text-sm hover:opacity-90 transition shadow-lg flex items-center justify-center gap-2">
                    {{ __('Proceed to Checkout') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </div>
    </main>

    <script>
        function cartPage() {
            return {
                items: @json($cart),
                subtotal: {{ $subtotal }},
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

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
                    if (qty < 1) return;

                    this.items[key].quantity = qty;
                    this.calculateSubtotal();

                    try {
                        await fetch('{{ route('commerce.cart.update') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ cart_key: key, quantity: qty })
                        });
                    } catch (e) {
                        console.error(e);
                    }
                },

                async removeItem(key) {
                    delete this.items[key];
                    this.calculateSubtotal();

                    try {
                        await fetch('{{ route('commerce.cart.remove') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ cart_key: key })
                        });
                    } catch (e) {
                        console.error(e);
                    }
                },

                checkout() {
                    window.location.href = '{{ route('commerce.checkout.index') }}';
                }
            }
        }
    </script>
</body>
</html>

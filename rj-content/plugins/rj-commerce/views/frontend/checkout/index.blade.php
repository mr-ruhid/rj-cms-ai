<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Checkout') }} — {{ rj_get_option('site_name', config('app.name')) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style> body { font-family: 'Figtree', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen" x-data="{ sameAsBilling: true }">

    <header class="bg-white border-b border-gray-200 py-4 px-6 sticky top-0 z-30 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rj-purple to-rj-pink">
            {{ rj_get_option('site_name', 'RJ CMS') }}
        </a>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            {{ __('Secure Checkout') }}
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">{{ __('Checkout') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Complete your order by providing your details below.') }}</p>
        </div>

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-s-4 border-red-500 text-red-800 px-4 py-3 rounded-e-lg">
                <ul class="list-disc pl-5 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('commerce.checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            @csrf

            <div class="lg:col-span-7 space-y-6">

                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-5">
                    <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">{{ __('Contact Information') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Email Address') }} <span class="text-red-500">*</span></label>
                            <input type="email" name="billing_email" value="{{ old('billing_email', auth()->user()?->email) }}" required class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="billing_phone" value="{{ old('billing_phone') }}" required class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-5">
                    <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">{{ __('Billing Details') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('First Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="billing_first_name" value="{{ old('billing_first_name', auth()->user()?->name) }}" required class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Last Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="billing_last_name" value="{{ old('billing_last_name') }}" required class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Street Address') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="billing_address_1" value="{{ old('billing_address_1') }}" required class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('House number and street name') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('City') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="billing_city" value="{{ old('billing_city') }}" required class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Postal Code') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="billing_postal_code" value="{{ old('billing_postal_code') }}" required class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Country') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="billing_country" value="{{ old('billing_country') }}" required class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h2 class="text-lg font-bold text-gray-900">{{ __('Shipping Details') }}</h2>
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600">
                            <input type="checkbox" x-model="sameAsBilling" class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                            {{ __('Same as billing') }}
                        </label>
                    </div>

                    <div x-show="!sameAsBilling" x-cloak x-collapse class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('First Name') }}</label>
                            <input type="text" name="shipping_first_name" value="{{ old('shipping_first_name') }}" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Last Name') }}</label>
                            <input type="text" name="shipping_last_name" value="{{ old('shipping_last_name') }}" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Street Address') }}</label>
                            <input type="text" name="shipping_address_1" value="{{ old('shipping_address_1') }}" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('City') }}</label>
                            <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Postal Code') }}</label>
                            <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Country') }}</label>
                            <input type="text" name="shipping_country" value="{{ old('shipping_country') }}" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-5">
                    <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">{{ __('Payment Method') }}</h2>

                    <div class="space-y-3">
                        @foreach($paymentMethods as $key => $method)
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                                <input type="radio" name="payment_method" value="{{ $key }}" required class="text-rj-purple focus:ring-rj-purple" {{ $loop->first ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-gray-900">{{ $method }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Order Notes') }} ({{ __('Optional') }})</label>
                        <textarea name="customer_note" rows="3" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('Notes about your order, e.g. special notes for delivery.') }}">{{ old('customer_note') }}</textarea>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-5">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-4 mb-4">{{ __('Your Order') }}</h2>

                    <div class="space-y-4 mb-6 max-h-[40vh] overflow-y-auto pr-2">
                        @foreach($cart as $key => $item)
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 flex-shrink-0 bg-gray-50 rounded-lg overflow-hidden border border-gray-100">
                                    @if($item['image'])
                                        <img src="{{ $item['image'] }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900 line-clamp-1">{{ $item['name'] }}</div>
                                    @if($item['variation_name'])
                                        <div class="text-xs text-gray-500">{{ $item['variation_name'] }}</div>
                                    @endif
                                    <div class="text-xs text-gray-500 mt-1">{{ __('Qty') }}: {{ $item['quantity'] }}</div>
                                </div>
                                <div class="text-sm font-bold text-gray-900">
                                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>{{ __('Subtotal') }}</span>
                            <span class="font-medium text-gray-900">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>{{ __('Tax') }}</span>
                            <span class="font-medium text-gray-900">${{ number_format($taxTotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 text-sm">
                            <span>{{ __('Shipping') }}</span>
                            <span class="font-medium text-gray-900">${{ number_format($shippingTotal, 2) }}</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4 mb-6">
                        <div class="flex justify-between items-end">
                            <span class="text-base font-bold text-gray-900">{{ __('Total') }}</span>
                            <span class="text-2xl font-extrabold text-gray-900">${{ number_format($total, 2) }} <span class="text-sm font-normal text-gray-500">{{ $currency }}</span></span>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white font-bold text-sm hover:opacity-90 transition shadow-lg flex items-center justify-center gap-2">
                        {{ __('Place Order') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>

                    <p class="text-center text-xs text-gray-400 mt-4 flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        {{ __('Your personal data will be used to process your order.') }}
                    </p>
                </div>
            </div>
        </form>
    </main>
</body>
</html>

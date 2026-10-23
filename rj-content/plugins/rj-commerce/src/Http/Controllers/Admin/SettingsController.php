@extends('admin.layouts.app')

@section('title', __('Commerce Settings'))

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
    tab: 'general',
    stripeEnabled: {{ ($options['stripe_enabled'] ?? '0') === '1' ? 'true' : 'false' }},
    paypalEnabled: {{ ($options['paypal_enabled'] ?? '0') === '1' ? 'true' : 'false' }},
    checkoutEnabled: {{ ($options['checkout_enabled'] ?? '0') === '1' ? 'true' : 'false' }},
    razorpayEnabled: {{ ($options['razorpay_enabled'] ?? '0') === '1' ? 'true' : 'false' }},
    cryptoEnabled: {{ ($options['crypto_enabled'] ?? '0') === '1' ? 'true' : 'false' }}
}">

    <div class="flex flex-col lg:flex-row gap-8 items-start">

        <!-- Sol Menyu -->
        <div class="w-full lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Commerce Settings') }}</h2>
                </div>
                <nav class="p-2 space-y-1">
                    <button type="button" @click="tab = 'general'" :class="tab === 'general' ? 'bg-rj-purple/10 text-rj-purple font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition text-start">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        {{ __('General') }}
                    </button>
                    <button type="button" @click="tab = 'payments'" :class="tab === 'payments' ? 'bg-rj-purple/10 text-rj-purple font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition text-start">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        {{ __('Payment Gateways') }}
                    </button>
                    <button type="button" @click="tab = 'shipping'" :class="tab === 'shipping' ? 'bg-rj-purple/10 text-rj-purple font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition text-start">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                        {{ __('Shipping') }}
                    </button>
                    <button type="button" @click="tab = 'tax'" :class="tab === 'tax' ? 'bg-rj-purple/10 text-rj-purple font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition text-start">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        {{ __('Taxes') }}
                    </button>
                    <button type="button" @click="tab = 'api'" :class="tab === 'api' ? 'bg-rj-purple/10 text-rj-purple font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition text-start">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        {{ __('API & Notifications') }}
                    </button>
                </nav>
            </div>
        </div>

        <!-- Əsas Məzmun -->
        <div class="flex-1 min-w-0">
            <form method="POST" action="{{ route('admin.commerce.settings.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="tab" :value="tab">

                <!-- General Tab -->
                <div x-show="tab === 'general'" x-cloak class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ __('Store Information') }}</h2>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Basic settings for your store.') }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Store Name') }}</label>
                                <input type="text" name="store_name" value="{{ $options['store_name'] }}" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Currency') }}</label>
                                <select name="currency" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                    <option value="USD" @selected($options['currency'] === 'USD')>USD ($)</option>
                                    <option value="AED" @selected($options['currency'] === 'AED')>AED (د.إ)</option>
                                    <option value="EUR" @selected($options['currency'] === 'EUR')>EUR (€)</option>
                                    <option value="GBP" @selected($options['currency'] === 'GBP')>GBP (£)</option>
                                    <option value="AZN" @selected($options['currency'] === 'AZN')>AZN (₼)</option>
                                    <option value="INR" @selected($options['currency'] === 'INR')>INR (₹)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Weight Unit') }}</label>
                                <select name="weight_unit" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                    <option value="kg" @selected($options['weight_unit'] === 'kg')>{{ __('Kilograms (kg)') }}</option>
                                    <option value="g" @selected($options['weight_unit'] === 'g')>{{ __('Grams (g)') }}</option>
                                    <option value="lbs" @selected($options['weight_unit'] === 'lbs')>{{ __('Pounds (lbs)') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Tab -->
                <div x-show="tab === 'payments'" x-cloak class="space-y-6">

                    <!-- Stripe -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#635BFF]/10 text-[#635BFF] flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M13.479 9.883c-1.626-.604-2.512-1.067-2.512-1.803 0-.622.511-.977 1.423-.977 1.667 0 3.379.642 4.558 1.22l.666-4.111c-.935-.446-2.847-1.177-5.246-1.177-1.69 0-3.21.44-4.363 1.283-1.192.87-1.844 2.124-1.844 3.612 0 3.193 2.47 4.447 6.152 5.684 1.677.566 2.345 1.044 2.345 1.802 0 .7-.6 1.134-1.679 1.134-1.878 0-4.148-.82-5.546-1.54l-.69 4.238c1.31.66 3.666 1.354 6.163 1.354 1.786 0 3.382-.4 4.564-1.225 1.258-.87 1.947-2.193 1.947-3.764 0-3.295-2.5-4.547-6.138-5.763z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">{{ __('Stripe') }} <span class="text-xs font-normal text-gray-500">(UAE & Global)</span></h3>
                                    <p class="text-xs text-gray-500">{{ __('Accept credit cards securely.') }}</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="stripe_enabled" value="0">
                                <input type="checkbox" name="stripe_enabled" value="1" x-model="stripeEnabled" @checked($options['stripe_enabled'] === '1') class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-rj-purple transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>
                        <div x-show="stripeEnabled" x-collapse class="pt-4 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Publishable Key') }}</label>
                                <input type="text" name="stripe_pk" value="{{ $options['stripe_pk'] }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Secret Key') }}</label>
                                <input type="password" name="stripe_sk" value="{{ $options['stripe_sk'] }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                            </div>
                        </div>
                    </div>

                    <!-- PayPal -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#00457C]/10 text-[#00457C] flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .923-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.777-4.471z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">{{ __('PayPal') }} <span class="text-xs font-normal text-gray-500">(UAE & Global)</span></h3>
                                    <p class="text-xs text-gray-500">{{ __('Fast and secure online payments.') }}</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="paypal_enabled" value="0">
                                <input type="checkbox" name="paypal_enabled" value="1" x-model="paypalEnabled" @checked($options['paypal_enabled'] === '1') class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-rj-purple transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>
                        <div x-show="paypalEnabled" x-collapse class="pt-4 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Client ID') }}</label>
                                <input type="text" name="paypal_client_id" value="{{ $options['paypal_client_id'] }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Secret') }}</label>
                                <input type="password" name="paypal_secret" value="{{ $options['paypal_secret'] }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                            </div>
                        </div>
                    </div>

                    <!-- Checkout.com -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">{{ __('Checkout.com') }} <span class="text-xs font-normal text-gray-500">(UAE)</span></h3>
                                    <p class="text-xs text-gray-500">{{ __('Enterprise payment processing.') }}</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="checkout_enabled" value="0">
                                <input type="checkbox" name="checkout_enabled" value="1" x-model="checkoutEnabled" @checked($options['checkout_enabled'] === '1') class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-rj-purple transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>
                        <div x-show="checkoutEnabled" x-collapse class="pt-4 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Public Key') }}</label>
                                <input type="text" name="checkout_pk" value="{{ $options['checkout_pk'] }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Secret Key') }}</label>
                                <input type="password" name="checkout_sk" value="{{ $options['checkout_sk'] }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                            </div>
                        </div>
                    </div>

                    <!-- Razorpay -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">{{ __('Razorpay') }}</h3>
                                    <p class="text-xs text-gray-500">{{ __('Popular in India and Southeast Asia.') }}</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="razorpay_enabled" value="0">
                                <input type="checkbox" name="razorpay_enabled" value="1" x-model="razorpayEnabled" @checked($options['razorpay_enabled'] === '1') class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-rj-purple transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>
                        <div x-show="razorpayEnabled" x-collapse class="pt-4 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Key ID') }}</label>
                                <input type="text" name="razorpay_key" value="{{ $options['razorpay_key'] }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Key Secret') }}</label>
                                <input type="password" name="razorpay_secret" value="{{ $options['razorpay_secret'] }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                            </div>
                        </div>
                    </div>

                    <!-- Crypto -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">{{ __('Crypto Payments') }}</h3>
                                    <p class="text-xs text-gray-500">{{ __('Accept Binance Pay or Coinbase Commerce.') }}</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="crypto_enabled" value="0">
                                <input type="checkbox" name="crypto_enabled" value="1" x-model="cryptoEnabled" @checked($options['crypto_enabled'] === '1') class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-rj-purple transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>
                        <div x-show="cryptoEnabled" x-collapse class="pt-4 border-t border-gray-100 space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Provider') }}</label>
                                <select name="crypto_provider" class="w-full max-w-xs rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                    <option value="binance" @selected($options['crypto_provider'] === 'binance')>Binance Pay</option>
                                    <option value="coinbase" @selected($options['crypto_provider'] === 'coinbase')>Coinbase Commerce</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('API Key') }}</label>
                                <input type="text" name="crypto_api_key" value="{{ $options['crypto_api_key'] }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                            </div>
                        </div>
                    </div>

                    <!-- COD -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">{{ __('Cash on Delivery (COD)') }}</h3>
                                    <p class="text-xs text-gray-500">{{ __('Customers pay when they receive the order.') }}</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="cod_enabled" value="0">
                                <input type="checkbox" name="cod_enabled" value="1" @checked($options['cod_enabled'] === '1') class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-rj-purple transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Shipping Tab -->
                <div x-show="tab === 'shipping'" x-cloak class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">{{ __('Shipping Zones') }}</h3>
                            <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">{{ __('Shipping zones and methods will be available in the next update.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tax Tab -->
                <div x-show="tab === 'tax'" x-cloak class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ __('Tax Settings') }}</h2>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Configure how taxes are calculated at checkout.') }}</p>
                        </div>

                        <label class="flex items-center gap-3 cursor-pointer p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                            <input type="hidden" name="tax_enabled" value="0">
                            <input type="checkbox" name="tax_enabled" value="1" @checked($options['tax_enabled'] === '1') class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple w-5 h-5">
                            <div>
                                <span class="block text-sm font-bold text-gray-900">{{ __('Enable Taxes') }}</span>
                                <span class="block text-xs text-gray-500">{{ __('Calculate and apply taxes to product prices.') }}</span>
                            </div>
                        </label>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default Tax Rate (%)') }}</label>
                            <input type="number" name="default_tax_rate" value="{{ $options['default_tax_rate'] }}" step="0.01" min="0" class="w-full max-w-xs rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <p class="text-xs text-gray-500 mt-1">{{ __('Applied to orders unless overridden by specific rules.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- API Tab -->
                <div x-show="tab === 'api'" x-cloak class="space-y-6">

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">{{ __('External API') }}</h2>
                                <p class="text-sm text-gray-500 mt-1">{{ __('Connect mobile apps or external ERPs to manage orders and products.') }}</p>
                            </div>
                            <a href="{{ route('admin.commerce.api.docs') }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-rj-purple/10 text-rj-purple text-xs font-bold hover:bg-rj-purple/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                {{ __('Read Documentation') }}
                            </a>
                        </div>

                        <label class="flex items-center gap-3 cursor-pointer p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                            <input type="hidden" name="api_enabled" value="0">
                            <input type="checkbox" name="api_enabled" value="1" @checked($options['api_enabled'] === '1') class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple w-5 h-5">
                            <div>
                                <span class="block text-sm font-bold text-gray-900">{{ __('Enable Commerce API') }}</span>
                                <span class="block text-xs text-gray-500">{{ __('Allow external access using the keys below.') }}</span>
                            </div>
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Public Key') }}</label>
                                <input type="text" name="api_public_key" value="{{ $options['api_public_key'] }}" readonly class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-600 text-sm font-mono cursor-not-allowed" placeholder="{{ __('Generate keys...') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Secret Key') }}</label>
                                <input type="text" name="api_secret_key" value="{{ $options['api_secret_key'] }}" readonly class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-600 text-sm font-mono cursor-not-allowed" placeholder="{{ __('Generate keys...') }}">
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                                <input type="hidden" name="generate_keys" value="0">
                                <input type="checkbox" name="generate_keys" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                {{ __('Regenerate API keys (Invalidates old keys)') }}
                            </label>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ __('Order Notifications') }}</h2>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Receive email alerts when new orders arrive or statuses change.') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Notification Emails') }}</label>
                            <textarea name="notification_emails" rows="3" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="admin@example.com&#10;manager@example.com">{{ $options['notification_emails'] }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Separate multiple emails with commas or new lines.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 transition shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Save Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

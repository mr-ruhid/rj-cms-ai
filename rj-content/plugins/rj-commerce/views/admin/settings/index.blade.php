@extends('admin.layouts.app')

@section('title', __('Commerce Settings'))

@section('content')
<div class="max-w-5xl space-y-6" x-data="{ tab: 'general' }">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Commerce Settings') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('Configure your store settings, payment methods, and shipping options.') }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col md:flex-row">

        <div class="w-full md:w-64 bg-gray-50 border-r border-gray-100 flex-shrink-0">
            <nav class="p-3 space-y-1">
                <button @click="tab = 'general'" :class="tab === 'general' ? 'bg-white text-rj-purple shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-start">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    {{ __('General') }}
                </button>
                <button @click="tab = 'payments'" :class="tab === 'payments' ? 'bg-white text-rj-purple shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-start">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    {{ __('Payments') }}
                </button>
                <button @click="tab = 'shipping'" :class="tab === 'shipping' ? 'bg-white text-rj-purple shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-start">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                    {{ __('Shipping') }}
                </button>
                <button @click="tab = 'tax'" :class="tab === 'tax' ? 'bg-white text-rj-purple shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-start">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    {{ __('Taxes') }}
                </button>
            </nav>
        </div>

        <div class="flex-1 p-6 md:p-8">
            <form method="POST" action="{{ route('admin.commerce.settings.update') }}">
                @csrf

                <div x-show="tab === 'general'" class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-2 mb-4">{{ __('General Settings') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Store Name') }}</label>
                                <input type="text" name="store_name" value="{{ rj_get_option('commerce_store_name', rj_get_option('site_name')) }}" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Store Currency') }}</label>
                                <select name="currency" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                                    <option value="USD" @selected(rj_get_option('commerce_currency', 'USD') === 'USD')>USD ($)</option>
                                    <option value="EUR" @selected(rj_get_option('commerce_currency') === 'EUR')>EUR (€)</option>
                                    <option value="AZN" @selected(rj_get_option('commerce_currency') === 'AZN')>AZN (₼)</option>
                                    <option value="TRY" @selected(rj_get_option('commerce_currency') === 'TRY')>TRY (₺)</option>
                                    <option value="GBP" @selected(rj_get_option('commerce_currency') === 'GBP')>GBP (£)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default Weight Unit') }}</label>
                        <select name="weight_unit" class="w-full max-w-xs rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <option value="kg">{{ __('Kilograms (kg)') }}</option>
                            <option value="g">{{ __('Grams (g)') }}</option>
                            <option value="lbs">{{ __('Pounds (lbs)') }}</option>
                        </select>
                    </div>
                </div>

                <div x-show="tab === 'payments'" class="space-y-6" x-cloak>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-2 mb-4">{{ __('Payment Methods') }}</h2>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 rounded-xl border border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ __('Stripe') }}</div>
                                        <div class="text-xs text-gray-500">{{ __('Accept credit cards securely.') }}</div>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="stripe_enabled" value="0">
                                    <input type="checkbox" name="stripe_enabled" value="1" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-rj-purple transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 rounded-xl border border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .923-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.777-4.471z"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ __('PayPal') }}</div>
                                        <div class="text-xs text-gray-500">{{ __('Fast and secure online payments.') }}</div>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="paypal_enabled" value="0">
                                    <input type="checkbox" name="paypal_enabled" value="1" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-rj-purple transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 rounded-xl border border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-50 text-green-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ __('Cash on Delivery') }}</div>
                                        <div class="text-xs text-gray-500">{{ __('Customers pay when they receive the order.') }}</div>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="cod_enabled" value="0">
                                    <input type="checkbox" name="cod_enabled" value="1" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-rj-purple transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'shipping'" class="space-y-6" x-cloak>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-2 mb-4">{{ __('Shipping Zones') }}</h2>
                        <div class="p-6 border-2 border-dashed border-gray-200 rounded-xl text-center">
                            <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h3 class="text-sm font-medium text-gray-900">{{ __('No shipping zones defined') }}</h3>
                            <p class="text-xs text-gray-500 mt-1 mb-4">{{ __('Add shipping zones to define where you deliver and how much it costs.') }}</p>
                            <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">{{ __('Add Zone') }}</button>
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'tax'" class="space-y-6" x-cloak>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-2 mb-4">{{ __('Tax Settings') }}</h2>
                        <label class="flex items-center gap-2 cursor-pointer mb-4">
                            <input type="hidden" name="tax_enabled" value="0">
                            <input type="checkbox" name="tax_enabled" value="1" class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                            <span class="text-sm text-gray-700">{{ __('Enable taxes') }}</span>
                        </label>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Default Tax Rate (%)') }}</label>
                            <input type="number" name="default_tax_rate" value="0" step="0.01" class="w-full max-w-xs rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        </div>
                    </div>
                </div>

                <div class="pt-6 mt-8 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 transition shadow-md">
                        {{ __('Save Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

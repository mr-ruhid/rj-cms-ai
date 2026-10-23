@extends('theme::layout')

@section('title', __('Order Received') . ' — ' . rj_get_option('site_name', config('app.name')))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    <div class="text-center mb-10">
        <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-green-100">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ __('Thank you for your order!') }}</h1>
        <p class="text-gray-500">{{ __('Your order has been received and is now being processed.') }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-sm mb-8">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 pb-6 mb-6">
            <div>
                <div class="text-sm text-gray-500 mb-1">{{ __('Order Number') }}</div>
                <div class="text-lg font-bold text-gray-900">#{{ $order->id }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500 mb-1">{{ __('Date') }}</div>
                <div class="text-sm font-medium text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500 mb-1">{{ __('Total') }}</div>
                <div class="text-lg font-bold text-gray-900">${{ number_format($order->total, 2) }} <span class="text-xs text-gray-500 font-normal">{{ $order->currency }}</span></div>
            </div>
            <div>
                <div class="text-sm text-gray-500 mb-1">{{ __('Payment Method') }}</div>
                <div class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</div>
            </div>
        </div>

        <h2 class="text-base font-bold text-gray-900 mb-4">{{ __('Order Details') }}</h2>

        <div class="space-y-4">
            @foreach($order->items as $item)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center border border-gray-100 text-xs font-bold text-gray-400">
                            x{{ $item->quantity }}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                            @if($item->variation_name)
                                <div class="text-xs text-gray-500">{{ $item->variation_name }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="text-sm font-bold text-gray-900">
                        ${{ number_format($item->total, 2) }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 pt-6 border-t border-gray-100 space-y-2">
            <div class="flex justify-between text-sm text-gray-600">
                <span>{{ __('Subtotal') }}</span>
                <span>${{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>{{ __('Tax') }}</span>
                <span>${{ number_format($order->tax_total, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>{{ __('Shipping') }}</span>
                <span>${{ number_format($order->shipping_total, 2) }}</span>
            </div>
            <div class="flex justify-between text-base font-bold text-gray-900 pt-2">
                <span>{{ __('Total') }}</span>
                <span>${{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="bg-gray-100 rounded-2xl p-6 text-center">
        <h3 class="text-sm font-bold text-gray-900 mb-2">{{ __('Customer Details') }}</h3>
        <p class="text-sm text-gray-600">
            @php $billing = $order->billing_address ?? []; @endphp
            {{ $billing['email'] ?? '' }}<br>
            @if(!empty($billing['address_line_1']))
                {{ $billing['address_line_1'] }}, {{ $billing['city'] }}, {{ $billing['postal_code'] }}, {{ $billing['country'] }}
            @endif
        </p>
    </div>

    <div class="mt-10 flex justify-center gap-4">
        <a href="{{ route('commerce.shop.index') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gray-900 text-white font-medium hover:bg-gray-800 transition">
            {{ __('Back to Shop') }}
        </a>
    </div>

</div>
@endsection

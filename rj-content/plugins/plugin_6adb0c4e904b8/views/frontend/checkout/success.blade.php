@extends('theme::layout')

@section('title', __('Order Received') . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')
<div class="bg-slate-50 min-h-screen py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm ring-1 ring-green-100">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight mb-3">{{ __('Thank you for your order!') }}</h1>
            <p class="text-slate-500">{{ __('Your order has been received and is now being processed.') }}</p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/60 p-6 sm:p-8 shadow-xl shadow-slate-200/50 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-6 border-b border-slate-100 pb-6 mb-6">
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('Order Number') }}</div>
                    <div class="text-lg font-bold text-slate-900">#{{ $order->id }}</div>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('Date') }}</div>
                    <div class="text-sm font-semibold text-slate-900">{{ $order->created_at->format('M d, Y H:i') }}</div>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('Total') }}</div>
                    <div class="text-lg font-bold text-slate-900">${{ number_format($order->total, 2) }} <span class="text-xs text-slate-500 font-medium">{{ $order->currency }}</span></div>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('Payment Method') }}</div>
                    <div class="text-sm font-semibold text-slate-900 capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</div>
                </div>
            </div>

            <h2 class="text-lg font-bold text-slate-900 mb-4">{{ __('Order Details') }}</h2>

            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center border border-slate-200/60 text-sm font-bold text-slate-500">
                                x{{ $item->quantity }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-900">{{ $item->product_name }}</div>
                                @if($item->variation_name)
                                    <div class="text-xs font-medium text-slate-500 mt-0.5">{{ $item->variation_name }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="text-sm font-bold text-slate-900">
                            ${{ number_format($item->total, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 pt-6 border-t border-slate-100 space-y-3">
                <div class="flex justify-between text-sm text-slate-600 font-medium">
                    <span>{{ __('Subtotal') }}</span>
                    <span>${{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-slate-600 font-medium">
                    <span>{{ __('Tax') }}</span>
                    <span>${{ number_format($order->tax_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-slate-600 font-medium">
                    <span>{{ __('Shipping') }}</span>
                    <span>${{ number_format($order->shipping_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-lg font-extrabold text-slate-900 pt-4 border-t border-slate-100 mt-2">
                    <span>{{ __('Total') }}</span>
                    <span>${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-slate-100/50 rounded-2xl p-6 text-center border border-slate-200/60">
            <h3 class="text-sm font-bold text-slate-900 mb-2">{{ __('Customer Details') }}</h3>
            <p class="text-sm text-slate-600 leading-relaxed">
                @php $billing = $order->billing_address ?? []; @endphp
                <span class="font-medium text-slate-900">{{ $billing['email'] ?? '' }}</span><br>
                @if(!empty($billing['address_line_1']))
                    {{ $billing['address_line_1'] }}, {{ $billing['city'] }}, {{ $billing['postal_code'] }}, {{ $billing['country'] }}
                @endif
            </p>
        </div>

        <div class="mt-10 flex justify-center gap-4">
            <a href="{{ route('commerce.shop.index') }}" class="inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-900/20 active:scale-95">
                {{ __('Back to Shop') }}
            </a>
        </div>

    </div>
</div>
@endsection

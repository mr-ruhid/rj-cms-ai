@extends('admin.layouts.app')

@section('title', __('Order Details'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.commerce.orders.index') }}" class="p-2 rounded-lg hover:bg-gray-100 transition text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Order') }} #{{ $order->id }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $order->created_at->format('M d, Y \a\t H:i') }}</p>
            </div>
        </div>
        <div>
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                    'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'completed' => 'bg-green-50 text-green-700 border-green-200',
                    'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                    'refunded' => 'bg-gray-100 text-gray-700 border-gray-200',
                    'failed' => 'bg-red-100 text-red-800 border-red-300',
                ];
                $colorClass = $statusColors[$order->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
            @endphp
            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold uppercase tracking-wider border {{ $colorClass }}">
                {{ $order->status_label }}
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">{{ __('Order Items') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-white text-gray-500 font-medium border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3">{{ __('Product') }}</th>
                                <th class="px-6 py-3 text-right">{{ __('Price') }}</th>
                                <th class="px-6 py-3 text-center">{{ __('Qty') }}</th>
                                <th class="px-6 py-3 text-right">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($item->product && $item->product->featured_image)
                                                <img src="{{ Storage::url($item->product->featured_image) }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                                @if($item->variation_name)
                                                    <div class="text-xs text-gray-500 mt-0.5">{{ $item->variation_name }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-600">${{ number_format($item->price, 2) }}</td>
                                    <td class="px-6 py-4 text-center text-gray-900 font-medium">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-900">${{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    <div class="w-full md:w-1/2 ml-auto space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>{{ __('Subtotal') }}:</span>
                            <span>${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->discount_total > 0)
                            <div class="flex justify-between text-green-600">
                                <span>{{ __('Discount') }}:</span>
                                <span>-${{ number_format($order->discount_total, 2) }}</span>
                            </div>
                        @endif
                        @if($order->shipping_total > 0)
                            <div class="flex justify-between text-gray-600">
                                <span>{{ __('Shipping') }}:</span>
                                <span>${{ number_format($order->shipping_total, 2) }}</span>
                            </div>
                        @endif
                        @if($order->tax_total > 0)
                            <div class="flex justify-between text-gray-600">
                                <span>{{ __('Tax') }}:</span>
                                <span>${{ number_format($order->tax_total, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-gray-900 font-bold text-base pt-2 border-t border-gray-200 mt-2">
                            <span>{{ __('Total') }}:</span>
                            <span>${{ number_format($order->total, 2) }} {{ $order->currency }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($order->customer_note)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">{{ __('Customer Note') }}</h2>
                    <div class="p-4 bg-yellow-50 border border-yellow-100 rounded-lg text-sm text-yellow-800 italic">
                        "{{ $order->customer_note }}"
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-3">{{ __('Order Actions') }}</h2>

                <form method="POST" action="{{ route('admin.commerce.orders.update', $order) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">{{ __('Update Status') }}</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                            <option value="pending" @selected($order->status === 'pending')>{{ __('Pending Payment') }}</option>
                            <option value="processing" @selected($order->status === 'processing')>{{ __('Processing') }}</option>
                            <option value="completed" @selected($order->status === 'completed')>{{ __('Completed') }}</option>
                            <option value="cancelled" @selected($order->status === 'cancelled')>{{ __('Cancelled') }}</option>
                            <option value="refunded" @selected($order->status === 'refunded')>{{ __('Refunded') }}</option>
                            <option value="failed" @selected($order->status === 'failed')>{{ __('Failed') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        {{ __('Update Status') }}
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-3">{{ __('Customer') }}</h2>

                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr($order->user?->name ?? 'G', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $order->user?->name ?? __('Guest Customer') }}</div>
                        <div class="text-xs text-gray-500">{{ $order->user?->email ?? __('No email provided') }}</div>
                    </div>
                </div>

                @if($order->payment_method)
                    <div class="pt-3 border-t border-gray-100 text-sm">
                        <span class="text-gray-500">{{ __('Payment Method') }}:</span>
                        <span class="font-medium text-gray-900">{{ $order->payment_method }}</span>
                    </div>
                @endif

                @if($order->shipping_method)
                    <div class="text-sm">
                        <span class="text-gray-500">{{ __('Shipping Method') }}:</span>
                        <span class="font-medium text-gray-900">{{ $order->shipping_method }}</span>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-3">{{ __('Billing Address') }}</h2>
                @php $billing = $order->billing_address ?? []; @endphp
                @if(!empty($billing))
                    <address class="text-sm text-gray-600 not-italic leading-relaxed">
                        {{ $billing['first_name'] ?? '' }} {{ $billing['last_name'] ?? '' }}<br>
                        @if(!empty($billing['company'])) {{ $billing['company'] }}<br> @endif
                        {{ $billing['address_line_1'] ?? '' }}<br>
                        @if(!empty($billing['address_line_2'])) {{ $billing['address_line_2'] }}<br> @endif
                        {{ $billing['city'] ?? '' }}, {{ $billing['state'] ?? '' }} {{ $billing['postal_code'] ?? '' }}<br>
                        {{ $billing['country'] ?? '' }}<br>
                        @if(!empty($billing['phone'])) <span class="text-gray-900 font-medium mt-1 inline-block">{{ $billing['phone'] }}</span> @endif
                    </address>
                @else
                    <p class="text-sm text-gray-500 italic">{{ __('No billing address provided.') }}</p>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-3">{{ __('Shipping Address') }}</h2>
                @php $shipping = $order->shipping_address ?? []; @endphp
                @if(!empty($shipping))
                    <address class="text-sm text-gray-600 not-italic leading-relaxed">
                        {{ $shipping['first_name'] ?? '' }} {{ $shipping['last_name'] ?? '' }}<br>
                        @if(!empty($shipping['company'])) {{ $shipping['company'] }}<br> @endif
                        {{ $shipping['address_line_1'] ?? '' }}<br>
                        @if(!empty($shipping['address_line_2'])) {{ $shipping['address_line_2'] }}<br> @endif
                        {{ $shipping['city'] ?? '' }}, {{ $shipping['state'] ?? '' }} {{ $shipping['postal_code'] ?? '' }}<br>
                        {{ $shipping['country'] ?? '' }}<br>
                        @if(!empty($shipping['phone'])) <span class="text-gray-900 font-medium mt-1 inline-block">{{ $shipping['phone'] }}</span> @endif
                    </address>

                    @if(!empty($shipping['tracking_number']))
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <span class="block text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">{{ __('Tracking') }}</span>
                            @if(!empty($shipping['shipping_carrier'])) <div class="text-sm font-medium text-gray-900">{{ $shipping['shipping_carrier'] }}</div> @endif
                            <div class="text-sm text-rj-purple font-mono">{{ $shipping['tracking_number'] }}</div>
                            @if(!empty($shipping['tracking_url']))
                                <a href="{{ $shipping['tracking_url'] }}" target="_blank" class="text-xs font-medium text-blue-600 hover:underline mt-1 inline-flex items-center gap-1">
                                    {{ __('Track Package') }}
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            @endif
                        </div>
                    @endif
                @else
                    <p class="text-sm text-gray-500 italic">{{ __('No shipping address provided.') }}</p>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection

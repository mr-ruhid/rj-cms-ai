@extends('theme::layout')

@section('title', __('Shop') . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 flex items-end justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">{{ __('Shop') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Browse our latest products.') }}</p>
        </div>
        <a href="{{ route('commerce.cart.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            {{ __('Cart') }} ({{ array_sum(array_column(session()->get('rj_cart', []), 'quantity')) }})
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            @php
                $imgUrl = null;
                if ($product->featured_image) {
                    if (Str::startsWith($product->featured_image, 'http')) {
                        $imgUrl = $product->featured_image;
                    } elseif (Str::startsWith($product->featured_image, 'media/')) {
                        $imgUrl = asset($product->featured_image);
                    } else {
                        $imgUrl = Storage::url($product->featured_image);
                    }
                }
            @endphp

            <a href="{{ route('commerce.product.show', $product->slug) }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="aspect-[4/5] bg-gray-100 overflow-hidden">
                    @if($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $product->translation()?->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </div>
                <div class="p-5">
                    <div class="text-[11px] font-bold text-rj-purple uppercase tracking-wider mb-1">{{ $product->category?->translation()?->name ?? __('Uncategorized') }}</div>
                    <h3 class="text-base font-bold text-gray-900 line-clamp-2">{{ $product->translation()?->name }}</h3>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                        @if($product->compare_price)
                            <span class="text-sm text-gray-400 line-through">${{ number_format($product->compare_price, 2) }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full py-20 text-center">
                <p class="text-gray-500">{{ __('No products found.') }}</p>
            </div>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $products->links() }}
    </div>
</main>
@endsection

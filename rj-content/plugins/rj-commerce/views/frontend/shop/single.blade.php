@extends('theme::layout')

@section('title', $product->translation()?->name . ' — ' . rj_get_option('site_name', config('app.name')))

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="productPage()">

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('commerce.shop.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            {{ __('Back to Shop') }}
        </a>
        <a href="{{ route('commerce.cart.index') }}" class="relative inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            {{ __('Cart') }} (<span x-text="cartCount"></span>)
        </a>
    </div>

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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        <div>
            <div class="aspect-square bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 mb-4">
                <template x-if="activeImage">
                    <img :src="activeImage" class="w-full h-full object-cover">
                </template>
                <template x-if="!activeImage">
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </template>
            </div>

            @if(!empty($product->gallery) && is_array($product->gallery))
                <div class="grid grid-cols-4 gap-3">
                    @foreach($product->gallery as $galleryImg)
                        @php
                            $gUrl = Str::startsWith($galleryImg, 'http') ? $galleryImg : (Str::startsWith($galleryImg, 'media/') ? asset($galleryImg) : Storage::url($galleryImg));
                        @endphp
                        <button type="button" @click="activeImage = '{{ $gUrl }}'" class="aspect-square rounded-lg overflow-hidden border-2 border-transparent hover:border-gray-900 transition">
                            <img src="{{ $gUrl }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex flex-col">
            <div class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">{{ $product->category?->translation()?->name ?? __('Uncategorized') }}</div>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-4">{{ $product->translation()?->name }}</h1>

            <div class="flex items-center gap-3 mb-6">
                <span class="text-2xl font-bold text-gray-900" x-text="currentPrice"></span>
                @if($product->compare_price)
                    <span class="text-lg text-gray-400 line-through">${{ number_format($product->compare_price, 2) }}</span>
                @endif
            </div>

            <div class="prose prose-sm text-gray-600 mb-8 max-w-none">
                {!! $product->translation()?->description ?? $product->translation()?->short_description !!}
            </div>

            @if($product->variations->isNotEmpty())
                <div class="space-y-4 mb-8">
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Options') }}</h3>

                    <div class="grid grid-cols-1 gap-3">
                        @foreach($product->variations as $index => $variation)
                            <label class="flex items-center justify-between p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="selectedVariation === {{ $variation->id }} ? 'border-gray-900 bg-gray-50 ring-1 ring-gray-900' : ''">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="variation" value="{{ $variation->id }}"
                                           @change="selectVariation({{ $variation->id }}, {{ $variation->price ?: 'null' }})"
                                           class="text-gray-900 focus:ring-gray-900">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $variation->sku ?: __('Option') . ' ' . ($index + 1) }}
                                        @if($variation->price && $variation->price != $product->price)
                                            (+${{ number_format($variation->price - $product->price, 2) }})
                                        @endif
                                    </span>
                                </div>
                                @if($variation->stock_quantity > 0)
                                    <span class="text-xs text-green-600 font-medium">{{ __('In Stock') }}</span>
                                @else
                                    <span class="text-xs text-red-500 font-medium">{{ __('Out of Stock') }}</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-auto pt-6 border-t border-gray-100">
                <button type="button" @click="addToCart()" :disabled="isAdding"
                        class="w-full py-4 rounded-xl bg-gray-900 text-white font-bold text-sm hover:bg-gray-800 transition shadow-lg flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">

                    <svg x-show="isAdding" x-cloak class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                    <svg x-show="!isAdding" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>

                    <span x-text="isAdding ? '{{ __('Adding...') }}' : '{{ __('Add to Cart') }}'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function productPage() {
        return {
            activeImage: '{{ $imgUrl }}',
            basePrice: {{ $product->price }},
            selectedVariation: null,
            currentPrice: '${{ number_format($product->price, 2) }}',
            cartCount: {{ array_sum(array_column(session()->get('rj_cart', []), 'quantity')) }},
            isAdding: false,
            csrf: '{{ csrf_token() }}',

            selectVariation(id, price) {
                this.selectedVariation = id;
                if (price) {
                    this.currentPrice = '$' + parseFloat(price).toFixed(2);
                } else {
                    this.currentPrice = '$' + parseFloat(this.basePrice).toFixed(2);
                }
            },

            async addToCart() {
                if (this.isAdding) return;
                this.isAdding = true;

                try {
                    const res = await fetch('{{ route('commerce.cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: {{ $product->id }},
                            variation_id: this.selectedVariation,
                            quantity: 1
                        })
                    });

                    const data = await res.json();

                    if (data.success) {
                        this.cartCount = data.cart_count;
                        alert(data.message);
                    } else {
                        alert(data.message || 'Error adding to cart.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Network error. Please try again.');
                } finally {
                    this.isAdding = false;
                }
            }
        }
    }
</script>
@endsection

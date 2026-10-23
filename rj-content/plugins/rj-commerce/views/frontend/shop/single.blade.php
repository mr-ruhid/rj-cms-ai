@extends('theme::layout')

@section('title', ($product->translation()?->name ?? $product->name) . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')
<div class="bg-slate-50 min-h-screen py-12" x-data="productPage()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('commerce.shop.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ __('Back to Shop') }}
            </a>
            <a href="{{ route('commerce.cart.index') }}" class="relative inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-xl text-sm font-semibold hover:bg-slate-800 transition shadow-lg shadow-slate-900/20 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                {{ __('Cart') }} (<span x-text="cartCount">{{ array_sum(array_column(session()->get('rj_cart', []), 'quantity')) }}</span>)
            </a>
        </div>

        @php
            $imgUrl = null;
            if ($product->featured_image) {
                if (Str::startsWith($product->featured_image, 'http')) {
                    $imgUrl = $product->featured_image;
                } elseif (Str::startsWith($product->featured_image, 'media/') || Str::startsWith($product->featured_image, '/media/')) {
                    $imgUrl = asset(ltrim($product->featured_image, '/'));
                } else {
                    $imgUrl = Storage::url($product->featured_image);
                }
            }
        @endphp

        <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16 items-start">

            <div class="lg:sticky lg:top-8">
                <div class="aspect-square rounded-3xl bg-white overflow-hidden shadow-xl shadow-slate-200/60 ring-1 ring-slate-900/5 group">
                    @if($imgUrl)
                        <img :src="activeImage" src="{{ $imgUrl }}" alt="{{ $product->translation()?->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </div>

                @if(!empty($product->gallery) && is_array($product->gallery))
                    <div class="mt-4 grid grid-cols-4 gap-4">
                        @foreach($product->gallery as $galleryImg)
                            @php
                                $gUrl = Str::startsWith($galleryImg, 'http') ? $galleryImg : (Str::startsWith($galleryImg, 'media/') || Str::startsWith($galleryImg, '/media/') ? asset(ltrim($galleryImg, '/')) : Storage::url($galleryImg));
                            @endphp
                            <button type="button" @click="activeImage = '{{ $gUrl }}'" class="relative aspect-square rounded-xl overflow-hidden bg-slate-100 ring-1 ring-slate-900/5 hover:ring-slate-900 transition-all" :class="activeImage === '{{ $gUrl }}' ? 'ring-2 ring-slate-900 opacity-100' : 'opacity-60 hover:opacity-100'">
                                <img src="{{ $gUrl }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-10 px-4 sm:px-0 lg:mt-0">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xs font-bold tracking-wider text-slate-500 uppercase">{{ $product->category?->translation()?->name ?? __('Uncategorized') }}</span>
                    @if($product->brand)
                        <span class="text-slate-300">•</span>
                        <span class="text-xs font-bold tracking-wider text-slate-500 uppercase">{{ $product->brand->name }}</span>
                    @endif
                </div>

                <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">{{ $product->translation()?->name ?? $product->name }}</h1>

                <div class="flex items-end gap-3 mb-8">
                    <span class="text-4xl font-bold text-slate-900" x-text="currentPrice">${{ number_format($product->price, 2) }}</span>
                    @if($product->compare_price)
                        <span class="text-xl text-slate-400 line-through mb-1">${{ number_format($product->compare_price, 2) }}</span>
                    @endif
                </div>

                <div class="prose prose-slate prose-sm text-slate-600 mb-8 max-w-none">
                    {!! $product->translation()?->description ?? $product->translation()?->short_description ?? __('No description available.') !!}
                </div>

                <form action="{{ route('commerce.cart.add') }}" method="POST" id="add-to-cart-form" class="mt-8">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    @if($product->variations->isNotEmpty())
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-semibold text-slate-900">{{ __('Available Options') }}</h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($product->variations as $index => $variation)
                                    <label class="relative flex cursor-pointer rounded-2xl border bg-white p-4 shadow-sm focus:outline-none transition-all" :class="selectedVariation === {{ $variation->id }} ? 'border-slate-900 ring-2 ring-slate-900 bg-slate-50' : 'border-slate-200 hover:border-slate-400'">
                                        <input type="radio" name="variation_id" value="{{ $variation->id }}" @change="selectVariation({{ $variation->id }}, {{ $variation->price ?: 'null' }})" class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                        <span class="flex flex-1 items-center justify-between">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-semibold text-slate-900">{{ $variation->sku ?: __('Option') . ' ' . ($index + 1) }}</span>
                                                @if($variation->price && $variation->price != $product->price)
                                                    <span class="mt-1 flex items-center text-xs font-medium text-emerald-600">+${{ number_format($variation->price - $product->price, 2) }}</span>
                                                @endif
                                                @if($variation->stock_quantity <= 0)
                                                    <span class="mt-1 text-xs font-medium text-red-500">{{ __('Out of Stock') }}</span>
                                                @endif
                                            </span>
                                            <svg class="h-5 w-5 text-slate-900 transition-opacity" :class="selectedVariation === {{ $variation->id }} ? 'opacity-100' : 'opacity-0'" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <div class="flex items-center rounded-xl border border-slate-300 bg-white shadow-sm">
                            <button type="button" class="px-4 py-4 text-slate-500 hover:text-slate-900 transition-colors" onclick="document.getElementById('quantity-input').stepDown()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <input type="number" id="quantity-input" name="quantity" value="1" min="1" class="w-16 text-center border-0 focus:ring-0 text-slate-900 font-semibold text-lg bg-transparent -ml-1 -mr-1" style="-moz-appearance: textfield;">
                            <button type="button" class="px-4 py-4 text-slate-500 hover:text-slate-900 transition-colors" onclick="document.getElementById('quantity-input').stepUp()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>

                        <button type="submit" id="add-to-cart-btn" class="flex-1 flex items-center justify-center gap-3 bg-slate-900 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-slate-800 transition-all duration-200 shadow-lg shadow-slate-900/20 active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">
                            <svg id="btn-spinner" class="hidden w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <svg id="btn-cart-icon" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span id="btn-text">{{ __('Add to Cart') }}</span>
                        </button>
                    </div>
                </form>

                <div class="border-t border-slate-200 pt-6 grid grid-cols-2 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-slate-100 rounded-lg text-slate-600">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900">{{ __('Secure Checkout') }}</h4>
                            <p class="text-xs text-slate-500">{{ __('SSL encrypted') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-slate-100 rounded-lg text-slate-600">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900">{{ __('Fast Delivery') }}</h4>
                            <p class="text-xs text-slate-500">{{ __('On all orders') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('add-to-cart-form');
        const btn = document.getElementById('add-to-cart-btn');
        const btnSpinner = document.getElementById('btn-spinner');
        const btnIcon = document.getElementById('btn-cart-icon');
        const btnText = document.getElementById('btn-text');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                btn.disabled = true;
                btnSpinner.classList.remove('hidden');
                btnIcon.classList.add('hidden');
                btnText.textContent = '{{ __("Adding...") }}';

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        window.location.href = "{{ route('commerce.cart.index') }}";
                    } else {
                        alert(data.message || 'Xəta baş verdi');
                        btn.disabled = false;
                        btnSpinner.classList.add('hidden');
                        btnIcon.classList.remove('hidden');
                        btnText.textContent = '{{ __("Add to Cart") }}';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    form.submit();
                });
            });
        }
    });

    function productPage() {
        return {
            activeImage: @json($imgUrl),
            basePrice: Number({{ $product->price ?? 0 }}),
            selectedVariation: {{ $product->variations->isNotEmpty() ? $product->variations->first()->id : 'null' }},
            currentPrice: '$' + Number({{ $product->variations->isNotEmpty() && $product->variations->first()->price ? $product->variations->first()->price : ($product->price ?? 0) }}).toFixed(2),
            cartCount: Number({{ array_sum(array_column(session()->get('rj_cart', []), 'quantity')) }}),

            selectVariation(id, price) {
                this.selectedVariation = id;
                if (price) {
                    this.currentPrice = '$' + parseFloat(price).toFixed(2);
                } else {
                    this.currentPrice = '$' + parseFloat(this.basePrice).toFixed(2);
                }
            }
        }
    }
</script>
@endsection

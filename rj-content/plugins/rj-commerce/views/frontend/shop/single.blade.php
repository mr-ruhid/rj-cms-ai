<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->translation()?->name }} — {{ rj_get_option('site_name', config('app.name')) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style> body { font-family: 'Figtree', sans-serif; } </style>
</head>
<body class="bg-white min-h-screen" x-data="productPage()">

    <header class="bg-white border-b border-gray-200 py-4 px-6 sticky top-0 z-30 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rj-purple to-rj-pink">
            {{ rj_get_option('site_name', 'RJ CMS') }}
        </a>
        <a href="{{ route('commerce.shop.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">{{ __('Back to Shop') }}</a>
    </header>

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

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            <!-- Məhsul Şəkilləri -->
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
                            <button type="button" @click="activeImage = '{{ $gUrl }}'" class="aspect-square rounded-lg overflow-hidden border-2 border-transparent hover:border-rj-purple transition">
                                <img src="{{ $gUrl }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Məhsul Məlumatları -->
            <div class="flex flex-col">
                <div class="text-sm font-bold text-rj-purple uppercase tracking-wider mb-2">{{ $product->category?->translation()?->name ?? __('Uncategorized') }}</div>
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

                <!-- Variasiyalar (Rəng, Ölçü və s.) -->
                @if($product->variations->isNotEmpty())
                    <div class="space-y-4 mb-8">
                        <h3 class="text-sm font-bold text-gray-900">{{ __('Options') }}</h3>

                        {{-- Variasiya seçimləri burada JS ilə idarə olunacaq --}}
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($product->variations as $index => $variation)
                                <label class="flex items-center justify-between p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="selectedVariation === {{ $index }} ? 'border-rj-purple bg-rj-purple/5 ring-1 ring-rj-purple' : ''">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="variation" value="{{ $index }}" @change="selectVariation({{ $index }}, {{ $variation->price ?: 'null' }})" class="text-rj-purple focus:ring-rj-purple">
                                        <span class="text-sm font-medium text-gray-900">
                                            @php
                                                $attrs = [];
                                                foreach($variation->attributes as $attrId => $valId) {
                                                    $attr = $product->variations->first()->product->category; // dummy
                                                }
                                            @endphp
                                            {{-- Sadəcə qiymət fərqini göstəririk --}}
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
                    <button type="button" @click="addToCart()" class="w-full py-4 rounded-xl bg-gray-900 text-white font-bold text-sm hover:bg-gray-800 transition shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        {{ __('Add to Cart') }}
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        function productPage() {
            return {
                activeImage: '{{ $imgUrl }}',
                basePrice: {{ $product->price }},
                selectedVariation: null,
                currentPrice: '${{ number_format($product->price, 2) }}',

                selectVariation(index, price) {
                    this.selectedVariation = index;
                    if (price) {
                        this.currentPrice = '$' + parseFloat(price).toFixed(2);
                    } else {
                        this.currentPrice = '$' + parseFloat(this.basePrice).toFixed(2);
                    }
                },

                addToCart() {
                    alert('{{ __('Cart functionality will be connected soon!') }}');
                }
            }
        }
    </script>
</body>
</html>

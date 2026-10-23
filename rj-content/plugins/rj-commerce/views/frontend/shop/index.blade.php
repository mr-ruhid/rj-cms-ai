<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Shop') }} — {{ rj_get_option('site_name', config('app.name')) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style> body { font-family: 'Figtree', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <header class="bg-white border-b border-gray-200 py-4 px-6 sticky top-0 z-30 flex items-center justify-between">
        <a href="{{ url('/') }}" class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rj-purple to-rj-pink">
            {{ rj_get_option('site_name', 'RJ CMS') }}
        </a>
        <a href="{{ url('/') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">{{ __('Back to Home') }}</a>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">{{ __('Shop') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ __('Browse our latest products.') }}</p>
            </div>
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
</body>
</html>

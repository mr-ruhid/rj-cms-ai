<script src="https://cdn.tailwindcss.com"></script>

<section class="py-24 bg-white font-sans">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">

        @if($title || $subtitle)
            <div class="text-center mb-16">
                @if($subtitle)
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.3em] mb-3">
                        {{ $subtitle }}
                    </p>
                @endif
                @if($title)
                    <h2 class="text-3xl md:text-4xl font-serif text-slate-900 tracking-tight">
                        {{ $title }}
                    </h2>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ $columns }} gap-8 lg:gap-12">
            @forelse($products as $product)
                @php
                    $imgUrl = null;
                    if ($product->featured_image) {
                        if (\Illuminate\Support\Str::startsWith($product->featured_image, 'http')) {
                            $imgUrl = $product->featured_image;
                        } elseif (\Illuminate\Support\Str::startsWith($product->featured_image, 'media/') || \Illuminate\Support\Str::startsWith($product->featured_image, '/media/')) {
                            $imgUrl = asset(ltrim($product->featured_image, '/'));
                        } else {
                            $imgUrl = \Illuminate\Support\Facades\Storage::url($product->featured_image);
                        }
                    }
                @endphp

                <a href="{{ route('commerce.product.show', $product->slug) }}" class="group block">
                    <div class="relative w-full aspect-[3/4] bg-[#f8f6f0] overflow-hidden">
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $product->translation()?->name }}" class="group-hover:scale-110 transition-transform duration-1000 ease-out" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-slate-300">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif

                        @if($product->compare_price)
                            <div class="absolute top-4 left-4 bg-[#d4af37] text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm uppercase tracking-widest z-10">
                                {{ __('Sale') }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 text-center">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">
                            {{ $product->category?->translation()?->name ?? __('Uncategorized') }}
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-[#d4af37] transition-colors duration-300 line-clamp-1 mb-2">
                            {{ $product->translation()?->name }}
                        </h3>
                        <div class="flex items-center justify-center gap-2">
                            <span class="text-sm font-semibold text-slate-900">${{ number_format($product->price, 2) }}</span>
                            @if($product->compare_price)
                                <span class="text-xs text-slate-400 line-through">${{ number_format($product->compare_price, 2) }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-16 text-center bg-[#f8f6f0] border border-[#e5dfd3]">
                    <p class="text-slate-500">{{ __('No products found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

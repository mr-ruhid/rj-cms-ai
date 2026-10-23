<script src="https://cdn.tailwindcss.com"></script>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ $columns }} gap-6">
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

        <a href="{{ route('commerce.product.show', $product->slug) }}" class="group bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 flex flex-col">
            <div class="aspect-[4/5] bg-slate-50 overflow-hidden relative">
                @if($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $product->translation()?->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif

                @if($product->compare_price)
                    <div class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                        {{ __('Sale') }}
                    </div>
                @endif
            </div>

            <div class="p-5 flex-1 flex flex-col">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                    {{ $product->category?->translation()?->name ?? __('Uncategorized') }}
                </div>
                <h3 class="text-base font-bold text-slate-900 line-clamp-2 mb-2 group-hover:text-rj-purple transition-colors">
                    {{ $product->translation()?->name }}
                </h3>

                <div class="mt-auto flex items-center gap-2">
                    <span class="text-lg font-extrabold text-slate-900">${{ number_format($product->price, 2) }}</span>
                    @if($product->compare_price)
                        <span class="text-sm text-slate-400 line-through">${{ number_format($product->compare_price, 2) }}</span>
                    @endif
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full py-12 text-center bg-slate-50 rounded-2xl border border-slate-200 border-dashed">
            <p class="text-slate-500">{{ __('No products found.') }}</p>
        </div>
    @endforelse
</div>

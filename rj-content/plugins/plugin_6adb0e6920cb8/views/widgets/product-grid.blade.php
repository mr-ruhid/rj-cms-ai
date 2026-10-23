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

        <a href="{{ route('commerce.product.show', $product->slug) }}" class="group relative flex flex-col bg-white rounded-2xl border border-slate-200/60 overflow-hidden hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:-translate-y-1 transition-all duration-300">

            <div class="relative w-full aspect-[4/3] bg-slate-50 overflow-hidden flex items-center justify-center p-4">
                @if($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $product->translation()?->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-700 ease-in-out">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif

                @if($product->compare_price)
                    <div class="absolute top-3 left-3 bg-red-500 text-white text-[11px] font-bold px-2.5 py-1 rounded-full shadow-md z-10 uppercase tracking-wide">
                        {{ __('Sale') }}
                    </div>
                @endif
            </div>

            <div class="p-5 flex flex-col flex-1">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                    {{ $product->category?->translation()?->name ?? __('Uncategorized') }}
                </div>
                <h3 class="text-base font-extrabold text-slate-900 line-clamp-2 leading-tight group-hover:text-indigo-600 transition-colors mb-4">
                    {{ $product->translation()?->name }}
                </h3>

                <div class="mt-auto pt-4 flex items-center justify-between border-t border-slate-100">
                    <div class="flex flex-col">
                        @if($product->compare_price)
                            <span class="text-xs text-slate-400 line-through">${{ number_format($product->compare_price, 2) }}</span>
                        @endif
                        <span class="text-lg font-black text-slate-900">${{ number_format($product->price, 2) }}</span>
                    </div>

                    <div class="w-10 h-10 rounded-full bg-slate-50 group-hover:bg-indigo-600 flex items-center justify-center transition-colors duration-300">
                        <svg class="w-5 h-5 text-slate-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full py-12 text-center bg-slate-50 rounded-2xl border border-slate-200 border-dashed">
            <p class="text-slate-500">{{ __('No products found.') }}</p>
        </div>
    @endforelse
</div>

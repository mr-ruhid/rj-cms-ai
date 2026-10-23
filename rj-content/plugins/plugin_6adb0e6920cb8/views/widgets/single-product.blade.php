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

<div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden flex flex-col md:flex-row group">
    <div class="md:w-1/2 aspect-square md:aspect-auto bg-slate-50 relative overflow-hidden">
        @if($imgUrl)
            <img src="{{ $imgUrl }}" alt="{{ $product->translation()?->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center text-slate-300 min-h-[300px]">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        @endif
    </div>

    <div class="md:w-1/2 p-8 flex flex-col justify-center">
        <div class="text-[11px] font-bold text-rj-purple uppercase tracking-wider mb-2">
            {{ $product->category?->translation()?->name ?? __('Uncategorized') }}
        </div>

        <h2 class="text-2xl font-extrabold text-slate-900 mb-3 leading-tight">
            {{ $product->translation()?->name }}
        </h2>

        <div class="flex items-center gap-3 mb-4">
            <span class="text-2xl font-bold text-slate-900">${{ number_format($product->price, 2) }}</span>
            @if($product->compare_price)
                <span class="text-base text-slate-400 line-through">${{ number_format($product->compare_price, 2) }}</span>
            @endif
        </div>

        <div class="text-sm text-slate-600 mb-8 line-clamp-3">
            {!! strip_tags($product->translation()?->short_description ?? $product->translation()?->description) !!}
        </div>

        <a href="{{ route('commerce.product.show', $product->slug) }}" class="inline-flex items-center justify-center w-full px-6 py-3.5 rounded-xl bg-slate-900 text-white font-bold text-sm hover:bg-slate-800 transition-all duration-200 shadow-lg shadow-slate-900/20 active:scale-[0.98]">
            {{ __('View Product') }}
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>
</div>

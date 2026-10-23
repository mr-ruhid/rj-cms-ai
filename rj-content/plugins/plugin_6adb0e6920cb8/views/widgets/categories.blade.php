<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ $columns }} gap-6">
    @forelse($categories as $category)
        @php
            $imgUrl = null;
            if ($category->image) {
                if (\Illuminate\Support\Str::startsWith($category->image, 'http')) {
                    $imgUrl = $category->image;
                } elseif (\Illuminate\Support\Str::startsWith($category->image, 'media/') || \Illuminate\Support\Str::startsWith($category->image, '/media/')) {
                    $imgUrl = asset(ltrim($category->image, '/'));
                } else {
                    $imgUrl = \Illuminate\Support\Facades\Storage::url($category->image);
                }
            }
        @endphp

        <a href="{{ route('commerce.shop.index', ['category' => $category->slug]) }}" class="group bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 flex flex-col items-center justify-center p-8 text-center">
            <div class="w-24 h-24 rounded-full bg-slate-50 overflow-hidden mb-5 ring-1 ring-slate-900/5 group-hover:ring-rj-purple/30 transition-all">
                @if($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $category->translation()?->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                @endif
            </div>

            <h3 class="text-lg font-bold text-slate-900 group-hover:text-rj-purple transition-colors">
                {{ $category->translation()?->name ?? $category->slug }}
            </h3>

            <div class="mt-2 text-sm text-slate-500">
                {{ $category->children->count() }} {{ __('Subcategories') }}
            </div>
        </a>
    @empty
        <div class="col-span-full py-12 text-center bg-slate-50 rounded-3xl border border-slate-200 border-dashed">
            <p class="text-slate-500">{{ __('No categories found.') }}</p>
        </div>
    @endforelse
</div>

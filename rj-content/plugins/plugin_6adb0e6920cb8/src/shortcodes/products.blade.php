<div class="rj-products-grid rj-cols-{{ $atts['columns'] }}" style="display: grid; grid-template-columns: repeat({{ $atts['columns'] }}, minmax(0, 1fr)); gap: 24px; margin: 20px 0;">
    @forelse($products as $product)
        <div class="rj-product-card" style="background: #fff; border: 1px solid #f3f4f6; border-radius: 16px; overflow: hidden; transition: all 0.2s; display: flex; flex-direction: column;">
            <a href="{{ url('/product/' . $product->slug) }}" style="display: block; aspect-ratio: 4/5; overflow: hidden; background: #f9fafb;">
                @if($product->featured_image)
                    @php
                        $imgUrl = Str::startsWith($product->featured_image, 'http') ? $product->featured_image : (Str::startsWith($product->featured_image, 'media/') ? asset($product->featured_image) : Storage::url($product->featured_image));
                    @endphp
                    <img src="{{ $imgUrl }}" alt="{{ $product->translation()?->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #d1d5db;">
                        <svg style="width: 48px; height: 48px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
            </a>
            <div style="padding: 20px; display: flex; flex-direction: column; flex: 1;">
                <div style="font-size: 11px; font-weight: 700; color: #8b5cf6; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                    {{ $product->category?->translation()?->name ?? __('Uncategorized') }}
                </div>
                <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 8px 0; line-height: 1.4;">
                    <a href="{{ url('/product/' . $product->slug) }}" style="color: inherit; text-decoration: none;">{{ $product->translation()?->name }}</a>
                </h3>
                <div style="font-size: 15px; font-weight: 700; color: #111827; margin-top: auto;">
                    ${{ number_format($product->price, 2) }}
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; padding: 40px; text-align: center; background: #f9fafb; border-radius: 12px; color: #6b7280;">
            {{ __('No products found.') }}
        </div>
    @endforelse
</div>

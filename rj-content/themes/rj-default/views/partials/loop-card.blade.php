@php
    $featured = $post->featured_image
        ? (Str::startsWith($post->featured_image, 'http') ? $post->featured_image : asset($post->featured_image))
        : null;
@endphp

<article class="rj-card">
    <a href="{{ route('frontend.posts.show', $post->slug) }}" class="rj-card-link">
        <div class="rj-card-media">
            @if($featured)
                <img src="{{ $featured }}" alt="{{ $post->title }}" loading="lazy">
            @else
                <div class="rj-card-placeholder">
                    <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 4v6h6"/>
                    </svg>
                </div>
            @endif

            @if($post->categories->isNotEmpty())
                <span class="rj-card-cat">{{ $post->categories->first()->name }}</span>
            @endif
        </div>

        <div class="rj-card-body">
            <h3 class="rj-card-title">{{ $post->title }}</h3>

            @if($post->excerpt)
                <p class="rj-card-excerpt">{{ Str::limit($post->excerpt, 120) }}</p>
            @endif

            <div class="rj-card-meta">
                <span class="rj-card-avatar">{{ strtoupper(substr($post->author->name ?? '?', 0, 1)) }}</span>
                <span class="rj-card-author">{{ $post->author->name ?? __('Unknown') }}</span>

                @if($post->published_at)
                    <span class="rj-card-dot">·</span>
                    <span>{{ $post->published_at->translatedFormat('M d, Y') }}</span>
                @endif
            </div>
        </div>
    </a>
</article>

@once
    @push('scripts')
        <style>
            .rj-card {
                background: #fff;
                border-radius: 16px;
                overflow: hidden;
                border: 1px solid #f3f4f6;
                transition: transform .2s, box-shadow .2s, border-color .2s;
                height: 100%;
                display: flex;
            }
            .rj-card:hover {
                transform: translateY(-4px);
                border-color: #e5e7eb;
                box-shadow: 0 20px 40px -20px rgba(124,58,237,.25);
            }
            .rj-card-link {
                display: flex;
                flex-direction: column;
                text-decoration: none;
                color: inherit;
                width: 100%;
            }
            .rj-card-media {
                position: relative;
                aspect-ratio: 16 / 10;
                background: #f3f4f6;
                overflow: hidden;
            }
            .rj-card-media img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                transition: transform .4s;
            }
            .rj-card:hover .rj-card-media img { transform: scale(1.04); }
            .rj-card-placeholder {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #c4b5fd;
                background: linear-gradient(135deg, #f5f3ff, #fce7f3);
            }
            .rj-card-placeholder svg { width: 48px; height: 48px; }
            .rj-card-cat {
                position: absolute;
                top: 14px;
                left: 14px;
                padding: 5px 11px;
                border-radius: 999px;
                background: rgba(255,255,255,.95);
                color: #6d28d9;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .04em;
                backdrop-filter: blur(6px);
            }
            .rj-card-body {
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 10px;
                flex: 1;
            }
            .rj-card-title {
                font-size: 17px;
                font-weight: 700;
                line-height: 1.35;
                color: #111827;
                margin: 0;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                letter-spacing: -.01em;
            }
            .rj-card:hover .rj-card-title { color: #6d28d9; }
            .rj-card-excerpt {
                font-size: 13.5px;
                line-height: 1.55;
                color: #6b7280;
                margin: 0;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .rj-card-meta {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-top: auto;
                padding-top: 12px;
                font-size: 12.5px;
                color: #9ca3af;
            }
            .rj-card-avatar {
                width: 22px;
                height: 22px;
                border-radius: 50%;
                background: linear-gradient(135deg, #8b5cf6, #ec4899);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-size: 10px;
                font-weight: 700;
                flex-shrink: 0;
            }
            .rj-card-author { color: #4b5563; font-weight: 500; }
            .rj-card-dot { opacity: .5; }
        </style>
    @endpush
@endonce

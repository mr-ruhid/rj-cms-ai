@extends('theme::layout')

@section('title', $post->title . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
    <style>
        .rj-post-hero { position: relative; background: #0f172a; color: #fff; padding: 80px 24px 60px; }
        .rj-post-hero-inner { max-width: 760px; margin: 0 auto; }
        .rj-post-hero .rj-post-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 999px; background: rgba(139,92,246,.15); color: #c4b5fd; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 20px; text-decoration: none; }
        .rj-post-hero .rj-post-badge:hover { background: rgba(139,92,246,.25); }
        .rj-post-hero h1 { font-size: 44px; line-height: 1.15; font-weight: 800; margin: 0 0 20px; letter-spacing: -.02em; }
        .rj-post-hero .rj-post-meta { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; font-size: 14px; color: #cbd5e1; }
        .rj-post-hero .rj-post-meta-item { display: inline-flex; align-items: center; gap: 6px; }
        .rj-post-hero .rj-post-meta svg { width: 15px; height: 15px; opacity: .7; }
        .rj-post-hero .rj-post-avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #ec4899); display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; }

        .rj-post-featured { max-width: 1000px; margin: -40px auto 60px; padding: 0 24px; position: relative; z-index: 1; }
        .rj-post-featured img { width: 100%; border-radius: 16px; box-shadow: 0 24px 60px -20px rgba(0,0,0,.35); display: block; }

        .rj-post-body { max-width: 760px; margin: 0 auto; padding: 0 24px 40px; font-size: 17px; line-height: 1.75; color: #1f2937; }
        .rj-post-body > *:first-child { margin-top: 0; }
        .rj-post-body h2 { font-size: 28px; font-weight: 700; margin: 40px 0 16px; letter-spacing: -.01em; }
        .rj-post-body h3 { font-size: 22px; font-weight: 700; margin: 32px 0 14px; }
        .rj-post-body p { margin: 0 0 20px; }
        .rj-post-body a { color: #7c3aed; text-decoration: underline; text-underline-offset: 3px; }
        .rj-post-body a:hover { color: #6d28d9; }
        .rj-post-body img { max-width: 100%; height: auto; border-radius: 12px; margin: 24px 0; }
        .rj-post-body blockquote { margin: 28px 0; padding: 4px 0 4px 24px; border-left: 4px solid #8b5cf6; font-size: 19px; font-style: italic; color: #4b5563; }
        .rj-post-body code { background: #f3f4f6; padding: 2px 7px; border-radius: 4px; font-size: .92em; font-family: ui-monospace, Menlo, monospace; }
        .rj-post-body pre { background: #0f172a; color: #e5e7eb; padding: 20px; border-radius: 12px; overflow-x: auto; margin: 24px 0; }
        .rj-post-body pre code { background: none; padding: 0; color: inherit; }
        .rj-post-body ul, .rj-post-body ol { margin: 0 0 20px; padding-left: 26px; }
        .rj-post-body li { margin-bottom: 8px; }

        .rj-post-tags { max-width: 760px; margin: 0 auto; padding: 0 24px 40px; display: flex; flex-wrap: wrap; gap: 8px; }
        .rj-post-tags a { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 999px; background: #f3f4f6; color: #374151; font-size: 13px; font-weight: 500; text-decoration: none; transition: background .15s; }
        .rj-post-tags a:hover { background: #e5e7eb; }

        .rj-post-related { background: #f9fafb; padding: 60px 24px; border-top: 1px solid #f3f4f6; }
        .rj-post-related-inner { max-width: 1100px; margin: 0 auto; }
        .rj-post-related h2 { font-size: 24px; font-weight: 700; margin: 0 0 28px; text-align: center; }
        .rj-related-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 24px; }

        @media (max-width: 640px) {
            .rj-post-hero { padding: 60px 20px 40px; }
            .rj-post-hero h1 { font-size: 30px; }
            .rj-post-body { font-size: 16px; padding: 0 20px 32px; }
            .rj-post-featured { margin-top: -24px; margin-bottom: 40px; padding: 0 20px; }
        }
    </style>
@endpush

@section('content')

<header class="rj-frontend-header">
    @if($header)
        {!! rj_render_template($header) !!}
    @endif
</header>

<main class="rj-frontend-main">

    <section class="rj-post-hero">
        <div class="rj-post-hero-inner">
            @if($post->categories->isNotEmpty())
                <a href="{{ route('frontend.posts.index', ['term' => $post->categories->first()->slug, 'taxonomy' => 'category']) }}"
                   class="rj-post-badge">
                    {{ $post->categories->first()->name }}
                </a>
            @endif

            <h1>{{ $post->title }}</h1>

            <div class="rj-post-meta">
                <span class="rj-post-meta-item">
                    <span class="rj-post-avatar">{{ strtoupper(substr($post->author->name ?? '?', 0, 1)) }}</span>
                    {{ $post->author->name ?? __('Unknown') }}
                </span>

                @if($post->published_at)
                    <span class="rj-post-meta-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ $post->published_at->translatedFormat('M d, Y') }}
                    </span>
                @endif

                @if($post->view_count > 0)
                    <span class="rj-post-meta-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/></svg>
                        {{ number_format($post->view_count) }}
                    </span>
                @endif
            </div>
        </div>
    </section>

    @if($post->featured_image)
        <div class="rj-post-featured">
            <img src="{{ Str::startsWith($post->featured_image, 'http') ? $post->featured_image : asset($post->featured_image) }}"
                 alt="{{ $post->title }}">
        </div>
    @endif

    <article class="rj-post-body">
        @if($post->hasBlocks())
            {!! rj_render_blocks($post->blocks['blocks'] ?? []) !!}
        @elseif($post->content)
            {!! $post->content !!}
        @elseif($post->excerpt)
            <p>{{ $post->excerpt }}</p>
        @else
            <p style="color:#9ca3af; font-style:italic;">{{ __('No content yet.') }}</p>
        @endif
    </article>

    @if($post->tags->isNotEmpty())
        <div class="rj-post-tags">
            @foreach($post->tags as $tag)
                <a href="{{ route('frontend.posts.index', ['term' => $tag->slug, 'taxonomy' => 'tag']) }}">
                    #{{ $tag->name }}
                </a>
            @endforeach
        </div>
    @endif

    @if($related->isNotEmpty())
        <section class="rj-post-related">
            <div class="rj-post-related-inner">
                <h2>{{ __('Related posts') }}</h2>
                <div class="rj-related-grid">
                    @foreach($related as $relatedPost)
                        @include('theme::partials.loop-card', ['post' => $relatedPost])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</main>

<footer class="rj-frontend-footer">
    @if($footer)
        {!! rj_render_template($footer) !!}
    @endif
</footer>

@endsection

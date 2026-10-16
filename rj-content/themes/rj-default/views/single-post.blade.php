@extends('theme::layout')

@section('title', $post->title . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
    <style>
        .rj-post-hero {
            position: relative;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #4c1d95 100%);
            color: #fff;
            padding: 96px 24px 80px;
            overflow: hidden;
        }
        .rj-post-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(139,92,246,.35), transparent 45%),
                radial-gradient(circle at 80% 70%, rgba(236,72,153,.25), transparent 45%);
            pointer-events: none;
        }
        .rj-post-hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(139,92,246,.4), transparent);
        }
        .rj-post-hero-inner {
            max-width: 820px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .rj-post-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: rgba(255,255,255,.55);
            margin-bottom: 24px;
        }
        .rj-post-breadcrumb a {
            color: rgba(255,255,255,.75);
            text-decoration: none;
            transition: color .15s;
        }
        .rj-post-breadcrumb a:hover { color: #fff; }
        .rj-post-breadcrumb span { opacity: .4; }

        .rj-post-cat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(139,92,246,.2);
            border: 1px solid rgba(139,92,246,.35);
            color: #c4b5fd;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            text-decoration: none;
            margin-bottom: 24px;
            backdrop-filter: blur(8px);
            transition: all .2s;
        }
        .rj-post-cat-pill:hover {
            background: rgba(139,92,246,.35);
            color: #fff;
        }

        .rj-post-hero h1 {
            font-size: 52px;
            line-height: 1.1;
            font-weight: 800;
            margin: 0 0 28px;
            letter-spacing: -.025em;
            background: linear-gradient(135deg, #ffffff 0%, #e9d5ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .rj-post-hero-meta {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,.1);
        }

        .rj-post-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .rj-post-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            color: #fff;
            box-shadow: 0 8px 20px rgba(139,92,246,.4);
            flex-shrink: 0;
        }
        .rj-post-author-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .rj-post-author-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.5);
            font-weight: 600;
        }
        .rj-post-author-name {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }

        .rj-post-hero-divider {
            width: 1px;
            height: 32px;
            background: rgba(255,255,255,.15);
        }

        .rj-post-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: rgba(255,255,255,.75);
            font-weight: 500;
        }
        .rj-post-meta-item svg {
            width: 16px;
            height: 16px;
            opacity: .65;
        }

        .rj-post-featured {
            max-width: 1040px;
            margin: -60px auto 72px;
            padding: 0 24px;
            position: relative;
            z-index: 2;
        }
        .rj-post-featured img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 30px 80px -20px rgba(0,0,0,.5), 0 0 0 1px rgba(255,255,255,.05);
            display: block;
        }

        .rj-post-body {
            max-width: 760px;
            margin: 0 auto;
            padding: 0 24px 48px;
            font-size: 18px;
            line-height: 1.8;
            color: #1f2937;
        }
        .rj-post-body > *:first-child { margin-top: 0; }
        .rj-post-body h2 {
            font-size: 30px;
            font-weight: 700;
            margin: 48px 0 20px;
            letter-spacing: -.015em;
            color: #0f172a;
        }
        .rj-post-body h3 {
            font-size: 23px;
            font-weight: 700;
            margin: 36px 0 16px;
            color: #0f172a;
        }
        .rj-post-body p { margin: 0 0 24px; }
        .rj-post-body a {
            color: #7c3aed;
            text-decoration: underline;
            text-underline-offset: 3px;
            text-decoration-thickness: 1px;
        }
        .rj-post-body a:hover { color: #6d28d9; }
        .rj-post-body img { max-width: 100%; height: auto; border-radius: 14px; margin: 28px 0; }
        .rj-post-body blockquote {
            margin: 32px 0;
            padding: 8px 0 8px 28px;
            border-left: 4px solid #8b5cf6;
            font-size: 20px;
            font-style: italic;
            color: #4b5563;
            line-height: 1.6;
        }
        .rj-post-body code {
            background: #f3f4f6;
            padding: 2px 8px;
            border-radius: 5px;
            font-size: .9em;
            font-family: ui-monospace, Menlo, monospace;
            color: #6d28d9;
        }
        .rj-post-body pre {
            background: #0f172a;
            color: #e5e7eb;
            padding: 24px;
            border-radius: 14px;
            overflow-x: auto;
            margin: 28px 0;
            font-size: 14px;
            line-height: 1.7;
        }
        .rj-post-body pre code { background: none; padding: 0; color: inherit; }
        .rj-post-body ul, .rj-post-body ol { margin: 0 0 24px; padding-left: 28px; }
        .rj-post-body li { margin-bottom: 10px; }

        .rj-post-tags {
            max-width: 760px;
            margin: 0 auto;
            padding: 0 24px 64px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        .rj-post-tags-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #9ca3af;
            font-weight: 700;
            margin-right: 8px;
        }
        .rj-post-tags a {
            display: inline-flex;
            align-items: center;
            padding: 7px 16px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all .15s;
            border: 1px solid transparent;
        }
        .rj-post-tags a:hover {
            background: #ede9fe;
            color: #6d28d9;
            border-color: #c4b5fd;
        }

        .rj-post-related {
            background: #fafafa;
            padding: 80px 24px;
            border-top: 1px solid #f3f4f6;
        }
        .rj-post-related-inner { max-width: 1100px; margin: 0 auto; }
        .rj-post-related-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .rj-post-related-header h2 {
            font-size: 30px;
            font-weight: 800;
            margin: 0 0 8px;
            letter-spacing: -.02em;
            color: #0f172a;
        }
        .rj-post-related-header p {
            font-size: 15px;
            color: #6b7280;
            margin: 0;
        }
        .rj-related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 24px;
        }

        @media (max-width: 768px) {
            .rj-post-hero { padding: 64px 20px 56px; }
            .rj-post-hero h1 { font-size: 32px; margin-bottom: 20px; }
            .rj-post-hero-meta { gap: 16px; padding-top: 20px; }
            .rj-post-hero-divider { display: none; }
            .rj-post-featured { margin-top: -32px; margin-bottom: 48px; padding: 0 20px; }
            .rj-post-body { font-size: 16px; padding: 0 20px 40px; }
            .rj-post-body h2 { font-size: 24px; margin: 36px 0 16px; }
            .rj-post-related { padding: 56px 20px; }
            .rj-post-related-header h2 { font-size: 22px; }
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

            <nav class="rj-post-breadcrumb">
                <a href="{{ url('/') }}">{{ rj_get_option('site_name', __('Home')) }}</a>
                <span>/</span>
                <a href="{{ route('frontend.posts.index') }}">{{ __('Blog') }}</a>
            </nav>

            @if($post->categories->isNotEmpty())
                <a href="{{ route('frontend.posts.index', ['term' => $post->categories->first()->slug, 'taxonomy' => 'category']) }}"
                   class="rj-post-cat-pill">
                    {{ $post->categories->first()->name }}
                </a>
            @endif

            <h1>{{ $post->title }}</h1>

            <div class="rj-post-hero-meta">

                <div class="rj-post-author">
                    <span class="rj-post-avatar">{{ strtoupper(substr($post->author->name ?? '?', 0, 1)) }}</span>
                    <div class="rj-post-author-info">
                        <span class="rj-post-author-label">{{ __('Written by') }}</span>
                        <span class="rj-post-author-name">{{ $post->author->name ?? __('Unknown') }}</span>
                    </div>
                </div>

                @if($post->published_at)
                    <span class="rj-post-hero-divider"></span>
                    <span class="rj-post-meta-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ $post->published_at->translatedFormat('F d, Y') }}
                    </span>
                @endif

                @if($post->view_count > 0)
                    <span class="rj-post-hero-divider"></span>
                    <span class="rj-post-meta-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z"/></svg>
                        {{ number_format($post->view_count) }} {{ __('views') }}
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
            <span class="rj-post-tags-label">{{ __('Tags') }}</span>
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
                <div class="rj-post-related-header">
                    <h2>{{ __('Related posts') }}</h2>
                    <p>{{ __('You might also enjoy these articles') }}</p>
                </div>
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
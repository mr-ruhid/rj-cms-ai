@extends('theme::layout')

@section('title', $archiveTitle . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
    <style>
        .rj-archive-hero {
            position: relative;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #4c1d95 100%);
            color: #fff;
            padding: 64px 24px 56px;
            overflow: hidden;
            text-align: center;
        }
        .rj-archive-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 15% 20%, rgba(139,92,246,.35), transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(236,72,153,.25), transparent 45%);
            pointer-events: none;
        }
        .rj-archive-hero-inner {
            max-width: 720px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .rj-archive-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            background: rgba(139,92,246,.2);
            border: 1px solid rgba(139,92,246,.35);
            color: #c4b5fd;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 18px;
            backdrop-filter: blur(8px);
        }

        .rj-archive-hero h1 {
            font-size: 40px;
            line-height: 1.15;
            font-weight: 800;
            margin: 0 0 14px;
            letter-spacing: -.02em;
            background: linear-gradient(135deg, #ffffff 0%, #e9d5ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .rj-archive-hero p {
            font-size: 16px;
            color: rgba(255,255,255,.7);
            line-height: 1.6;
            margin: 0;
            max-width: 560px;
            margin-left: auto;
            margin-right: auto;
        }

        .rj-archive-toolbar {
            max-width: 1200px;
            margin: 0 auto;
            padding: 28px 24px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .rj-archive-count {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }
        .rj-archive-count b {
            color: #0f172a;
            font-weight: 700;
        }

        .rj-archive-search {
            position: relative;
            flex: 1;
            max-width: 320px;
            min-width: 200px;
        }
        .rj-archive-search input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #fff;
            font-size: 14px;
            font-family: inherit;
            color: #111827;
            outline: none;
            transition: all .15s;
        }
        .rj-archive-search input:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139,92,246,.15);
        }
        .rj-archive-search svg {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: #9ca3af;
            pointer-events: none;
        }

        .rj-archive-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px 72px;
        }

        .rj-archive-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 28px;
        }

        .rj-archive-empty {
            text-align: center;
            padding: 80px 24px;
            color: #6b7280;
        }
        .rj-archive-empty svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            color: #d1d5db;
        }
        .rj-archive-empty h3 {
            font-size: 18px;
            font-weight: 700;
            color: #374151;
            margin: 0 0 8px;
        }
        .rj-archive-empty p {
            font-size: 14px;
            margin: 0 0 20px;
        }
        .rj-archive-empty a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 20px;
            border-radius: 10px;
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: opacity .15s;
        }
        .rj-archive-empty a:hover { opacity: .9; }

        .rj-pagination {
            margin-top: 56px;
            display: flex;
            justify-content: center;
        }
        .rj-pagination nav { display: inline-flex; }
        .rj-pagination .pagination {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .rj-pagination .pagination li a,
        .rj-pagination .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            height: 42px;
            padding: 0 14px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            color: #374151;
            background: #fff;
            border: 1px solid #e5e7eb;
            transition: all .15s;
        }
        .rj-pagination .pagination li a:hover {
            background: #f9fafb;
            border-color: #c4b5fd;
            color: #6d28d9;
        }
        .rj-pagination .pagination li.active span {
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 8px 20px -6px rgba(139,92,246,.5);
        }
        .rj-pagination .pagination li.disabled span {
            color: #d1d5db;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .rj-archive-hero { padding: 44px 20px 40px; }
            .rj-archive-hero h1 { font-size: 28px; }
            .rj-archive-hero p { font-size: 14px; }
            .rj-archive-toolbar { padding: 22px 20px 0; }
            .rj-archive-search { max-width: 100%; }
            .rj-archive-wrap { padding: 24px 20px 48px; }
            .rj-archive-grid { gap: 20px; grid-template-columns: 1fr; }
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

    <section class="rj-archive-hero">
        <div class="rj-archive-hero-inner">
            <span class="rj-archive-label">
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 4v6h6"/></svg>
                {{ __('Blog') }}
            </span>

            <h1>{{ $archiveTitle }}</h1>

            @if(!empty($archiveDescription))
                <p>{{ $archiveDescription }}</p>
            @endif
        </div>
    </section>

    <div class="rj-archive-toolbar">
        <div class="rj-archive-count">
            <b>{{ $posts->total() }}</b> {{ $posts->total() === 1 ? __('article') : __('articles') }}
        </div>

        <form method="GET" action="{{ route('frontend.posts.index') }}" class="rj-archive-search">
            @if(request('term'))
                <input type="hidden" name="term" value="{{ request('term') }}">
                <input type="hidden" name="taxonomy" value="{{ request('taxonomy') }}">
            @endif
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="s" value="{{ request('s') }}" placeholder="{{ __('Search articles...') }}">
        </form>
    </div>

    <div class="rj-archive-wrap">
        @if($posts->isEmpty())
            <div class="rj-archive-empty">
                <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3>{{ __('No articles found') }}</h3>
                <p>{{ __('Try different keywords or check back later.') }}</p>
                <a href="{{ route('frontend.posts.index') }}">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back to all articles') }}
                </a>
            </div>
        @else
            <div class="rj-archive-grid">
                @foreach($posts as $post)
                    @include('theme::partials.loop-card', ['post' => $post])
                @endforeach
            </div>

            @if($posts->hasPages())
                <div class="rj-pagination">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    </div>

</main>

<footer class="rj-frontend-footer">
    @if($footer)
        {!! rj_render_template($footer) !!}
    @endif
</footer>

@endsection
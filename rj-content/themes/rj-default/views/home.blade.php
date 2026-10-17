@extends('theme::layout')

@push('styles')
    <style>
        .rj-home-hero {
            position: relative;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #4c1d95 100%);
            color: #fff;
            padding: 96px 24px 88px;
            overflow: hidden;
            text-align: center;
        }
        .rj-home-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 15% 20%, rgba(139,92,246,.4), transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(236,72,153,.3), transparent 45%);
            pointer-events: none;
        }
        .rj-home-hero-inner {
            max-width: 820px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .rj-home-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 999px;
            background: rgba(139,92,246,.2);
            border: 1px solid rgba(139,92,246,.35);
            color: #c4b5fd;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 28px;
            backdrop-filter: blur(8px);
        }
        .rj-home-label::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #a78bfa;
            box-shadow: 0 0 12px #a78bfa;
            animation: rjPulse 2s ease-in-out infinite;
        }
        @keyframes rjPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .5; transform: scale(.85); }
        }

        .rj-home-hero h1 {
            font-size: 60px;
            line-height: 1.08;
            font-weight: 800;
            margin: 0 0 24px;
            letter-spacing: -.03em;
            background: linear-gradient(135deg, #ffffff 0%, #e9d5ff 60%, #f9a8d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .rj-home-hero p {
            font-size: 19px;
            line-height: 1.6;
            color: rgba(255,255,255,.75);
            margin: 0 auto;
            max-width: 560px;
        }

        .rj-home-cta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        .rj-home-cta a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
        }
        .rj-home-cta-primary {
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            color: #fff;
            box-shadow: 0 12px 32px -8px rgba(139,92,246,.6);
        }
        .rj-home-cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 40px -8px rgba(139,92,246,.75);
        }
        .rj-home-cta-secondary {
            background: rgba(255,255,255,.08);
            color: #fff;
            border: 1px solid rgba(255,255,255,.15);
            backdrop-filter: blur(8px);
        }
        .rj-home-cta-secondary:hover {
            background: rgba(255,255,255,.15);
            border-color: rgba(255,255,255,.3);
        }

        .rj-home-posts {
            max-width: 1200px;
            margin: 0 auto;
            padding: 88px 24px;
        }
        .rj-home-posts-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 44px;
            flex-wrap: wrap;
        }
        .rj-home-posts-header-text h2 {
            font-size: 34px;
            font-weight: 800;
            margin: 0 0 8px;
            letter-spacing: -.02em;
            color: #0f172a;
        }
        .rj-home-posts-header-text p {
            font-size: 15px;
            color: #6b7280;
            margin: 0;
        }
        .rj-home-posts-header-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #e5e7eb;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all .15s;
        }
        .rj-home-posts-header-link:hover {
            border-color: #c4b5fd;
            color: #6d28d9;
            background: #faf5ff;
        }
        .rj-home-posts-header-link svg {
            transition: transform .15s;
        }
        .rj-home-posts-header-link:hover svg {
            transform: translateX(3px);
        }

        .rj-home-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 28px;
        }

        .rj-home-empty {
            max-width: 720px;
            margin: 0 auto;
            padding: 100px 24px;
            text-align: center;
        }
        .rj-home-empty h2 {
            font-size: 34px;
            font-weight: 800;
            margin: 0 0 12px;
            letter-spacing: -.02em;
            color: #0f172a;
        }
        .rj-home-empty p {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.6;
            margin: 0 0 32px;
        }

        @media (max-width: 768px) {
            .rj-home-hero { padding: 64px 20px 56px; }
            .rj-home-hero h1 { font-size: 34px; }
            .rj-home-hero p { font-size: 16px; }
            .rj-home-cta { margin-top: 32px; }
            .rj-home-cta a { padding: 12px 22px; font-size: 14px; }
            .rj-home-posts { padding: 56px 20px; }
            .rj-home-posts-header-text h2 { font-size: 24px; }
            .rj-home-grid { gap: 20px; grid-template-columns: 1fr; }
            .rj-home-empty { padding: 64px 20px; }
            .rj-home-empty h2 { font-size: 24px; }
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

    @if($page)
        @if($page->isBuilderMode() && $page->hasBlocks())
            {!! rj_render_blocks($page->blocks['blocks'] ?? []) !!}
        @elseif($page->content)
            <div style="max-width:1200px;margin:0 auto;padding:48px 24px;">
                {!! $page->content !!}
            </div>
        @endif
    @else

        <section class="rj-home-hero">
            <div class="rj-home-hero-inner">
                <span class="rj-home-label">{{ __('Welcome') }}</span>

                <h1>{{ rj_get_option('site_name', config('app.name')) }}</h1>

                <p>{{ rj_get_option('site_tagline', __('A modern site powered by RJ CMS.')) }}</p>

                <div class="rj-home-cta">
                    <a href="{{ route('frontend.posts.index') }}" class="rj-home-cta-primary">
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        {{ __('Read the blog') }}
                    </a>
                    <a href="{{ url('/admin') }}" class="rj-home-cta-secondary">
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ __('Admin Panel') }}
                    </a>
                </div>
            </div>
        </section>

        @if($latestPosts->isNotEmpty())
            <section class="rj-home-posts">
                <div class="rj-home-posts-header">
                    <div class="rj-home-posts-header-text">
                        <h2>{{ __('Latest articles') }}</h2>
                        <p>{{ __('Fresh from the blog') }}</p>
                    </div>
                    <a href="{{ route('frontend.posts.index') }}" class="rj-home-posts-header-link">
                        {{ __('View all') }}
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="rj-home-grid">
                    @foreach($latestPosts as $post)
                        @include('theme::partials.loop-card', ['post' => $post])
                    @endforeach
                </div>
            </section>
        @else
            <div class="rj-home-empty">
                <h2>{{ rj_get_option('site_name', config('app.name')) }}</h2>
                <p>{{ __('Welcome to your new site. Publish your first post to get started.') }}</p>
                <a href="{{ url('/admin/posts/create') }}" class="rj-home-cta-primary" style="display:inline-flex;align-items:center;gap:8px;padding:14px 28px;border-radius:12px;color:#fff;text-decoration:none;font-weight:600;">
                    {{ __('Create your first post') }}
                </a>
            </div>
        @endif

    @endif

</main>

<footer class="rj-frontend-footer">
    @if($footer)
        {!! rj_render_template($footer) !!}
    @endif
</footer>

@endsection
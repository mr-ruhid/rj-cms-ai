@extends('theme::layout')

@section('title', ($page->title ?? __('Landing')) . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
    <style>
        .rj-landing-hero {
            position: relative;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #4c1d95 100%);
            color: #fff;
            padding: 120px 24px 112px;
            overflow: hidden;
            text-align: center;
        }
        .rj-landing-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 15% 25%, rgba(139,92,246,.4), transparent 45%),
                radial-gradient(circle at 85% 75%, rgba(236,72,153,.3), transparent 45%);
            pointer-events: none;
        }
        .rj-landing-hero-inner {
            max-width: 880px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        .rj-landing-label {
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
            margin-bottom: 32px;
            backdrop-filter: blur(8px);
        }
        .rj-landing-label::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #a78bfa;
            box-shadow: 0 0 12px #a78bfa;
            animation: rjLandingPulse 2s ease-in-out infinite;
        }
        @keyframes rjLandingPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .5; transform: scale(.85); }
        }
        .rj-landing-hero h1 {
            font-size: 64px;
            line-height: 1.05;
            font-weight: 800;
            margin: 0 0 28px;
            letter-spacing: -.035em;
            background: linear-gradient(135deg, #ffffff 0%, #e9d5ff 60%, #f9a8d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .rj-landing-hero p {
            font-size: 20px;
            line-height: 1.6;
            color: rgba(255,255,255,.75);
            margin: 0 auto;
            max-width: 620px;
        }
        .rj-landing-cta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-top: 48px;
            flex-wrap: wrap;
        }
        .rj-landing-cta a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 16px 32px;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
        }
        .rj-landing-cta-primary {
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            color: #fff;
            box-shadow: 0 16px 40px -10px rgba(139,92,246,.6);
        }
        .rj-landing-cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 50px -10px rgba(139,92,246,.75);
        }
        .rj-landing-cta-secondary {
            background: rgba(255,255,255,.08);
            color: #fff;
            border: 1px solid rgba(255,255,255,.15);
            backdrop-filter: blur(8px);
        }
        .rj-landing-cta-secondary:hover {
            background: rgba(255,255,255,.15);
            border-color: rgba(255,255,255,.3);
        }

        .rj-landing-body {
            max-width: 900px;
            margin: 0 auto;
            padding: 96px 24px 112px;
            font-size: 18px;
            line-height: 1.75;
            color: #1f2937;
        }
        .rj-landing-body > *:first-child { margin-top: 0; }
        .rj-landing-body h2 {
            font-size: 34px;
            font-weight: 800;
            margin: 56px 0 20px;
            letter-spacing: -.02em;
            color: #0f172a;
        }
        .rj-landing-body h3 {
            font-size: 24px;
            font-weight: 700;
            margin: 40px 0 16px;
            color: #0f172a;
        }
        .rj-landing-body p { margin: 0 0 24px; }
        .rj-landing-body a {
            color: #7c3aed;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        .rj-landing-body a:hover { color: #6d28d9; }
        .rj-landing-body img { max-width: 100%; height: auto; border-radius: 16px; margin: 32px 0; }
        .rj-landing-body blockquote {
            margin: 40px 0;
            padding: 8px 0 8px 32px;
            border-left: 4px solid #8b5cf6;
            font-size: 21px;
            font-style: italic;
            color: #4b5563;
            line-height: 1.6;
        }
        .rj-landing-body ul, .rj-landing-body ol { margin: 0 0 24px; padding-left: 28px; }
        .rj-landing-body li { margin-bottom: 12px; }

        @media (max-width: 768px) {
            .rj-landing-hero { padding: 80px 20px 72px; }
            .rj-landing-hero h1 { font-size: 38px; }
            .rj-landing-hero p { font-size: 17px; }
            .rj-landing-cta { margin-top: 36px; gap: 10px; }
            .rj-landing-cta a { padding: 14px 24px; font-size: 15px; }
            .rj-landing-body { padding: 64px 20px 80px; font-size: 16px; }
            .rj-landing-body h2 { font-size: 26px; margin: 40px 0 16px; }
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

    <section class="rj-landing-hero">
        <div class="rj-landing-hero-inner">
            <span class="rj-landing-label">{{ $page->excerpt ?: __('Featured') }}</span>

            <h1>{{ $page->title ?? rj_get_option('site_name', config('app.name')) }}</h1>

            <p>{{ $page->excerpt ?: rj_get_option('site_tagline', __('A modern site powered by RJ CMS.')) }}</p>

            <div class="rj-landing-cta">
                <a href="{{ route('frontend.posts.index') }}" class="rj-landing-cta-primary">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    {{ __('Get Started') }}
                </a>
                <a href="#learn-more" class="rj-landing-cta-secondary">
                    {{ __('Learn More') }}
                </a>
            </div>
        </div>
    </section>

    <article class="rj-landing-body" id="learn-more">
        @if($page->hasBlocks())
            {!! rj_render_blocks($page->blocks['blocks'] ?? []) !!}
        @elseif($page->content)
            {!! $page->content !!}
        @else
            <p style="color:#9ca3af; font-style:italic; text-align:center;">{{ __('No content yet.') }}</p>
        @endif
    </article>

</main>

<footer class="rj-frontend-footer">
    @if($footer)
        {!! rj_render_template($footer) !!}
    @endif
</footer>

@endsection
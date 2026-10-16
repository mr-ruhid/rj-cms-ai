@extends('theme::layout')

@section('title', ($page->title ?? '') . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
    <style>
        .rj-page-hero {
            position: relative;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #4c1d95 100%);
            color: #fff;
            padding: 64px 24px 56px;
            overflow: hidden;
            text-align: center;
        }
        .rj-page-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(139,92,246,.35), transparent 45%),
                radial-gradient(circle at 80% 70%, rgba(236,72,153,.25), transparent 45%);
            pointer-events: none;
        }
        .rj-page-hero-inner {
            max-width: 760px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        .rj-page-hero h1 {
            font-size: 42px;
            line-height: 1.15;
            font-weight: 800;
            margin: 0;
            letter-spacing: -.025em;
            background: linear-gradient(135deg, #ffffff 0%, #e9d5ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .rj-page-hero p {
            font-size: 17px;
            color: rgba(255,255,255,.7);
            line-height: 1.6;
            margin: 16px 0 0;
        }

        .rj-page-body {
            max-width: 760px;
            margin: 0 auto;
            padding: 64px 24px 80px;
            font-size: 17px;
            line-height: 1.8;
            color: #1f2937;
        }
        .rj-page-body > *:first-child { margin-top: 0; }
        .rj-page-body h2 {
            font-size: 30px;
            font-weight: 700;
            margin: 48px 0 20px;
            letter-spacing: -.015em;
            color: #0f172a;
        }
        .rj-page-body h3 {
            font-size: 23px;
            font-weight: 700;
            margin: 36px 0 16px;
            color: #0f172a;
        }
        .rj-page-body p { margin: 0 0 24px; }
        .rj-page-body a {
            color: #7c3aed;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        .rj-page-body a:hover { color: #6d28d9; }
        .rj-page-body img { max-width: 100%; height: auto; border-radius: 14px; margin: 28px 0; }
        .rj-page-body blockquote {
            margin: 32px 0;
            padding: 8px 0 8px 28px;
            border-left: 4px solid #8b5cf6;
            font-size: 20px;
            font-style: italic;
            color: #4b5563;
            line-height: 1.6;
        }
        .rj-page-body code {
            background: #f3f4f6;
            padding: 2px 8px;
            border-radius: 5px;
            font-size: .9em;
            font-family: ui-monospace, Menlo, monospace;
            color: #6d28d9;
        }
        .rj-page-body pre {
            background: #0f172a;
            color: #e5e7eb;
            padding: 24px;
            border-radius: 14px;
            overflow-x: auto;
            margin: 28px 0;
            font-size: 14px;
            line-height: 1.7;
        }
        .rj-page-body pre code { background: none; padding: 0; color: inherit; }
        .rj-page-body ul, .rj-page-body ol { margin: 0 0 24px; padding-left: 28px; }
        .rj-page-body li { margin-bottom: 10px; }

        @media (max-width: 768px) {
            .rj-page-hero { padding: 48px 20px 40px; }
            .rj-page-hero h1 { font-size: 30px; }
            .rj-page-hero p { font-size: 15px; }
            .rj-page-body { padding: 48px 20px 64px; font-size: 16px; }
            .rj-page-body h2 { font-size: 24px; margin: 36px 0 16px; }
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

    @if($page->isBuilderMode() && $page->hasBlocks())
        <div class="rj-page-builder-content">
            {!! rj_render_blocks($page->blocks['blocks'] ?? []) !!}
        </div>
    @else
        <section class="rj-page-hero">
            <div class="rj-page-hero-inner">
                <h1>{{ $page->title }}</h1>
                @if($page->excerpt)
                    <p>{{ $page->excerpt }}</p>
                @endif
            </div>
        </section>

        <article class="rj-page-body">
            @if($page->content)
                {!! $page->content !!}
            @elseif($page->excerpt)
                <p>{{ $page->excerpt }}</p>
            @else
                <p style="color:#9ca3af; font-style:italic;">{{ __('No content yet.') }}</p>
            @endif
        </article>
    @endif

</main>

<footer class="rj-frontend-footer">
    @if($footer)
        {!! rj_render_template($footer) !!}
    @endif
</footer>

@endsection
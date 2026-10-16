@extends('theme::layout')

@section('title', ($page->title ?? '') . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
    <style>
        .rj-page-hero { padding: 72px 24px 40px; background: linear-gradient(180deg, #faf5ff 0%, #ffffff 100%); border-bottom: 1px solid #f3f4f6; }
        .rj-page-hero-inner { max-width: 900px; margin: 0 auto; text-align: center; }
        .rj-page-hero h1 { font-size: 42px; line-height: 1.15; font-weight: 800; letter-spacing: -.02em; color: #111827; margin: 0; }

        .rj-page-body { max-width: 760px; margin: 0 auto; padding: 56px 24px 72px; font-size: 17px; line-height: 1.75; color: #1f2937; }
        .rj-page-body > *:first-child { margin-top: 0; }
        .rj-page-body h2 { font-size: 28px; font-weight: 700; margin: 40px 0 16px; letter-spacing: -.01em; }
        .rj-page-body h3 { font-size: 22px; font-weight: 700; margin: 32px 0 14px; }
        .rj-page-body p { margin: 0 0 20px; }
        .rj-page-body a { color: #7c3aed; text-decoration: underline; text-underline-offset: 3px; }
        .rj-page-body a:hover { color: #6d28d9; }
        .rj-page-body img { max-width: 100%; height: auto; border-radius: 12px; margin: 24px 0; }
        .rj-page-body blockquote { margin: 28px 0; padding: 4px 0 4px 24px; border-left: 4px solid #8b5cf6; font-size: 19px; font-style: italic; color: #4b5563; }
        .rj-page-body ul, .rj-page-body ol { margin: 0 0 20px; padding-left: 26px; }
        .rj-page-body li { margin-bottom: 8px; }

        @media (max-width: 640px) {
            .rj-page-hero { padding: 56px 20px 32px; }
            .rj-page-hero h1 { font-size: 30px; }
            .rj-page-body { font-size: 16px; padding: 40px 20px 56px; }
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
        @if($page->hasBlocks())
            {!! rj_render_blocks($page->blocks['blocks'] ?? []) !!}
        @else
            <section class="rj-page-hero">
                <div class="rj-page-hero-inner">
                    <h1>{{ $page->title }}</h1>
                </div>
            </section>

            <article class="rj-page-body">
                @if($page->content)
                    {!! $page->content !!}
                @elseif($page->excerpt)
                    <p>{{ $page->excerpt }}</p>
                @endif
            </article>
        @endif
    @else
        <div style="max-width:720px; margin:80px auto; padding:0 24px; text-align:center;">
            <h1 style="font-size:36px; font-weight:700; margin:0 0 12px; color:#111827;">
                {{ rj_get_option('site_name', config('app.name')) }}
            </h1>
            <p style="font-size:16px; color:#6b7280; line-height:1.6;">
                {{ __('Welcome to your new site.') }}
            </p>
        </div>
    @endif

</main>

<footer class="rj-frontend-footer">
    @if($footer)
        {!! rj_render_template($footer) !!}
    @endif
</footer>

@endsection

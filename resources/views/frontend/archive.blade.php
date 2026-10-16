@extends('frontend.layout')

@section('title', $archiveTitle . ' — ' . rj_get_option('site_name', config('app.name')))

@push('scripts')
    <style>
        .rj-archive-hero {
            background: linear-gradient(135deg, #f5f3ff 0%, #fce7f3 100%);
            padding: 72px 24px 60px;
            text-align: center;
            border-bottom: 1px solid #f3f4f6;
        }
        .rj-archive-hero-inner { max-width: 760px; margin: 0 auto; }
        .rj-archive-hero h1 {
            font-size: 40px;
            font-weight: 800;
            line-height: 1.15;
            margin: 0 0 12px;
            letter-spacing: -.02em;
            color: #111827;
        }
        .rj-archive-hero p {
            font-size: 17px;
            color: #6b7280;
            line-height: 1.6;
            margin: 0;
        }

        .rj-archive-wrap { max-width: 1200px; margin: 0 auto; padding: 56px 24px 72px; }

        .rj-archive-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
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
            font-weight: 600;
            color: #374151;
            margin: 0 0 8px;
        }
        .rj-archive-empty p {
            font-size: 14px;
            margin: 0;
        }

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
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            color: #374151;
            background: #fff;
            border: 1px solid #e5e7eb;
            transition: all .15s;
        }
        .rj-pagination .pagination li a:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }
        .rj-pagination .pagination li.active span {
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: #fff;
            border-color: transparent;
        }
        .rj-pagination .pagination li.disabled span {
            color: #d1d5db;
            cursor: not-allowed;
        }

        @media (max-width: 640px) {
            .rj-archive-hero { padding: 56px 20px 44px; }
            .rj-archive-hero h1 { font-size: 28px; }
            .rj-archive-hero p { font-size: 15px; }
            .rj-archive-wrap { padding: 40px 20px 56px; }
            .rj-archive-grid { gap: 20px; }
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
            <h1>{{ $archiveTitle }}</h1>
            @if(!empty($archiveDescription))
                <p>{{ $archiveDescription }}</p>
            @endif
        </div>
    </section>

    <div class="rj-archive-wrap">
        @if($posts->isEmpty())
            <div class="rj-archive-empty">
                <svg fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3>{{ __('No posts found') }}</h3>
                <p>{{ __('Try different keywords or check back later.') }}</p>
            </div>
        @else
            <div class="rj-archive-grid">
                @foreach($posts as $post)
                    @include('frontend.partials.loop-card', ['post' => $post])
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

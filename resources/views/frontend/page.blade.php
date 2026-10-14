@extends('frontend.layout')

@section('content')

<header class="rj-frontend-header">
    @if($header)
        {!! rj_render_template($header) !!}
    @endif
</header>

<main class="rj-frontend-main">
    @if($page)
        {!! rj_render_template($page) !!}
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
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? rj_get_option('site_name', config('app.name')) }}</title>

    @if(rj_get_option('site_favicon'))
        <link rel="icon" href="{{ rj_get_option('site_favicon') }}">
    @endif

    {!! rj_global_color_css() !!}

    @include('rjbuilder.partials.styles')

    <style>
        body { margin: 0; font-family: 'Inter', system-ui, -apple-system, sans-serif; background: #fff; color: #111827; }
        .rj-frontend { min-height: 100vh; display: flex; flex-direction: column; }
        .rj-frontend > main { flex: 1; }
        .rj-frontend-wrapper { container-type: inline-size; container-name: rj; }
    </style>
</head>
<body>
    <div class="rj-frontend rj-frontend-wrapper">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
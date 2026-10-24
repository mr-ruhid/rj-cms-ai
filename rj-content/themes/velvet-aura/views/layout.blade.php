<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', rj_get_option('site_name', config('app.name')))</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            900: '#4c1d95',
                        },
                        dark: '#0F172A'
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 flex flex-col min-h-screen selection:bg-brand-500 selection:text-white">

    @php
        $resolver = app(\App\Services\ThemeResolver::class);
        $headerTemplate = $resolver->header();
    @endphp

    @if($headerTemplate)
        <header id="rj-builder-header">
            {!! rj_render_template($headerTemplate) !!}
        </header>
    @else
        @include('theme::partials.header')
    @endif

    <main class="flex-grow">
        @yield('content')
    </main>

    @php
        $footerTemplate = $resolver->footer();
    @endphp

    @if($footerTemplate)
        <footer id="rj-builder-footer">
            {!! rj_render_template($footerTemplate) !!}
        </footer>
    @else
        @include('theme::partials.footer')
    @endif

    @stack('scripts')
</body>
</html>

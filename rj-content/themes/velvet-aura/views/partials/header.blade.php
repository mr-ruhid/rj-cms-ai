@php
    $headerStyle = rj_get_option('velvet_header_style', 'style-1');
    $headerView = 'theme::partials.headers.' . $headerStyle;

    if (!view()->exists($headerView)) {
        $headerView = 'theme::partials.headers.style-1';
    }
@endphp

@include($headerView)

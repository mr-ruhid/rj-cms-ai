@php
    $footerStyle = rj_get_option('velvet_footer_style', 'style-1');
    $footerView = 'theme::partials.footers.' . $footerStyle;

    if (!view()->exists($footerView)) {
        $footerView = 'theme::partials.footers.style-1';
    }
@endphp

@include($footerView)

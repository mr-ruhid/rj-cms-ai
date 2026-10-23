<?php

if (!function_exists('rj_commerce_url')) {
    function rj_commerce_url(string $type): string
    {
        $optionKey = 'commerce_page_' . $type;
        $pageId = rj_get_option($optionKey);

        if ($pageId) {
            $page = \App\Models\Post::find($pageId);
            if ($page) {
                return $page->public_url;
            }
        }

        return match($type) {
            'shop' => url('/shop'),
            'cart' => url('/cart'),
            'checkout' => url('/checkout'),
            default => url('/'),
        };
    }
}

if (!function_exists('rj_commerce_get_cart_count')) {
    function rj_commerce_get_cart_count(): int
    {
        $cart = session()->get('rj_cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }
}

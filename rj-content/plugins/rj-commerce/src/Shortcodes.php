<?php

use RjCommerce\Models\Product;
use RjCommerce\Models\ProductCategory;

if (function_exists('add_shortcode')) {

    // 1. Məhsullar (Products Grid / JSON)
    add_shortcode('rj_products', function ($atts) {
        $atts = shortcode_atts([
            'category' => '',
            'brand' => '',
            'limit' => 12,
            'columns' => 4,
            'orderby' => 'latest',
            'format' => 'html'
        ], $atts);

        $query = Product::with(['translations', 'category.translations', 'brand.translations', 'variations'])
            ->where('status', 'published');

        if (!empty($atts['category'])) {
            $category = ProductCategory::where('slug', $atts['category'])->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($atts['orderby'] === 'price_low') {
            $query->orderBy('price', 'asc');
        } elseif ($atts['orderby'] === 'price_high') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->limit($atts['limit'])->get();

        if ($atts['format'] === 'json') {
            $id = 'rj_products_json_' . uniqid();
            return '<script type="application/json" id="' . $id . '">' . json_encode($products) . '</script>';
        }

        $view = 'plugin-rj-commerce::shortcodes.products';
        if (view()->exists('theme::partials.commerce.products')) {
            $view = 'theme::partials.commerce.products';
        }

        return view($view, compact('products', 'atts'))->render();
    });

    // 2. Kateqoriyalar (Categories Grid / JSON)
    add_shortcode('rj_categories', function ($atts) {
        $atts = shortcode_atts([
            'limit' => 10,
            'columns' => 4,
            'format' => 'html'
        ], $atts);

        $categories = ProductCategory::with('translations')
            ->where('is_active', true)
            ->limit($atts['limit'])
            ->get();

        if ($atts['format'] === 'json') {
            $id = 'rj_categories_json_' . uniqid();
            return '<script type="application/json" id="' . $id . '">' . json_encode($categories) . '</script>';
        }

        $view = 'plugin-rj-commerce::shortcodes.categories';
        if (view()->exists('theme::partials.commerce.categories')) {
            $view = 'theme::partials.commerce.categories';
        }

        return view($view, compact('categories', 'atts'))->render();
    });

    // 3. Səbət (Cart)
    add_shortcode('rj_cart', function ($atts) {
        $view = 'plugin-rj-commerce::frontend.cart.index';
        if (view()->exists('theme::commerce.cart.index')) {
            $view = 'theme::commerce.cart.index';
        }
        return view($view)->render();
    });

    // 4. Checkout
    add_shortcode('rj_checkout', function ($atts) {
        $cart = session()->get('rj_cart', []);

        if (empty($cart)) {
            return '<div class="text-center py-10 text-red-500">' . __('Your cart is empty.') . '</div>';
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $taxEnabled = rj_get_option('commerce_tax_enabled') == '1';
        $taxRate = $taxEnabled ? (float) rj_get_option('commerce_default_tax_rate', 0) : 0;
        $taxTotal = $subtotal * ($taxRate / 100);
        $shippingTotal = 0;
        $total = $subtotal + $taxTotal + $shippingTotal;
        $currency = rj_get_option('commerce_currency', 'USD');

        $paymentMethods = [];
        if (rj_get_option('commerce_cod_enabled', '1') == '1') {
            $paymentMethods['cod'] = __('Cash on Delivery');
        }
        if (rj_get_option('commerce_stripe_enabled', '0') == '1') {
            $paymentMethods['stripe'] = __('Credit Card (Stripe)');
        }

        $view = 'plugin-rj-commerce::frontend.checkout.index';
        if (view()->exists('theme::commerce.checkout.index')) {
            $view = 'theme::commerce.checkout.index';
        }

        return view($view, compact('cart', 'subtotal', 'taxTotal', 'shippingTotal', 'total', 'currency', 'paymentMethods'))->render();
    });
}

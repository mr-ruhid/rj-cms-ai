<?php

use RjCommerce\Models\Product;
use RjCommerce\Models\ProductCategory;

if (function_exists('add_shortcode')) {

    add_shortcode('rj_products', function ($atts) {
        $atts = shortcode_atts([
            'category' => '',
            'brand' => '',
            'limit' => 12,
            'columns' => 4,
            'orderby' => 'latest',
            'format' => 'html'
        ], $atts);

        $query = Product::with(['translations', 'category.translations', 'brand.translations'])
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
            return json_encode($products);
        }

        $view = 'plugin-rj-commerce::shortcodes.products';
        if (view()->exists('theme::partials.commerce.products')) {
            $view = 'theme::partials.commerce.products';
        }

        return view($view, compact('products', 'atts'))->render();
    });

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
            return json_encode($categories);
        }

        $view = 'plugin-rj-commerce::shortcodes.categories';
        if (view()->exists('theme::partials.commerce.categories')) {
            $view = 'theme::partials.commerce.categories';
        }

        return view($view, compact('categories', 'atts'))->render();
    });
}

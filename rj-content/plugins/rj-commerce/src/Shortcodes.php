<?php

use RjCommerce\Models\Product;
use RjCommerce\Models\ProductCategory;

if (function_exists('add_shortcode')) {

    // 1. Məhsul Şəbəkəsi (Products Grid)
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

    // 2. Kateqoriyalar (Categories Grid)
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

    // 3. Səbət (Cart View)
    add_shortcode('rj_cart', function ($atts) {
        $view = 'plugin-rj-commerce::frontend.cart.index';
        if (view()->exists('theme::commerce.cart.index')) {
            $view = 'theme::commerce.cart.index';
        }
        return view($view)->render();
    });

    // 4. Checkout (Checkout View)
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

    // 5. Tək Məhsul Səbət Düyməsi (Add to cart button for specific product)
    add_shortcode('rj_add_to_cart', function ($atts) {
        $atts = shortcode_atts([
            'id' => null,
        ], $atts);

        if (!$atts['id']) {
            return '';
        }

        $product = Product::find($atts['id']);
        if (!$product) {
            return '';
        }

        $html = '<button type="button" onclick="rjAddToCart(' . $product->id . ', this)" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gray-900 text-white font-medium hover:bg-gray-800 transition shadow-lg">';
        $html .= '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>';
        $html .= __('Add to Cart');
        $html .= '</button>';

        $html .= "<script>
            async function rjAddToCart(productId, btn) {
                const originalText = btn.innerHTML;
                btn.innerHTML = '...';
                btn.disabled = true;
                try {
                    const res = await fetch('" . route('commerce.cart.add') . "', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '" . csrf_token() . "',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ product_id: productId, quantity: 1 })
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert(data.message);
                    } else {
                        alert(data.message || 'Error');
                    }
                } catch (e) {
                    alert('Error');
                } finally {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }
        </script>";

        return $html;
    });
}

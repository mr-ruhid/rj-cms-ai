<?php

namespace RjCommerce\Widgets;

use App\Widgets\BaseWidget;

class CartWidget extends BaseWidget
{
    public function id(): string
    {
        return 'commerce-cart';
    }

    public function name(): string
    {
        return __('Shopping Cart');
    }

    public function icon(): string
    {
        return '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>';
    }

    public function category(): string
    {
        return 'ecommerce';
    }

    public function description(): string
    {
        return __('Display the cart icon or a mini-cart summary.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'display_type',
                'type' => 'select',
                'label' => __('Display Type'),
                'default' => 'icon',
                'options' => [
                    'icon' => __('Cart Icon with Count'),
                    'mini_cart' => __('Mini Cart Summary'),
                ],
            ],
        ];
    }

    public function render(array $settings): string
    {
        $cart = session()->get('rj_cart', []);
        $itemCount = array_sum(array_column($cart, 'quantity'));
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $displayType = $settings['display_type'] ?? 'icon';

        return view('plugin-rj-commerce::widgets.cart', compact('cart', 'itemCount', 'subtotal', 'displayType'))->render();
    }
}

<?php

namespace RjCommerce\Widgets;

use App\Widgets\BaseWidget;
use RjCommerce\Models\Product;

class SingleProductWidget extends BaseWidget
{
    public function id(): string
    {
        return 'commerce-single-product';
    }

    public function name(): string
    {
        return __('Single Product');
    }

    public function icon(): string
    {
        return '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>';
    }

    public function category(): string
    {
        return 'ecommerce';
    }

    public function description(): string
    {
        return __('Display a specific product details card.');
    }

    public function schema(): array
    {
        $products = Product::with('translations')->where('status', 'published')->get();
        $options = ['' => __('— Select Product —')];

        foreach ($products as $product) {
            $options[$product->id] = $product->translation()?->name ?? $product->slug;
        }

        return [
            [
                'name' => 'product_id',
                'type' => 'select',
                'label' => __('Select Product'),
                'default' => '',
                'options' => $options,
            ],
        ];
    }

    public function render(array $settings): string
    {
        $productId = $settings['product_id'] ?? null;

        if (!$productId) {
            return '<div class="p-4 bg-amber-50 text-amber-800 text-sm border border-amber-200 rounded-lg">' . __('Please select a product to display.') . '</div>';
        }

        $product = Product::with(['translations', 'category.translations', 'brand.translations', 'variations'])->find($productId);

        if (!$product || $product->status !== 'published') {
            return '<div class="p-4 bg-red-50 text-red-800 text-sm border border-red-200 rounded-lg">' . __('Selected product not found or not published.') . '</div>';
        }

        return view('plugin-rj-commerce::widgets.single-product', compact('product'))->render();
    }
}

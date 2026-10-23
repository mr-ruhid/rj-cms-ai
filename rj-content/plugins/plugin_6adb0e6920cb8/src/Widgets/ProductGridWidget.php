<?php

namespace RjCommerce\Widgets;

use App\Widgets\BaseWidget;
use RjCommerce\Models\Product;
use RjCommerce\Models\ProductCategory;

class ProductGridWidget extends BaseWidget
{
    public function id(): string
    {
        return 'commerce-product-grid';
    }

    public function name(): string
    {
        return __('Product Grid');
    }

    public function icon(): string
    {
        return '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>';
    }

    public function category(): string
    {
        return 'ecommerce';
    }

    public function description(): string
    {
        return __('Display a grid of products from your store.');
    }

    public function schema(): array
    {
        $categories = ProductCategory::with('translations')->get();
        $options = ['' => __('All Categories')];

        foreach ($categories as $category) {
            $options[$category->id] = $category->translation()?->name ?? $category->slug;
        }

        return [
            [
                'name' => 'category_id',
                'type' => 'select',
                'label' => __('Filter by Category'),
                'default' => '',
                'options' => $options,
            ],
            [
                'name' => 'limit',
                'type' => 'number',
                'label' => __('Number of Products'),
                'default' => 8,
            ],
            [
                'name' => 'columns',
                'type' => 'select',
                'label' => __('Columns'),
                'default' => '4',
                'options' => [
                    '2' => __('2 Columns'),
                    '3' => __('3 Columns'),
                    '4' => __('4 Columns'),
                ],
            ],
            [
                'name' => 'orderby',
                'type' => 'select',
                'label' => __('Order By'),
                'default' => 'latest',
                'options' => [
                    'latest' => __('Latest'),
                    'price_low' => __('Price: Low to High'),
                    'price_high' => __('Price: High to Low'),
                ],
            ],
        ];
    }

    public function render(array $settings): string
    {
        $query = Product::with(['translations', 'category.translations', 'brand.translations'])
            ->where('status', 'published');

        if (!empty($settings['category_id'])) {
            $query->where('category_id', $settings['category_id']);
        }

        $orderby = $settings['orderby'] ?? 'latest';

        if ($orderby === 'price_low') {
            $query->orderBy('price', 'asc');
        } elseif ($orderby === 'price_high') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $limit = (int) ($settings['limit'] ?? 8);
        $products = $query->limit($limit)->get();
        $columns = $settings['columns'] ?? '4';

        return view('plugin-rj-commerce::widgets.product-grid', compact('products', 'columns'))->render();
    }
}

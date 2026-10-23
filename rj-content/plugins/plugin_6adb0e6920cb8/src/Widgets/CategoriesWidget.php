<?php

namespace RjCommerce\Widgets;

use App\Widgets\BaseWidget;
use RjCommerce\Models\ProductCategory;

class CategoriesWidget extends BaseWidget
{
    public function id(): string
    {
        return 'commerce-categories';
    }

    public function name(): string
    {
        return __('Product Categories');
    }

    public function icon(): string
    {
        return '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>';
    }

    public function category(): string
    {
        return 'ecommerce';
    }

    public function description(): string
    {
        return __('Display a grid of product categories.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'limit',
                'type' => 'number',
                'label' => __('Number of Categories'),
                'default' => 6,
            ],
            [
                'name' => 'columns',
                'type' => 'select',
                'label' => __('Columns'),
                'default' => '3',
                'options' => [
                    '2' => __('2 Columns'),
                    '3' => __('3 Columns'),
                    '4' => __('4 Columns'),
                ],
            ],
        ];
    }

    public function render(array $settings): string
    {
        $limit = (int) ($settings['limit'] ?? 6);
        $columns = $settings['columns'] ?? '3';

        $categories = ProductCategory::with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();

        return view('plugin-rj-commerce::widgets.categories', compact('categories', 'columns'))->render();
    }
}

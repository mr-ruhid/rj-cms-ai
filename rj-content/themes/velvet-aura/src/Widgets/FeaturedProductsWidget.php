<?php

namespace VelvetAura\Widgets;

use App\Widgets\BaseWidget;
use RjCommerce\Models\Product;
use RjCommerce\Models\ProductCategory;

class FeaturedProductsWidget extends BaseWidget
{
    public function id(): string
    {
        return 'velvet-featured-products';
    }

    public function name(): string
    {
        return __('Velvet Featured Products');
    }

    public function icon(): string
    {
        return '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>';
    }

    public function category(): string
    {
        return 'velvet-aura';
    }

    public function description(): string
    {
        return __('A luxury product grid curated for the VelvetAura theme.');
    }

    public function schema(): array
    {
        $options = ['' => __('All Categories')];

        if (class_exists(ProductCategory::class)) {
            $categories = ProductCategory::with('translations')->get();
            foreach ($categories as $category) {
                $options[$category->id] = $category->translation()?->name ?? $category->slug;
            }
        }

        return [
            [
                'name' => 'title',
                'type' => 'text',
                'label' => __('Section Title'),
                'default' => __('Curated Selection'),
            ],
            [
                'name' => 'subtitle',
                'type' => 'text',
                'label' => __('Section Subtitle'),
                'default' => __('Discover our latest arrivals'),
            ],
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
                'default' => 4,
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
        ];
    }

    public function render(array $settings): string
    {
        if (!class_exists(Product::class)) {
            return '<div class="p-4 bg-red-50 text-red-800 text-sm border border-red-200 rounded-lg">' . __('RJ Commerce plugin is required to use this widget.') . '</div>';
        }

        $query = Product::with(['translations', 'category.translations'])
            ->where('status', 'published');

        if (!empty($settings['category_id'])) {
            $query->where('category_id', $settings['category_id']);
        }

        $limit = (int) ($settings['limit'] ?? 4);
        $products = $query->latest()->limit($limit)->get();
        $columns = $settings['columns'] ?? '4';

        $title = $settings['title'] ?? '';
        $subtitle = $settings['subtitle'] ?? '';

        return view('theme::widgets.featured-products', compact('products', 'columns', 'title', 'subtitle'))->render();
    }
}

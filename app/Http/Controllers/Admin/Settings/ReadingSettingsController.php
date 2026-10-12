<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use App\Models\Option;

class ReadingSettingsController extends BaseSettingsController
{
    public function index()
    {
        $options = $this->getGroup('reading');

        $data = [
            'homepageTypes' => $this->homepageTypes(),
            'feedTypes' => $this->feedTypes(),
            'feedItems' => $this->feedItems(),
        ];

        return view('admin.settings.reading', compact('options', 'data'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'homepage_type' => ['required', 'in:latest,static'],
            'homepage_page' => ['nullable', 'string', 'max:200'],
            'posts_page' => ['nullable', 'string', 'max:200'],
            'posts_per_page' => ['required', 'integer', 'min:1', 'max:100'],
            'posts_per_rss' => ['required', 'integer', 'min:1', 'max:100'],
            'feed_type' => ['required', 'in:full,summary'],
            'feed_items' => ['required', 'integer', 'min:1', 'max:1000'],
            'show_featured_image' => ['nullable', 'boolean'],
            'show_author' => ['nullable', 'boolean'],
            'show_date' => ['nullable', 'boolean'],
            'show_category' => ['nullable', 'boolean'],
            'show_tags' => ['nullable', 'boolean'],
            'show_reading_time' => ['nullable', 'boolean'],
            'show_share_buttons' => ['nullable', 'boolean'],
            'enable_related_posts' => ['nullable', 'boolean'],
            'related_posts_count' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $booleanKeys = [
            'show_featured_image',
            'show_author',
            'show_date',
            'show_category',
            'show_tags',
            'show_reading_time',
            'show_share_buttons',
            'enable_related_posts',
        ];

        foreach ($booleanKeys as $key) {
            $data[$key] = $request->boolean($key) ? '1' : '0';
        }

        $typeMap = [
            'posts_per_page' => 'number',
            'posts_per_rss' => 'number',
            'feed_items' => 'number',
            'related_posts_count' => 'number',
        ];

        foreach ($data as $name => $value) {
            Option::set($name, $value, 'reading', $typeMap[$name] ?? 'text');
        }

        return back()->with('success', __('Reading settings saved.'));
    }

    protected function homepageTypes(): array
    {
        return [
            'latest' => __('Your latest posts'),
            'static' => __('A static page'),
        ];
    }

    protected function feedTypes(): array
    {
        return [
            'full' => __('Full text'),
            'summary' => __('Summary only'),
        ];
    }

    protected function feedItems(): array
    {
        return [
            10 => '10',
            20 => '20',
            50 => '50',
            100 => '100',
            500 => '500',
            1000 => '1000',
        ];
    }
}

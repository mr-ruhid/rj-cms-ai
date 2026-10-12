<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use App\Models\Option;

class PermalinksSettingsController extends BaseSettingsController
{
    public function index()
    {
        $options = $this->getGroup('permalinks');

        $data = [
            'structures' => $this->structures(),
            'categoryBases' => $this->categoryBases(),
            'tagBases' => $this->tagBases(),
        ];

        return view('admin.settings.permalinks', compact('options', 'data'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'permalink_structure' => ['required', 'in:plain,day,name,month,custom'],
            'custom_structure' => ['nullable', 'string', 'max:200'],

            'category_base' => ['nullable', 'string', 'max:100'],
            'tag_base' => ['nullable', 'string', 'max:100'],
            'author_base' => ['nullable', 'string', 'max:100'],

            'post_slug_case' => ['required', 'in:lower,keep'],
            'strip_stop_words' => ['nullable', 'boolean'],
            'redirect_old_urls' => ['nullable', 'boolean'],
            'trailing_slash' => ['required', 'in:yes,no'],
            'force_lowercase' => ['nullable', 'boolean'],
        ]);

        $booleanKeys = ['strip_stop_words', 'redirect_old_urls', 'force_lowercase'];

        foreach ($booleanKeys as $key) {
            $data[$key] = $request->boolean($key) ? '1' : '0';
        }

        foreach ($data as $name => $value) {
            Option::set($name, $value, 'permalinks');
        }

        return back()->with('success', __('Permalink settings saved.'));
    }

    protected function structures(): array
    {
        return [
            'plain' => [
                'label' => __('Plain'),
                'example' => '/?p=123',
            ],
            'day' => [
                'label' => __('Day and name'),
                'example' => '/2026/10/12/sample-post/',
            ],
            'month' => [
                'label' => __('Month and name'),
                'example' => '/2026/10/sample-post/',
            ],
            'name' => [
                'label' => __('Post name'),
                'example' => '/sample-post/',
            ],
            'custom' => [
                'label' => __('Custom Structure'),
                'example' => '/%category%/%postname%/',
            ],
        ];
    }

    protected function categoryBases(): array
    {
        return [
            'category' => 'category',
            'categories' => 'categories',
            'kategoriya' => 'kategoriya',
        ];
    }

    protected function tagBases(): array
    {
        return [
            'tag' => 'tag',
            'tags' => 'tags',
            'etiket' => 'etiket',
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use App\Models\Option;

class SeoSettingsController extends BaseSettingsController
{
    public function index()
    {
        $options = $this->getGroup('seo');

        $data = [
            'titleSeparators' => $this->titleSeparators(),
            'twitterCardTypes' => $this->twitterCardTypes(),
            'schemaTypes' => $this->schemaTypes(),
            'robotsContent' => $this->robotsContent(),
        ];

        return view('admin.settings.seo', compact('options', 'data'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'seo_title_template' => ['nullable', 'string', 'max:255'],
            'seo_title_separator' => ['required', 'string', 'max:5'],
            'seo_default_description' => ['nullable', 'string', 'max:500'],
            'seo_default_keywords' => ['nullable', 'string', 'max:500'],
            'seo_canonical' => ['nullable', 'boolean'],
            'seo_noindex_archives' => ['nullable', 'boolean'],

            'og_enabled' => ['nullable', 'boolean'],
            'og_site_name' => ['nullable', 'string', 'max:100'],
            'og_default_image' => ['nullable', 'url', 'max:500'],
            'og_type' => ['required', 'in:website,article,product,profile'],
            'twitter_enabled' => ['nullable', 'boolean'],
            'twitter_site' => ['nullable', 'string', 'max:50'],
            'twitter_card' => ['required', 'in:summary,summary_large_image,app,player'],

            'sitemap_enabled' => ['nullable', 'boolean'],
            'sitemap_url' => ['nullable', 'url', 'max:500'],
            'sitemap_include_posts' => ['nullable', 'boolean'],
            'sitemap_include_pages' => ['nullable', 'boolean'],
            'sitemap_include_categories' => ['nullable', 'boolean'],

            'robots_txt' => ['nullable', 'string', 'max:10000'],

            'schema_enabled' => ['nullable', 'boolean'],
            'schema_type' => ['required', 'in:Organization,WebSite,LocalBusiness,Person'],
            'schema_name' => ['nullable', 'string', 'max:200'],
            'schema_logo' => ['nullable', 'url', 'max:500'],

            'google_analytics_id' => ['nullable', 'string', 'max:50'],
            'google_search_console' => ['nullable', 'string', 'max:100'],
            'bing_verification' => ['nullable', 'string', 'max:100'],
            'yandex_verification' => ['nullable', 'string', 'max:100'],
        ]);

        $booleanKeys = [
            'seo_canonical',
            'seo_noindex_archives',
            'og_enabled',
            'twitter_enabled',
            'sitemap_enabled',
            'sitemap_include_posts',
            'sitemap_include_pages',
            'sitemap_include_categories',
            'schema_enabled',
        ];

        foreach ($booleanKeys as $key) {
            $data[$key] = $request->boolean($key) ? '1' : '0';
        }

        foreach ($data as $name => $value) {
            Option::set($name, $value, 'seo');
        }

        return back()->with('success', __('SEO settings saved.'));
    }

    protected function titleSeparators(): array
    {
        return [
            '-' => '-',
            '|' => '|',
            '·' => '·',
            '—' => '—',
            '»' => '»',
            ':' => ':',
        ];
    }

    protected function twitterCardTypes(): array
    {
        return [
            'summary' => __('Summary Card') . ' — ' . __('small square thumbnail'),
            'summary_large_image' => __('Summary Large Image') . ' — ' . __('wide banner image'),
            'app' => __('App Card') . ' — ' . __('for mobile apps'),
            'player' => __('Player Card') . ' — ' . __('for media players'),
        ];
    }

    protected function schemaTypes(): array
    {
        return [
            'Organization' => __('Organization'),
            'WebSite' => __('WebSite'),
            'LocalBusiness' => __('Local Business'),
            'Person' => __('Person'),
        ];
    }

    protected function robotsContent(): string
    {
        $path = public_path('robots.txt');

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml');
    }
}
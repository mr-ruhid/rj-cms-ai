<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CustomField;
use App\Models\GlobalWidget;
use App\Models\Menu;
use App\Models\Option;
use App\Models\Post;
use App\Models\PostType;
use App\Models\Tag;
use App\Models\Taxonomy;
use App\Models\Template;
use App\Models\Term;

class ContentExportService
{
    public function getAvailableOptions(): array
    {
        return [
            'posts' => ['label' => __('Posts'), 'count' => Post::where('type', 'post')->count(), 'default' => true],
            'pages' => ['label' => __('Pages'), 'count' => Post::where('type', 'page')->count(), 'default' => true],
            'categories' => ['label' => __('Categories'), 'count' => Category::count(), 'default' => true],
            'tags' => ['label' => __('Tags'), 'count' => Tag::count(), 'default' => true],
            'terms' => ['label' => __('Terms'), 'count' => Term::count(), 'default' => true],
            'menus' => ['label' => __('Menus'), 'count' => Menu::count(), 'default' => true],
            'post_types' => ['label' => __('Post Types'), 'count' => PostType::count(), 'default' => true],
            'taxonomies' => ['label' => __('Taxonomies'), 'count' => Taxonomy::count(), 'default' => true],
            'custom_fields' => ['label' => __('Custom Fields'), 'count' => CustomField::count(), 'default' => true],
            'templates' => ['label' => __('RJBuilder Templates'), 'count' => Template::count(), 'default' => true],
            'global_widgets' => ['label' => __('Global Widgets'), 'count' => GlobalWidget::count(), 'default' => true],
            'options' => ['label' => __('Site Settings'), 'count' => Option::whereNotIn('option_name', ['active_theme', 'rj_version'])->count(), 'default' => false],
        ];
    }

    public function export(array $options, string $format): array
    {
        $data = $this->buildData($options);

        return match ($format) {
            'json' => $this->buildJsonFile($data),
            'wxr' => $this->buildWxrFile($data),
            'csv' => $this->buildCsvFile($data),
            default => throw new \InvalidArgumentException(__('Unknown format: :format', ['format' => $format])),
        };
    }

    protected function buildData(array $options): array
    {
        $data = [
            'version' => '1.0',
            'generator' => 'RJ CMS',
            'exported_at' => now()->toIso8601String(),
            'site' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
                'locale' => config('app.locale'),
            ],
            'items' => [],
        ];

        if (!empty($options['posts'])) {
            $data['items']['posts'] = $this->exportPosts('post');
        }
        if (!empty($options['pages'])) {
            $data['items']['pages'] = $this->exportPosts('page');
        }
        if (!empty($options['categories'])) {
            $data['items']['categories'] = $this->exportCategories();
        }
        if (!empty($options['tags'])) {
            $data['items']['tags'] = $this->exportTags();
        }
        if (!empty($options['terms'])) {
            $data['items']['terms'] = $this->exportTerms();
        }
        if (!empty($options['menus'])) {
            $data['items']['menus'] = $this->exportMenus();
        }
        if (!empty($options['post_types'])) {
            $data['items']['post_types'] = $this->exportPostTypes();
        }
        if (!empty($options['taxonomies'])) {
            $data['items']['taxonomies'] = $this->exportTaxonomies();
        }
        if (!empty($options['custom_fields'])) {
            $data['items']['custom_fields'] = $this->exportCustomFields();
        }
        if (!empty($options['templates'])) {
            $data['items']['templates'] = $this->exportTemplates();
        }
        if (!empty($options['global_widgets'])) {
            $data['items']['global_widgets'] = $this->exportGlobalWidgets();
        }
        if (!empty($options['options'])) {
            $data['items']['options'] = $this->exportOptions();
        }

        return $data;
    }

    protected function exportPosts(string $type): array
    {
        return Post::with(['translations', 'categories', 'tags'])
            ->where('type', $type)
            ->orderBy('id')
            ->get()
            ->map(function ($post) {
                return [
                    'old_id' => $post->id,
                    'type' => $post->type,
                    'slug' => $post->slug,
                    'status' => $post->status,
                    'blocks' => $post->blocks,
                    'edit_mode' => $post->edit_mode ?? null,
                    'published_at' => $post->published_at?->toIso8601String(),
                    'created_at' => $post->created_at?->toIso8601String(),
                    'updated_at' => $post->updated_at?->toIso8601String(),
                    'translations' => $post->translations->map(fn($t) => [
                        'locale' => $t->locale,
                        'title' => $t->title ?? null,
                        'slug' => $t->slug ?? null,
                        'content' => $t->content ?? null,
                        'excerpt' => $t->excerpt ?? null,
                        'seo_title' => $t->seo_title ?? null,
                        'seo_description' => $t->seo_description ?? null,
                    ])->values()->toArray(),
                    'categories' => $post->categories->pluck('slug')->values()->toArray(),
                    'tags' => $post->tags->pluck('slug')->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }

    protected function exportCategories(): array
    {
        return Category::with(['translations'])
            ->orderBy('id')
            ->get()
            ->map(fn($c) => [
                'old_id' => $c->id,
                'slug' => $c->slug,
                'parent_id' => $c->parent_id ?? null,
                'translations' => $c->translations->map(fn($t) => [
                    'locale' => $t->locale,
                    'name' => $t->name ?? null,
                    'description' => $t->description ?? null,
                    'seo_title' => $t->seo_title ?? null,
                    'seo_description' => $t->seo_description ?? null,
                ])->values()->toArray(),
            ])
            ->values()
            ->toArray();
    }

    protected function exportTags(): array
    {
        return Tag::with(['translations'])
            ->orderBy('id')
            ->get()
            ->map(fn($t) => [
                'old_id' => $t->id,
                'slug' => $t->slug,
                'translations' => $t->translations->map(fn($tr) => [
                    'locale' => $tr->locale,
                    'name' => $tr->name ?? null,
                    'description' => $tr->description ?? null,
                ])->values()->toArray(),
            ])
            ->values()
            ->toArray();
    }

    protected function exportTerms(): array
    {
        return Term::with(['translations'])
            ->orderBy('id')
            ->get()
            ->map(fn($t) => [
                'old_id' => $t->id,
                'taxonomy' => $t->taxonomy ?? null,
                'slug' => $t->slug,
                'parent_id' => $t->parent_id ?? null,
                'translations' => $t->translations->map(fn($tr) => [
                    'locale' => $tr->locale,
                    'name' => $tr->name ?? null,
                    'description' => $tr->description ?? null,
                ])->values()->toArray(),
            ])
            ->values()
            ->toArray();
    }

    protected function exportMenus(): array
    {
        return Menu::with(['items'])
            ->orderBy('id')
            ->get()
            ->map(fn($m) => [
                'old_id' => $m->id,
                'name' => $m->name,
                'location' => $m->location ?? null,
                'items' => $m->items->map(fn($i) => [
                    'old_id' => $i->id,
                    'parent_id' => $i->parent_id ?? null,
                    'label' => $i->label,
                    'url' => $i->url ?? null,
                    'type' => $i->type ?? null,
                    'target' => $i->target ?? null,
                    'sort_order' => $i->sort_order ?? 0,
                ])->values()->toArray(),
            ])
            ->values()
            ->toArray();
    }

    protected function exportPostTypes(): array
    {
        return PostType::orderBy('id')->get()->map(fn($pt) => [
            'old_id' => $pt->id,
            'slug' => $pt->slug,
            'name' => $pt->name,
            'singular_name' => $pt->singular_name ?? null,
            'icon' => $pt->icon ?? null,
            'supports' => $pt->supports ?? [],
            'status' => $pt->status ?? 'active',
        ])->values()->toArray();
    }

    protected function exportTaxonomies(): array
    {
        return Taxonomy::orderBy('id')->get()->map(fn($t) => [
            'old_id' => $t->id,
            'slug' => $t->slug,
            'name' => $t->name,
            'post_types' => $t->post_types ?? [],
            'hierarchical' => (bool) ($t->hierarchical ?? false),
            'status' => $t->status ?? 'active',
        ])->values()->toArray();
    }

    protected function exportCustomFields(): array
    {
        return CustomField::orderBy('id')->get()->map(fn($f) => [
            'old_id' => $f->id,
            'slug' => $f->slug,
            'name' => $f->name,
            'type' => $f->type,
            'post_type' => $f->post_type ?? null,
            'options' => $f->options ?? [],
            'default' => $f->default ?? null,
            'required' => (bool) ($f->required ?? false),
            'sort_order' => $f->sort_order ?? 0,
        ])->values()->toArray();
    }

    protected function exportTemplates(): array
    {
        return Template::orderBy('id')->get()->map(fn($t) => [
            'old_id' => $t->id,
            'name' => $t->name,
            'slug' => $t->slug,
            'type' => $t->type,
            'content' => $t->content,
            'status' => $t->status,
            'is_default' => (bool) $t->is_default,
            'conditions' => $t->conditions ?? null,
            'settings' => $t->settings ?? null,
        ])->values()->toArray();
    }

    protected function exportGlobalWidgets(): array
    {
        return GlobalWidget::orderBy('id')->get()->map(fn($g) => [
            'old_id' => $g->id,
            'name' => $g->name,
            'type' => $g->type ?? null,
            'content' => $g->content ?? null,
            'settings' => $g->settings ?? null,
        ])->values()->toArray();
    }

    protected function exportOptions(): array
    {
        $exclude = ['active_theme', 'rj_version'];

        return Option::whereNotIn('option_name', $exclude)
            ->orderBy('option_name')
            ->get()
            ->map(fn($o) => [
                'key' => $o->option_name,
                'value' => $o->option_value,
                'group' => $o->option_group ?? 'general',
                'type' => $o->option_type ?? 'text',
            ])
            ->values()
            ->toArray();
    }

    protected function buildJsonFile(array $data): array
    {
        $filename = 'rj-export-' . now()->format('Y-m-d-His') . '.json';

        return [
            'filename' => $filename,
            'mime' => 'application/json',
            'content' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
    }

    protected function buildWxrFile(array $data): array
    {
        $filename = 'rj-export-' . now()->format('Y-m-d-His') . '.xml';
        $siteUrl = $data['site']['url'] ?? url('/');
        $siteName = $data['site']['name'] ?? config('app.name');

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $xml .= '<rss version="2.0"' . "\n";
        $xml .= '    xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"' . "\n";
        $xml .= '    xmlns:content="http://purl.org/rss/1.0/modules/content/"' . "\n";
        $xml .= '    xmlns:wfw="http://wellformedweb.org/CommentAPI/"' . "\n";
        $xml .= '    xmlns:dc="http://purl.org/dc/elements/1.1/"' . "\n";
        $xml .= '    xmlns:wp="http://wordpress.org/export/1.2/">' . "\n";
        $xml .= '<channel>' . "\n";
        $xml .= '    <title>' . htmlspecialchars($siteName) . '</title>' . "\n";
        $xml .= '    <link>' . htmlspecialchars($siteUrl) . '</link>' . "\n";
        $xml .= '    <description>' . htmlspecialchars($siteName) . '</description>' . "\n";
        $xml .= '    <wp:wxr_version>1.2</wp:wxr_version>' . "\n";
        $xml .= '    <wp:base_site_url>' . htmlspecialchars($siteUrl) . '</wp:base_site_url>' . "\n";
        $xml .= '    <wp:base_blog_url>' . htmlspecialchars($siteUrl) . '</wp:base_blog_url>' . "\n";
        $xml .= '    <generator>RJ CMS</generator>' . "\n";

        $allPosts = array_merge($data['items']['posts'] ?? [], $data['items']['pages'] ?? []);

        foreach ($allPosts as $post) {
            $primary = $post['translations'][0] ?? [];
            $title = $primary['title'] ?? $post['slug'];
            $content = $primary['content'] ?? '';
            $excerpt = $primary['excerpt'] ?? '';
            $slug = $post['slug'];

            $xml .= '    <item>' . "\n";
            $xml .= '        <title>' . htmlspecialchars($title) . '</title>' . "\n";
            $xml .= '        <link>' . htmlspecialchars(rtrim($siteUrl, '/') . '/' . $slug) . '</link>' . "\n";
            $xml .= '        <pubDate>' . ($post['published_at'] ?? $post['created_at'] ?? now()->toRfc2822String()) . '</pubDate>' . "\n";
            $xml .= '        <dc:creator>admin</dc:creator>' . "\n";
            $xml .= '        <guid isPermaLink="false">' . htmlspecialchars(rtrim($siteUrl, '/') . '/?p=' . $post['old_id']) . '</guid>' . "\n";
            $xml .= '        <description></description>' . "\n";
            $xml .= '        <content:encoded><![CDATA[' . $content . ']]></content:encoded>' . "\n";
            $xml .= '        <excerpt:encoded><![CDATA[' . $excerpt . ']]></excerpt:encoded>' . "\n";
            $xml .= '        <wp:post_id>' . $post['old_id'] . '</wp:post_id>' . "\n";
            $xml .= '        <wp:post_date>' . ($post['created_at'] ?? now()->toDateTimeString()) . '</wp:post_date>' . "\n";
            $xml .= '        <wp:post_name>' . htmlspecialchars($slug) . '</wp:post_name>' . "\n";
            $xml .= '        <wp:status>' . ($post['status'] === 'published' ? 'publish' : $post['status']) . '</wp:status>' . "\n";
            $xml .= '        <wp:post_type>' . ($post['type'] === 'page' ? 'page' : 'post') . '</wp:post_type>' . "\n";

            foreach ($post['categories'] ?? [] as $catSlug) {
                $xml .= '        <category domain="category" nicename="' . htmlspecialchars($catSlug) . '">' . htmlspecialchars($catSlug) . '</category>' . "\n";
            }
            foreach ($post['tags'] ?? [] as $tagSlug) {
                $xml .= '        <category domain="post_tag" nicename="' . htmlspecialchars($tagSlug) . '">' . htmlspecialchars($tagSlug) . '</category>' . "\n";
            }

            $xml .= '    </item>' . "\n";
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>' . "\n";

        return [
            'filename' => $filename,
            'mime' => 'application/xml',
            'content' => $xml,
        ];
    }

    protected function buildCsvFile(array $data): array
    {
        $filename = 'rj-export-' . now()->format('Y-m-d-His') . '.csv';

        $allPosts = array_merge($data['items']['posts'] ?? [], $data['items']['pages'] ?? []);

        $out = fopen('php://temp', 'r+');

        fputcsv($out, ['ID', 'Type', 'Status', 'Slug', 'Locale', 'Title', 'Content', 'Excerpt', 'SEO Title', 'SEO Description', 'Categories', 'Tags', 'Created At']);

        foreach ($allPosts as $post) {
            if (empty($post['translations'])) {
                fputcsv($out, [
                    $post['old_id'],
                    $post['type'],
                    $post['status'],
                    $post['slug'],
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    implode(',', $post['categories'] ?? []),
                    implode(',', $post['tags'] ?? []),
                    $post['created_at'] ?? '',
                ]);
                continue;
            }

            foreach ($post['translations'] as $t) {
                fputcsv($out, [
                    $post['old_id'],
                    $post['type'],
                    $post['status'],
                    $post['slug'],
                    $t['locale'] ?? '',
                    $t['title'] ?? '',
                    $t['content'] ?? '',
                    $t['excerpt'] ?? '',
                    $t['seo_title'] ?? '',
                    $t['seo_description'] ?? '',
                    implode(',', $post['categories'] ?? []),
                    implode(',', $post['tags'] ?? []),
                    $post['created_at'] ?? '',
                ]);
            }
        }

        rewind($out);
        $content = stream_get_contents($out);
        fclose($out);

        return [
            'filename' => $filename,
            'mime' => 'text/csv',
            'content' => $content,
        ];
    }
}
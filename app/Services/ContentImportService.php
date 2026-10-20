<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\CustomField;
use App\Models\GlobalWidget;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Option;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\PostType;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\Taxonomy;
use App\Models\Template;
use App\Models\Term;
use App\Models\TermTranslation;
use Illuminate\Support\Str;

class ContentImportService
{
    protected array $stats = [];
    protected array $warnings = [];

    public function parseFile(string $path, string $extension): array
    {
        $content = file_get_contents($path);
        $extension = strtolower($extension);

        return match ($extension) {
            'json' => $this->parseJson($content),
            'xml' => $this->parseWxr($content),
            'csv' => $this->parseCsv($content),
            default => throw new \InvalidArgumentException(__('Unsupported format: :ext', ['ext' => $extension])),
        };
    }

    public function parseJson(string $content): array
    {
        $data = json_decode($content, true);

        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(__('Invalid JSON: :msg', ['msg' => json_last_error_msg()]));
        }

        if (empty($data['items']) || !is_array($data['items'])) {
            throw new \RuntimeException(__('Missing "items" key in JSON. This is not an RJ CMS export.'));
        }

        return [
            'format' => 'json',
            'data' => $data,
        ];
    }

    public function parseWxr(string $content): array
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $msg = $errors[0]->message ?? __('Malformed XML');
            throw new \RuntimeException(__('Invalid XML: :msg', ['msg' => trim($msg)]));
        }

        $namespaces = $xml->getNamespaces(true);

        $items = [];

        foreach ($xml->channel->item as $item) {
            $itemWp = $item->children($namespaces['wp'] ?? 'http://wordpress.org/export/1.2/');
            $itemContent = $item->children($namespaces['content'] ?? 'http://purl.org/rss/1.0/modules/content/');
            $itemExcerpt = $item->children($namespaces['excerpt'] ?? 'http://wordpress.org/export/1.2/excerpt/');

            $postType = (string) ($itemWp->post_type ?? 'post');
            $status = (string) ($itemWp->status ?? 'draft');

            $statusMap = [
                'publish' => 'published',
                'draft' => 'draft',
                'pending' => 'draft',
                'private' => 'private',
                'trash' => 'trash',
            ];

            $categories = [];
            $tags = [];

            foreach ($item->category as $cat) {
                $domain = (string) $cat['domain'];
                $nicename = (string) $cat['nicename'];
                if ($domain === 'category') {
                    $categories[] = $nicename;
                } elseif ($domain === 'post_tag') {
                    $tags[] = $nicename;
                }
            }

            $items[] = [
                'old_id' => (int) ($itemWp->post_id ?? 0),
                'type' => $postType === 'page' ? 'page' : 'post',
                'slug' => (string) ($itemWp->post_name ?? Str::slug((string) $item->title)),
                'status' => $statusMap[$status] ?? 'draft',
                'blocks' => null,
                'edit_mode' => null,
                'published_at' => (string) ($itemWp->post_date ?? null),
                'created_at' => (string) ($itemWp->post_date ?? null),
                'updated_at' => now()->toIso8601String(),
                'translations' => [
                    [
                        'locale' => config('app.locale', 'en'),
                        'title' => (string) $item->title,
                        'slug' => (string) ($itemWp->post_name ?? Str::slug((string) $item->title)),
                        'content' => (string) ($itemContent->encoded ?? ''),
                        'excerpt' => (string) ($itemExcerpt->encoded ?? ''),
                        'seo_title' => null,
                        'seo_description' => null,
                    ],
                ],
                'categories' => $categories,
                'tags' => $tags,
            ];
        }

        return [
            'format' => 'wxr',
            'data' => [
                'version' => '1.0',
                'generator' => 'WordPress',
                'items' => [
                    'posts' => array_values(array_filter($items, fn($i) => $i['type'] === 'post')),
                    'pages' => array_values(array_filter($items, fn($i) => $i['type'] === 'page')),
                ],
            ],
        ];
    }

    public function parseCsv(string $content): array
    {
        $rows = [];
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $content);
        rewind($handle);

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            throw new \RuntimeException(__('Empty CSV file.'));
        }

        $header = array_map(fn($h) => strtolower(trim((string) $h)), $header);

        $required = ['type', 'slug', 'locale', 'title'];
        foreach ($required as $col) {
            if (!in_array($col, $header, true)) {
                fclose($handle);
                throw new \RuntimeException(__('CSV missing required column: :col', ['col' => $col]));
            }
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) {
                continue;
            }
            $rows[] = array_combine($header, $row);
        }
        fclose($handle);

        $grouped = [];

        foreach ($rows as $row) {
            $oldId = $row['id'] ?? $row['old_id'] ?? 0;
            $key = ($row['type'] ?? 'post') . ':' . ($oldId ?: $row['slug']);

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'old_id' => (int) $oldId,
                    'type' => ($row['type'] ?? 'post') === 'page' ? 'page' : 'post',
                    'slug' => $row['slug'] ?? '',
                    'status' => $row['status'] ?? 'draft',
                    'blocks' => null,
                    'edit_mode' => null,
                    'published_at' => $row['created at'] ?? $row['created_at'] ?? null,
                    'created_at' => $row['created at'] ?? $row['created_at'] ?? null,
                    'updated_at' => now()->toIso8601String(),
                    'translations' => [],
                    'categories' => [],
                    'tags' => [],
                ];
            }

            if (!empty($row['locale'])) {
                $grouped[$key]['translations'][] = [
                    'locale' => $row['locale'],
                    'title' => $row['title'] ?? '',
                    'slug' => $row['slug'] ?? '',
                    'content' => $row['content'] ?? '',
                    'excerpt' => $row['excerpt'] ?? '',
                    'seo_title' => $row['seo title'] ?? null,
                    'seo_description' => $row['seo description'] ?? null,
                ];
            }

            if (!empty($row['categories'])) {
                foreach (explode(',', $row['categories']) as $cat) {
                    $cat = trim($cat);
                    if ($cat !== '') {
                        $grouped[$key]['categories'][] = $cat;
                    }
                }
            }
            if (!empty($row['tags'])) {
                foreach (explode(',', $row['tags']) as $tag) {
                    $tag = trim($tag);
                    if ($tag !== '') {
                        $grouped[$key]['tags'][] = $tag;
                    }
                }
            }
        }

        $posts = [];
        $pages = [];

        foreach ($grouped as $item) {
            if (empty($item['translations'])) {
                continue;
            }
            if ($item['type'] === 'page') {
                $pages[] = $item;
            } else {
                $posts[] = $item;
            }
        }

        return [
            'format' => 'csv',
            'data' => [
                'version' => '1.0',
                'generator' => 'CSV',
                'items' => [
                    'posts' => $posts,
                    'pages' => $pages,
                ],
            ],
        ];
    }

    public function preview(array $parsed): array
    {
        $items = $parsed['data']['items'] ?? [];

        $summary = [
            'format' => $parsed['format'],
            'generator' => $parsed['data']['generator'] ?? '',
            'items' => [],
        ];

        $counts = [
            'posts' => count($items['posts'] ?? []),
            'pages' => count($items['pages'] ?? []),
            'categories' => count($items['categories'] ?? []),
            'tags' => count($items['tags'] ?? []),
            'terms' => count($items['terms'] ?? []),
            'menus' => count($items['menus'] ?? []),
            'post_types' => count($items['post_types'] ?? []),
            'taxonomies' => count($items['taxonomies'] ?? []),
            'custom_fields' => count($items['custom_fields'] ?? []),
            'templates' => count($items['templates'] ?? []),
            'global_widgets' => count($items['global_widgets'] ?? []),
            'options' => count($items['options'] ?? []),
        ];

        foreach ($counts as $key => $count) {
            if ($count > 0) {
                $summary['items'][$key] = $count;
            }
        }

        return $summary;
    }

    public function run(array $parsed, string $strategy = 'skip'): array
    {
        $this->stats = [];
        $this->warnings = [];

        $items = $parsed['data']['items'] ?? [];

        try {
            if (!empty($items['taxonomies'])) {
                $this->importTaxonomies($items['taxonomies'], $strategy);
            }
            if (!empty($items['post_types'])) {
                $this->importPostTypes($items['post_types'], $strategy);
            }
            if (!empty($items['categories'])) {
                $this->importCategories($items['categories'], $strategy);
            }
            if (!empty($items['tags'])) {
                $this->importTags($items['tags'], $strategy);
            }
            if (!empty($items['terms'])) {
                $this->importTerms($items['terms'], $strategy);
            }
            if (!empty($items['custom_fields'])) {
                $this->importCustomFields($items['custom_fields'], $strategy);
            }
            if (!empty($items['posts'])) {
                $this->importPosts($items['posts'], $strategy);
            }
            if (!empty($items['pages'])) {
                $this->importPosts($items['pages'], $strategy);
            }
            if (!empty($items['menus'])) {
                $this->importMenus($items['menus'], $strategy);
            }
            if (!empty($items['templates'])) {
                $this->importTemplates($items['templates'], $strategy);
            }
            if (!empty($items['global_widgets'])) {
                $this->importGlobalWidgets($items['global_widgets'], $strategy);
            }
            if (!empty($items['options'])) {
                $this->importOptions($items['options'], $strategy);
            }
        } catch (\Throwable $e) {
            $this->warnings[] = $e->getMessage();
        }

        return [
            'stats' => $this->stats,
            'warnings' => $this->warnings,
        ];
    }

    protected function track(string $group, string $action): void
    {
        if (!isset($this->stats[$group])) {
            $this->stats[$group] = ['created' => 0, 'updated' => 0, 'skipped' => 0];
        }
        $this->stats[$group][$action]++;
    }

    protected function importTaxonomies(array $items, string $strategy): void
    {
        foreach ($items as $item) {
            $slug = $item['slug'] ?? null;
            if (!$slug) continue;

            $existing = Taxonomy::where('slug', $slug)->first();

            if ($existing) {
                if ($strategy === 'update') {
                    $existing->update([
                        'name' => $item['name'] ?? $existing->name,
                        'post_types' => $item['post_types'] ?? $existing->post_types,
                        'hierarchical' => $item['hierarchical'] ?? $existing->hierarchical,
                    ]);
                    $this->track('taxonomies', 'updated');
                } else {
                    $this->track('taxonomies', 'skipped');
                }
                continue;
            }

            Taxonomy::create([
                'slug' => $slug,
                'name' => $item['name'] ?? Str::title($slug),
                'post_types' => $item['post_types'] ?? [],
                'hierarchical' => $item['hierarchical'] ?? false,
                'status' => $item['status'] ?? 'active',
            ]);
            $this->track('taxonomies', 'created');
        }
    }

    protected function importPostTypes(array $items, string $strategy): void
    {
        $locked = ['post', 'page'];

        foreach ($items as $item) {
            $slug = $item['slug'] ?? null;
            if (!$slug || in_array($slug, $locked, true)) continue;

            $existing = PostType::where('slug', $slug)->first();

            if ($existing) {
                if ($strategy === 'update') {
                    $existing->update([
                        'name' => $item['name'] ?? $existing->name,
                        'singular_name' => $item['singular_name'] ?? $existing->singular_name,
                        'icon' => $item['icon'] ?? $existing->icon,
                        'supports' => $item['supports'] ?? $existing->supports,
                    ]);
                    $this->track('post_types', 'updated');
                } else {
                    $this->track('post_types', 'skipped');
                }
                continue;
            }

            PostType::create([
                'slug' => $slug,
                'name' => $item['name'] ?? Str::title($slug),
                'singular_name' => $item['singular_name'] ?? Str::title($slug),
                'icon' => $item['icon'] ?? null,
                'supports' => $item['supports'] ?? [],
                'status' => $item['status'] ?? 'active',
            ]);
            $this->track('post_types', 'created');
        }
    }

    protected function importCategories(array $items, string $strategy): void
    {
        $idMap = [];

        foreach ($items as $item) {
            $slug = $item['slug'] ?? null;
            if (!$slug) continue;

            $existing = Category::where('slug', $slug)->first();

            if ($existing) {
                if ($strategy === 'update') {
                    $this->updateCategoryTranslations($existing, $item['translations'] ?? []);
                    $this->track('categories', 'updated');
                } else {
                    $this->track('categories', 'skipped');
                }
                $idMap[$item['old_id']] = $existing->id;
                continue;
            }

            $cat = Category::create([
                'slug' => $slug,
                'parent_id' => null,
            ]);
            $this->createCategoryTranslations($cat, $item['translations'] ?? []);
            $idMap[$item['old_id']] = $cat->id;
            $this->track('categories', 'created');
        }

        foreach ($items as $item) {
            if (empty($item['parent_id'])) continue;
            $newId = $idMap[$item['old_id']] ?? null;
            $newParent = $idMap[$item['parent_id']] ?? null;
            if ($newId && $newParent) {
                Category::where('id', $newId)->update(['parent_id' => $newParent]);
            }
        }
    }

    protected function createCategoryTranslations(Category $cat, array $translations): void
    {
        foreach ($translations as $t) {
            if (empty($t['locale'])) continue;
            CategoryTranslation::updateOrCreate(
                ['category_id' => $cat->id, 'locale' => $t['locale']],
                [
                    'name' => $t['name'] ?? Str::title($cat->slug),
                    'description' => $t['description'] ?? null,
                    'seo_title' => $t['seo_title'] ?? null,
                    'seo_description' => $t['seo_description'] ?? null,
                ]
            );
        }
    }

    protected function updateCategoryTranslations(Category $cat, array $translations): void
    {
        foreach ($translations as $t) {
            if (empty($t['locale'])) continue;
            CategoryTranslation::updateOrCreate(
                ['category_id' => $cat->id, 'locale' => $t['locale']],
                [
                    'name' => $t['name'] ?? null,
                    'description' => $t['description'] ?? null,
                    'seo_title' => $t['seo_title'] ?? null,
                    'seo_description' => $t['seo_description'] ?? null,
                ]
            );
        }
    }

    protected function importTags(array $items, string $strategy): void
    {
        foreach ($items as $item) {
            $slug = $item['slug'] ?? null;
            if (!$slug) continue;

            $existing = Tag::where('slug', $slug)->first();

            if ($existing) {
                if ($strategy === 'update') {
                    foreach ($item['translations'] ?? [] as $t) {
                        if (empty($t['locale'])) continue;
                        TagTranslation::updateOrCreate(
                            ['tag_id' => $existing->id, 'locale' => $t['locale']],
                            [
                                'name' => $t['name'] ?? null,
                                'description' => $t['description'] ?? null,
                            ]
                        );
                    }
                    $this->track('tags', 'updated');
                } else {
                    $this->track('tags', 'skipped');
                }
                continue;
            }

            $tag = Tag::create(['slug' => $slug]);

            foreach ($item['translations'] ?? [] as $t) {
                if (empty($t['locale'])) continue;
                TagTranslation::create([
                    'tag_id' => $tag->id,
                    'locale' => $t['locale'],
                    'name' => $t['name'] ?? Str::title($slug),
                    'description' => $t['description'] ?? null,
                ]);
            }
            $this->track('tags', 'created');
        }
    }

    protected function importTerms(array $items, string $strategy): void
    {
        $idMap = [];

        foreach ($items as $item) {
            $slug = $item['slug'] ?? null;
            $taxonomy = $item['taxonomy'] ?? null;
            if (!$slug || !$taxonomy) continue;

            $existing = Term::where('slug', $slug)->where('taxonomy', $taxonomy)->first();

            if ($existing) {
                if ($strategy === 'update') {
                    foreach ($item['translations'] ?? [] as $t) {
                        if (empty($t['locale'])) continue;
                        TermTranslation::updateOrCreate(
                            ['term_id' => $existing->id, 'locale' => $t['locale']],
                            [
                                'name' => $t['name'] ?? null,
                                'description' => $t['description'] ?? null,
                            ]
                        );
                    }
                    $this->track('terms', 'updated');
                } else {
                    $this->track('terms', 'skipped');
                }
                $idMap[$item['old_id']] = $existing->id;
                continue;
            }

            $term = Term::create([
                'slug' => $slug,
                'taxonomy' => $taxonomy,
                'parent_id' => null,
            ]);

            foreach ($item['translations'] ?? [] as $t) {
                if (empty($t['locale'])) continue;
                TermTranslation::create([
                    'term_id' => $term->id,
                    'locale' => $t['locale'],
                    'name' => $t['name'] ?? Str::title($slug),
                    'description' => $t['description'] ?? null,
                ]);
            }
            $idMap[$item['old_id']] = $term->id;
            $this->track('terms', 'created');
        }

        foreach ($items as $item) {
            if (empty($item['parent_id'])) continue;
            $newId = $idMap[$item['old_id']] ?? null;
            $newParent = $idMap[$item['parent_id']] ?? null;
            if ($newId && $newParent) {
                Term::where('id', $newId)->update(['parent_id' => $newParent]);
            }
        }
    }

    protected function importCustomFields(array $items, string $strategy): void
    {
        foreach ($items as $item) {
            $slug = $item['slug'] ?? null;
            $postType = $item['post_type'] ?? null;
            if (!$slug) continue;

            $existing = CustomField::where('slug', $slug)->where('post_type', $postType)->first();

            if ($existing) {
                if ($strategy === 'update') {
                    $existing->update([
                        'name' => $item['name'] ?? $existing->name,
                        'type' => $item['type'] ?? $existing->type,
                        'options' => $item['options'] ?? $existing->options,
                        'default' => $item['default'] ?? $existing->default,
                        'required' => $item['required'] ?? $existing->required,
                    ]);
                    $this->track('custom_fields', 'updated');
                } else {
                    $this->track('custom_fields', 'skipped');
                }
                continue;
            }

            CustomField::create([
                'slug' => $slug,
                'name' => $item['name'] ?? Str::title($slug),
                'type' => $item['type'] ?? 'text',
                'post_type' => $postType,
                'options' => $item['options'] ?? [],
                'default' => $item['default'] ?? null,
                'required' => $item['required'] ?? false,
                'sort_order' => $item['sort_order'] ?? 0,
            ]);
            $this->track('custom_fields', 'created');
        }
    }

    protected function importPosts(array $items, string $strategy): void
    {
        foreach ($items as $item) {
            $slug = $item['slug'] ?? null;
            if (!$slug || empty($item['translations'])) continue;

            $type = $item['type'] ?? 'post';
            $primaryTranslation = $item['translations'][0];
            $primarySlug = $primaryTranslation['slug'] ?? $slug;

            $existing = Post::where('slug', $primarySlug)->where('type', $type)->first();

            if ($existing) {
                if ($strategy === 'update') {
                    $existing->update([
                        'status' => $item['status'] ?? $existing->status,
                        'blocks' => $item['blocks'] ?? $existing->blocks,
                        'published_at' => $item['published_at'] ? now()->parse($item['published_at']) : $existing->published_at,
                    ]);
                    $this->syncPostTranslations($existing, $item['translations']);
                    $this->syncPostTaxonomies($existing, $item);
                    $this->track($type === 'page' ? 'pages' : 'posts', 'updated');
                } else {
                    $this->track($type === 'page' ? 'pages' : 'posts', 'skipped');
                }
                continue;
            }

            $post = Post::create([
                'type' => $type,
                'slug' => $primarySlug,
                'status' => $item['status'] ?? 'draft',
                'blocks' => $item['blocks'] ?? null,
                'edit_mode' => $item['edit_mode'] ?? null,
                'published_at' => $item['published_at'] ? now()->parse($item['published_at']) : null,
            ]);

            $this->syncPostTranslations($post, $item['translations']);
            $this->syncPostTaxonomies($post, $item);

            $this->track($type === 'page' ? 'pages' : 'posts', 'created');
        }
    }

    protected function syncPostTranslations(Post $post, array $translations): void
    {
        foreach ($translations as $t) {
            if (empty($t['locale'])) continue;

            PostTranslation::updateOrCreate(
                ['post_id' => $post->id, 'locale' => $t['locale']],
                [
                    'title' => $t['title'] ?? '',
                    'slug' => $t['slug'] ?? $post->slug,
                    'content' => $t['content'] ?? '',
                    'excerpt' => $t['excerpt'] ?? '',
                    'seo_title' => $t['seo_title'] ?? null,
                    'seo_description' => $t['seo_description'] ?? null,
                ]
            );
        }
    }

    protected function syncPostTaxonomies(Post $post, array $item): void
    {
        if (!empty($item['categories'])) {
            $catIds = Category::whereIn('slug', $item['categories'])->pluck('id')->toArray();
            if (!empty($catIds)) {
                $post->categories()->syncWithoutDetaching($catIds);
            }
        }

        if (!empty($item['tags'])) {
            $tagIds = Tag::whereIn('slug', $item['tags'])->pluck('id')->toArray();
            if (!empty($tagIds)) {
                $post->tags()->syncWithoutDetaching($tagIds);
            }
        }
    }

    protected function importMenus(array $items, string $strategy): void
    {
        foreach ($items as $item) {
            $name = $item['name'] ?? null;
            if (!$name) continue;

            $location = $item['location'] ?? null;
            $existing = $location
                ? Menu::where('location', $location)->first()
                : Menu::where('name', $name)->first();

            if ($existing) {
                if ($strategy === 'update') {
                    $existing->update(['name' => $name, 'location' => $location]);
                    $this->syncMenuItems($existing, $item['items'] ?? []);
                    $this->track('menus', 'updated');
                } else {
                    $this->track('menus', 'skipped');
                }
                continue;
            }

            $menu = Menu::create([
                'name' => $name,
                'location' => $location,
            ]);

            $this->syncMenuItems($menu, $item['items'] ?? []);

            $this->track('menus', 'created');
        }
    }

    protected function syncMenuItems(Menu $menu, array $items): void
    {
        $idMap = [];

        foreach ($items as $item) {
            if (empty($item['parent_id'])) {
                $newItem = MenuItem::create([
                    'menu_id' => $menu->id,
                    'parent_id' => null,
                    'label' => $item['label'] ?? '',
                    'url' => $item['url'] ?? null,
                    'type' => $item['type'] ?? 'custom',
                    'target' => $item['target'] ?? null,
                    'sort_order' => $item['sort_order'] ?? 0,
                ]);
                $idMap[$item['old_id']] = $newItem->id;
            }
        }

        foreach ($items as $item) {
            if (empty($item['parent_id'])) continue;
            $newParent = $idMap[$item['parent_id']] ?? null;
            if (!$newParent) continue;

            $newItem = MenuItem::create([
                'menu_id' => $menu->id,
                'parent_id' => $newParent,
                'label' => $item['label'] ?? '',
                'url' => $item['url'] ?? null,
                'type' => $item['type'] ?? 'custom',
                'target' => $item['target'] ?? null,
                'sort_order' => $item['sort_order'] ?? 0,
            ]);
            $idMap[$item['old_id']] = $newItem->id;
        }
    }

    protected function importTemplates(array $items, string $strategy): void
    {
        foreach ($items as $item) {
            $slug = $item['slug'] ?? null;
            if (!$slug) continue;

            $existing = Template::where('slug', $slug)->first();

            if ($existing) {
                if ($strategy === 'update') {
                    $existing->update([
                        'name' => $item['name'] ?? $existing->name,
                        'content' => $item['content'] ?? $existing->content,
                        'status' => $item['status'] ?? $existing->status,
                        'conditions' => $item['conditions'] ?? $existing->conditions,
                        'settings' => $item['settings'] ?? $existing->settings,
                    ]);
                    $this->track('templates', 'updated');
                } else {
                    $this->track('templates', 'skipped');
                }
                continue;
            }

            Template::create([
                'name' => $item['name'] ?? Str::title($slug),
                'slug' => $slug,
                'type' => $item['type'] ?? 'page',
                'content' => $item['content'] ?? null,
                'status' => $item['status'] ?? 'draft',
                'is_default' => $item['is_default'] ?? false,
                'conditions' => $item['conditions'] ?? null,
                'settings' => $item['settings'] ?? null,
            ]);
            $this->track('templates', 'created');
        }
    }

    protected function importGlobalWidgets(array $items, string $strategy): void
    {
        foreach ($items as $item) {
            $name = $item['name'] ?? null;
            if (!$name) continue;

            $existing = GlobalWidget::where('name', $name)->first();

            if ($existing) {
                if ($strategy === 'update') {
                    $existing->update([
                        'content' => $item['content'] ?? $existing->content,
                        'settings' => $item['settings'] ?? $existing->settings,
                    ]);
                    $this->track('global_widgets', 'updated');
                } else {
                    $this->track('global_widgets', 'skipped');
                }
                continue;
            }

            GlobalWidget::create([
                'name' => $name,
                'type' => $item['type'] ?? 'block',
                'content' => $item['content'] ?? null,
                'settings' => $item['settings'] ?? null,
            ]);
            $this->track('global_widgets', 'created');
        }
    }

    protected function importOptions(array $items, string $strategy): void
    {
        $dangerous = ['active_theme', 'rj_version', 'site_opt_custom_js', 'site_opt_head_html'];

        foreach ($items as $item) {
            $key = $item['key'] ?? $item['name'] ?? null;

            if (!$key || in_array($key, $dangerous, true)) {
                continue;
            }

            $existing = Option::where('key', $key)->first();

            if ($existing) {
                if ($strategy === 'update') {
                    $existing->update(['value' => $item['value']]);
                    $this->track('options', 'updated');
                } else {
                    $this->track('options', 'skipped');
                }
                continue;
            }

            Option::create([
                'key' => $key,
                'value' => $item['value'] ?? '',
                'group' => $item['group'] ?? 'general',
                'type' => $item['type'] ?? 'text',
            ]);
            $this->track('options', 'created');
        }

        rj_flush_options();
    }
}
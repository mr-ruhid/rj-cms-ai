<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostType;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PostService
{
    protected array $seoFields = [
        'seo_title',
        'seo_description',
        'seo_image',
        'focus_keyword',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'robots',
        'schema_type',
    ];

    public function create(array $data, Request $request): Post
    {
        $type = $data['type'] ?? 'post';
        $title = $data['title'] ?? '';

        $post = Post::create([
            'title' => $title,
            'slug' => $this->uniqueSlug($data['slug'] ?? $title, null, $type),
            'content' => $data['content'] ?? '',
            'edit_mode' => $data['edit_mode'] ?? 'classic',
            'theme_template' => $data['theme_template'] ?? null,
            'excerpt' => $data['excerpt'] ?? null,
            'featured_image' => $data['featured_image'] ?? null,
            'type' => $type,
            'status' => $data['status'] ?? 'draft',
            'is_sticky' => !empty($data['is_sticky']),
            'allow_comments' => $data['allow_comments'] ?? true,
            'author_id' => auth()->id(),
            'template_id' => $data['template_id'] ?? null,
            'settings' => $data['settings'] ?? [],
            'meta' => $data['meta'] ?? [],
            'published_at' => $this->resolvePublishedAt($data),
        ]);

        $this->syncCategories($post, $data['categories'] ?? []);
        $this->syncTags($post, $data['tags'] ?? []);
        $this->syncTranslations($post, $this->collectTranslations($data));

        return $post->fresh();
    }

    public function update(Post $post, array $data, Request $request): Post
    {
        $title = $data['title'] ?? $post->title;

        $payload = [
            'title' => $title,
            'slug' => $this->uniqueSlug($data['slug'] ?? $title, $post->id, $post->type),
            'content' => $data['content'] ?? $post->content,
            'excerpt' => $data['excerpt'] ?? null,
            'featured_image' => $data['featured_image'] ?? null,
            'status' => $data['status'] ?? $post->status,
            'is_sticky' => !empty($data['is_sticky']),
            'allow_comments' => $data['allow_comments'] ?? $post->allow_comments,
            'template_id' => $data['template_id'] ?? null,
            'settings' => $data['settings'] ?? $post->settings,
            'meta' => $data['meta'] ?? $post->meta,
            'published_at' => $this->resolvePublishedAt($data, $post),
        ];

        if (array_key_exists('edit_mode', $data)) {
            $payload['edit_mode'] = $data['edit_mode'] ?: 'classic';
        }

        if (array_key_exists('theme_template', $data)) {
            $payload['theme_template'] = $data['theme_template'] ?: null;
        }

        $post->update($payload);

        if (array_key_exists('categories', $data)) {
            $this->syncCategories($post, $data['categories']);
        }

        if (array_key_exists('tags', $data)) {
            $this->syncTags($post, $data['tags']);
        }

        $this->syncTranslations($post, $this->collectTranslations($data));

        return $post->fresh();
    }

    public function delete(Post $post): void
    {
        $post->categories()->detach();
        $post->tags()->detach();
        $post->translations()->delete();
        $post->delete();
    }

    public function duplicate(Post $post): Post
    {
        $copy = $post->replicate(['slug', 'view_count']);
        $copy->title = $post->title . ' (Copy)';
        $copy->slug = $this->uniqueSlug($copy->title, null, $post->type);
        $copy->status = 'draft';
        $copy->published_at = null;
        $copy->view_count = 0;
        $copy->author_id = auth()->id();
        $copy->save();

        $copy->categories()->sync($post->categories()->pluck('id')->toArray());
        $copy->tags()->sync($post->tags()->pluck('id')->toArray());

        foreach ($post->translations as $t) {
            $copy->translations()->create($t->only([
                'locale',
                'title',
                'content',
                'excerpt',
                'seo_title',
                'seo_description',
                'seo_image',
                'focus_keyword',
                'canonical_url',
                'og_title',
                'og_description',
                'og_image',
                'twitter_card',
                'twitter_title',
                'twitter_description',
                'twitter_image',
                'robots',
                'schema_type',
            ]));
        }

        return $copy;
    }

    public function publish(Post $post): void
    {
        $post->update([
            'status' => 'published',
            'published_at' => $post->published_at ?? now(),
        ]);
    }

    public function unpublish(Post $post): void
    {
        $post->update(['status' => 'draft']);
    }

    public function schedule(Post $post, string $datetime): void
    {
        $post->update([
            'status' => 'scheduled',
            'published_at' => $datetime,
        ]);
    }

    public function trash(Post $post): void
    {
        $post->update(['status' => 'trash']);
    }

    public function restore(Post $post): void
    {
        $post->update(['status' => 'draft']);
    }

    public function bulkAction(array $ids, string $action): int
    {
        $query = Post::whereIn('id', $ids);

        return match ($action) {
            'publish' => $query->update(['status' => 'published', 'published_at' => now()]),
            'draft' => $query->update(['status' => 'draft']),
            'trash' => $query->update(['status' => 'trash']),
            'delete' => $query->delete(),
            'sticky' => $query->update(['is_sticky' => true]),
            'unsticky' => $query->update(['is_sticky' => false]),
            default => 0,
        };
    }

    public function uniqueSlug(string $slug, ?int $ignoreId = null, string $type = 'post'): string
    {
        $base = \Illuminate\Support\Str::slug($slug) ?: 'post';
        $candidate = $base;
        $i = 1;

        while (
            Post::query()
                ->where('slug', $candidate)
                ->where('type', $type)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $i;
            $i++;
        }

        return $candidate;
    }

    protected function syncCategories(Post $post, array $categories): void
    {
        $ids = $this->resolveIds($categories, Category::class);

        $post->categories()->sync($ids);

        $this->refreshTaxonomyCounts(Category::class, $ids);
    }

    protected function syncTags(Post $post, array $tags): void
    {
        $ids = $this->resolveIds($tags, Tag::class);

        $post->tags()->sync($ids);

        $this->refreshTaxonomyCounts(Tag::class, $ids);
    }

    protected function resolveIds(array $items, string $modelClass): array
    {
        if (empty($items)) {
            return [];
        }

        if (is_string($items)) {
            $items = array_filter(array_map('trim', explode(',', $items)));
        }

        $ids = [];

        foreach ($items as $item) {
            if (is_numeric($item)) {
                $ids[] = (int) $item;
                continue;
            }

            if (is_array($item)) {
                $id = $item['id'] ?? null;
                if ($id) $ids[] = (int) $id;
                continue;
            }

            if (is_string($item) && $item !== '') {
                $slug = \Illuminate\Support\Str::slug($item);
                $existing = $modelClass::query()->where('slug', $slug)->first();

                if (!$existing && $modelClass === Tag::class) {
                    $existing = Tag::create(['name' => $item, 'slug' => $slug]);
                }

                if ($existing) {
                    $ids[] = $existing->id;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    protected function collectTranslations(array $data): array
    {
        $base = config('app.locale');
        $locales = rj_available_locales();

        $translations = $data['translations'] ?? [];

        if (!is_array($translations)) {
            $translations = [];
        }

        $out = [];

        foreach ($locales as $locale) {
            $entry = $translations[$locale] ?? [];

            if (!is_array($entry)) {
                $entry = [];
            }

            if ($locale === $base) {
                $entry['title'] = $data['title'] ?? null;
                $entry['content'] = $data['content'] ?? null;
                $entry['excerpt'] = $data['excerpt'] ?? null;
            }

            $hasAny = false;

            foreach (array_merge(['title', 'content', 'excerpt'], $this->seoFields) as $field) {
                if (!empty($entry[$field])) {
                    $hasAny = true;
                    break;
                }
            }

            if (!$hasAny && $locale !== $base) {
                continue;
            }

            $out[$locale] = $entry;
        }

        return $out;
    }

    protected function syncTranslations(Post $post, array $translations): void
    {
        $base = config('app.locale');

        foreach ($translations as $locale => $data) {
            if (!is_string($locale) || !is_array($data)) {
                continue;
            }

            $payload = [];

            foreach (array_merge(['title', 'content', 'excerpt'], $this->seoFields) as $field) {
                if (array_key_exists($field, $data)) {
                    $value = $data[$field];

                    if (is_string($value)) {
                        $value = trim($value);
                    }

                    $payload[$field] = $value === '' ? null : $value;
                }
            }

            if ($locale === $base) {
                if (array_key_exists('title', $data) && empty($data['title'])) {
                    $payload['title'] = $post->getRawOriginal('title');
                }

                if (array_key_exists('content', $data) && empty($data['content'])) {
                    $payload['content'] = $post->getRawOriginal('content');
                }
            }

            if (empty($payload)) {
                continue;
            }

            $hasContent = false;

            foreach (['title', 'content', 'excerpt'] as $field) {
                if (!empty($payload[$field])) {
                    $hasContent = true;
                    break;
                }
            }

            if (!$hasContent && $locale !== $base) {
                continue;
            }

            $post->translations()->updateOrCreate(['locale' => $locale], $payload);
        }
    }

    protected function resolvePublishedAt(array $data, ?Post $existing = null)
    {
        $status = $data['status'] ?? ($existing?->status ?? 'draft');
        $publishedAt = $data['published_at'] ?? $existing?->published_at;

        if ($status === 'published' && !$publishedAt) {
            return now();
        }

        if ($status === 'draft' && !array_key_exists('published_at', $data)) {
            return $existing?->published_at;
        }

        return $publishedAt;
    }

    protected function refreshTaxonomyCounts(string $modelClass, array $ids): void
    {
        foreach ($ids as $id) {
            $term = $modelClass::find($id);

            if (!$term) continue;

            $count = $term->posts()->count();

            $term->update(['post_count' => $count]);
        }
    }

    public function stats(): array
    {
        return [
            'all' => Post::count(),
            'published' => Post::where('status', 'published')->count(),
            'draft' => Post::where('status', 'draft')->count(),
            'scheduled' => Post::where('status', 'scheduled')->count(),
            'trash' => Post::where('status', 'trash')->count(),
            'posts' => Post::where('type', 'post')->count(),
            'pages' => Post::where('type', 'page')->count(),
        ];
    }
}
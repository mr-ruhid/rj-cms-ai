<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $table = 'rj_posts';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'blocks',
        'edit_mode',
        'theme_template',
        'excerpt',
        'featured_image',
        'type',
        'status',
        'is_sticky',
        'allow_comments',
        'view_count',
        'author_id',
        'template_id',
        'settings',
        'meta',
        'published_at',
    ];

    protected $casts = [
        'is_sticky' => 'boolean',
        'allow_comments' => 'boolean',
        'blocks' => 'array',
        'settings' => 'array',
        'meta' => 'array',
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'rj_post_category', 'post_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'rj_post_tag', 'post_id', 'tag_id');
    }

    public function translations()
    {
        return $this->hasMany(PostTranslation::class, 'post_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSticky($query)
    {
        return $query->where('is_sticky', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getTitleAttribute($value): string
    {
        if ($this->relationLoaded('translations') || $this->translations()->exists()) {
            $locale = app()->getLocale();
            $base = config('app.locale');

            if ($locale !== $base) {
                $translation = $this->translations->firstWhere('locale', $locale);

                if ($translation && !empty($translation->title)) {
                    return $translation->title;
                }
            }
        }

        return (string) $value;
    }

    public function getContentAttribute($value): ?string
    {
        $locale = app()->getLocale();
        $base = config('app.locale');

        if ($locale !== $base) {
            $translation = $this->translations->firstWhere('locale', $locale);

            if ($translation && !empty($translation->content)) {
                return $translation->content;
            }
        }

        return $value;
    }

    public function getExcerptAttribute($value): ?string
    {
        $locale = app()->getLocale();
        $base = config('app.locale');

        if ($locale !== $base) {
            $translation = $this->translations->firstWhere('locale', $locale);

            if ($translation && !empty($translation->excerpt)) {
                return $translation->excerpt;
            }
        }

        if (!empty($value)) {
            return $value;
        }

        if (!empty($this->attributes['content'])) {
            return Str::limit(strip_tags($this->attributes['content']), 160);
        }

        return null;
    }

    protected function translationValue(string $field, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $base = config('app.locale');

        if ($locale !== $base) {
            $translation = $this->translations->firstWhere('locale', $locale);

            if ($translation && !empty($translation->{$field})) {
                return $translation->{$field};
            }
        }

        $baseTranslation = $this->translations->firstWhere('locale', $base);

        if ($baseTranslation && !empty($baseTranslation->{$field})) {
            return $baseTranslation->{$field};
        }

        return null;
    }

    public function getSeoTitleAttribute(): ?string
    {
        return $this->translationValue('seo_title') ?: $this->title;
    }

    public function getSeoDescriptionAttribute(): ?string
    {
        return $this->translationValue('seo_description') ?: $this->excerpt;
    }

    public function getSeoImageAttribute(): ?string
    {
        return $this->translationValue('seo_image') ?: $this->featured_image;
    }

    public function getSeoImageUrlAttribute(): ?string
    {
        $image = $this->seo_image;

        if (!$image) {
            return null;
        }

        return Str::startsWith($image, 'http') ? $image : asset($image);
    }

    public function getFocusKeywordAttribute(): ?string
    {
        return $this->translationValue('focus_keyword');
    }

    public function getCanonicalUrlAttribute(): ?string
    {
        return $this->translationValue('canonical_url');
    }

    public function getOgTitleAttribute(): ?string
    {
        return $this->translationValue('og_title') ?: $this->seo_title;
    }

    public function getOgDescriptionAttribute(): ?string
    {
        return $this->translationValue('og_description') ?: $this->seo_description;
    }

    public function getOgImageAttribute(): ?string
    {
        return $this->translationValue('og_image') ?: $this->seo_image;
    }

    public function getOgImageUrlAttribute(): ?string
    {
        $image = $this->og_image;

        if (!$image) {
            return null;
        }

        return Str::startsWith($image, 'http') ? $image : asset($image);
    }

    public function getTwitterCardAttribute(): ?string
    {
        return $this->translationValue('twitter_card') ?: 'summary_large_image';
    }

    public function getTwitterTitleAttribute(): ?string
    {
        return $this->translationValue('twitter_title') ?: $this->og_title;
    }

    public function getTwitterDescriptionAttribute(): ?string
    {
        return $this->translationValue('twitter_description') ?: $this->og_description;
    }

    public function getTwitterImageAttribute(): ?string
    {
        return $this->translationValue('twitter_image') ?: $this->og_image;
    }

    public function getTwitterImageUrlAttribute(): ?string
    {
        $image = $this->twitter_image;

        if (!$image) {
            return null;
        }

        return Str::startsWith($image, 'http') ? $image : asset($image);
    }

    public function getRobotsAttribute(): ?string
    {
        return $this->translationValue('robots') ?: 'index,follow';
    }

    public function getSchemaTypeAttribute(): ?string
    {
        return $this->translationValue('schema_type') ?: 'Article';
    }

    public function getCanonicalUrlFullAttribute(): ?string
    {
        $canonical = $this->canonical_url;

        if ($canonical) {
            return $canonical;
        }

        if ($this->status !== 'published' || !$this->slug) {
            return null;
        }

        return $this->type === 'page'
            ? url('/' . $this->slug)
            : url('/blog/' . $this->slug);
    }

    public function hasBlocks(): bool
    {
        return !empty($this->blocks) && !empty($this->blocks['blocks'] ?? []);
    }

    public function isBuilderMode(): bool
    {
        return $this->edit_mode === 'builder';
    }

    public function isClassicMode(): bool
    {
        return $this->edit_mode === 'classic' || empty($this->edit_mode);
    }

    public function isThemeMode(): bool
    {
        return $this->edit_mode === 'theme';
    }

    public function getTranslation(string $locale): ?PostTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }

    public function setTranslation(string $locale, array $data): PostTranslation
    {
        return $this->translations()->updateOrCreate(
            ['locale' => $locale],
            $data
        );
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'published' => __('Published'),
            'draft' => __('Draft'),
            'scheduled' => __('Scheduled'),
            'trash' => __('Trash'),
            default => ucfirst((string) $this->status),
        };
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'post' => __('Post'),
            'page' => __('Page'),
            default => ucfirst((string) $this->type),
        };
    }

    public function editModeLabel(): string
    {
        return match ($this->edit_mode) {
            'builder' => __('RJBuilder'),
            'theme' => __('Theme Template'),
            default => __('Classic Editor'),
        };
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function incrementViews(): void
    {
        $this->increment('view_count');
    }
}
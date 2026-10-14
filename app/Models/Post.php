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
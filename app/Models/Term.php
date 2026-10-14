<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Term extends Model
{
    protected $table = 'rj_terms';

    protected $fillable = [
        'taxonomy',
        'parent_id',
        'name',
        'slug',
        'description',
        'featured_image',
        'seo_title',
        'seo_description',
        'post_count',
        'sort_order',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'post_count' => 'integer',
        'sort_order' => 'integer',
        'meta' => 'array',
    ];

    public function parent()
    {
        return $this->belongsTo(Term::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Term::class, 'parent_id')->orderBy('sort_order');
    }

    public function translations()
    {
        return $this->hasMany(TermTranslation::class, 'term_id');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'rj_post_term', 'term_id', 'post_id');
    }

    public function scopeTaxonomy($query, string $taxonomy)
    {
        return $query->where('taxonomy', $taxonomy);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getNameAttribute($value): string
    {
        $locale = app()->getLocale();
        $base = config('app.locale');

        if ($locale !== $base) {
            $translation = $this->translations->firstWhere('locale', $locale);

            if ($translation && !empty($translation->name)) {
                return $translation->name;
            }
        }

        return (string) $value;
    }

    public function getDescriptionAttribute($value): ?string
    {
        $locale = app()->getLocale();
        $base = config('app.locale');

        if ($locale !== $base) {
            $translation = $this->translations->firstWhere('locale', $locale);

            if ($translation && !empty($translation->description)) {
                return $translation->description;
            }
        }

        return $value;
    }

    public function setTranslation(string $locale, array $data): TermTranslation
    {
        return $this->translations()->updateOrCreate(
            ['locale' => $locale],
            $data
        );
    }

    public function getIndentedNameAttribute(): string
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return str_repeat('— ', $depth) . $this->name;
    }

    public static function uniqueSlug(string $taxonomy, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            static::query()
                ->where('taxonomy', $taxonomy)
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function refreshPostCount(): void
    {
        $this->update(['post_count' => $this->posts()->count()]);
    }

    public static function tree(string $taxonomy, ?int $parentId = null): array
    {
        $terms = static::taxonomy($taxonomy)
            ->active()
            ->where('parent_id', $parentId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $tree = [];

        foreach ($terms as $term) {
            $tree[] = [
                'term' => $term,
                'children' => static::tree($taxonomy, $term->id),
            ];
        }

        return $tree;
    }
}
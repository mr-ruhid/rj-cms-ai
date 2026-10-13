<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'rj_categories';

    protected $fillable = [
        'slug',
        'parent_id',
        'image',
        'sort_order',
        'post_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'post_count' => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        return $this->hasOne(CategoryTranslation::class, 'category_id')
            ->where('locale', $locale);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);

        if ($translation && $translation->name) {
            return $translation->name;
        }

        $fallback = $this->translations->firstWhere('locale', config('app.locale'));

        if ($fallback && $fallback->name) {
            return $fallback->name;
        }

        return $this->translations->first()?->name ?? $this->slug;
    }

    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);

        if ($translation && $translation->description) {
            return $translation->description;
        }

        $fallback = $this->translations->firstWhere('locale', config('app.locale'));

        return $fallback?->description;
    }

    public function getSeoTitleAttribute(): ?string
    {
        $locale = app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);

        return $translation?->seo_title
            ?? $this->translations->firstWhere('locale', config('app.locale'))?->seo_title;
    }

    public function getSeoDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);

        return $translation?->seo_description
            ?? $this->translations->firstWhere('locale', config('app.locale'))?->seo_description;
    }

    public function getSeoImageAttribute(): ?string
    {
        $locale = app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);

        return $translation?->seo_image
            ?? $this->translations->firstWhere('locale', config('app.locale'))?->seo_image;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return asset($this->image);
    }

    public function getSeoImageUrlAttribute(): ?string
    {
        if (!$this->seo_image) {
            return null;
        }

        if (str_starts_with($this->seo_image, 'http')) {
            return $this->seo_image;
        }

        return asset($this->seo_image);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getFullPath(): string
    {
        $parts = [$this->slug];
        $current = $this;

        while ($current->parent) {
            $current = $current->parent;
            array_unshift($parts, $current->slug);
        }

        return implode('/', $parts);
    }

    public function getIndentedNameAttribute(): string
    {
        $depth = 0;
        $current = $this;

        while ($current->parent_id) {
            $current = $current->parent;
            $depth++;

            if ($depth > 10) break;
        }

        return str_repeat('— ', $depth) . $this->name;
    }
}
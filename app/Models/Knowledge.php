<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Knowledge extends Model
{
    protected $table = 'rj_knowledge';

    protected $fillable = [
        'slug',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(KnowledgeTranslation::class, 'knowledge_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTitleAttribute(): string
    {
        $locale = app()->getLocale();
        $base = config('app.locale', 'en');

        $translation = $this->translations->firstWhere('locale', $locale);

        if ($translation && !empty($translation->title)) {
            return $translation->title;
        }

        $baseTranslation = $this->translations->firstWhere('locale', $base);

        if ($baseTranslation && !empty($baseTranslation->title)) {
            return $baseTranslation->title;
        }

        return Str::title(str_replace('-', ' ', $this->slug));
    }

    public function getContentAttribute(): ?string
    {
        $locale = app()->getLocale();
        $base = config('app.locale', 'en');

        $translation = $this->translations->firstWhere('locale', $locale);

        if ($translation && !empty($translation->content)) {
            return $translation->content;
        }

        $baseTranslation = $this->translations->firstWhere('locale', $base);

        if ($baseTranslation && !empty($baseTranslation->content)) {
            return $baseTranslation->content;
        }

        return null;
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
}
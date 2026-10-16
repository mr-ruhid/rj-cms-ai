<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'rj_tags';

    protected $fillable = ['slug', 'post_count'];

    protected $casts = [
        'post_count' => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(TagTranslation::class, 'tag_id');
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        return $this->hasOne(TagTranslation::class, 'tag_id')
            ->where('locale', $locale);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'rj_post_tag', 'tag_id', 'post_id');
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

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('post_count')->orderBy('id');
    }
}
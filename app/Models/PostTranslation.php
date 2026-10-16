<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostTranslation extends Model
{
    protected $table = 'rj_post_translations';

    protected $fillable = [
        'post_id',
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
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }
}
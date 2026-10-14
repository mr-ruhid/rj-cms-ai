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
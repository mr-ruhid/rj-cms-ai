<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    protected $table = 'rj_category_translations';

    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'description',
        'seo_title',
        'seo_description',
        'seo_image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }
}
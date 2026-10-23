<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'rj_product_categories';

    protected $fillable = [
        'slug',
        'image',
        'parent_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(ProductCategoryTranslation::class, 'product_category_id');
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);

        if (!$translation) {
            $translation = $this->translations->firstWhere('locale', config('app.locale')) ?: $this->translations->first();
        }

        return $translation;
    }

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')->orderBy('sort_order');
    }
}

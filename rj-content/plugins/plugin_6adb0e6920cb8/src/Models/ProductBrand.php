<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
    protected $table = 'rj_product_brands';

    protected $fillable = [
        'slug',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(ProductBrandTranslation::class, 'product_brand_id');
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
}

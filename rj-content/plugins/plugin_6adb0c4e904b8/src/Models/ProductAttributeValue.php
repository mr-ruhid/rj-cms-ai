<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    protected $table = 'rj_product_attribute_values';

    protected $fillable = [
        'product_attribute_id',
        'slug',
        'color_code',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    public function translations()
    {
        return $this->hasMany(ProductAttributeValueTranslation::class, 'product_attribute_value_id');
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

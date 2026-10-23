<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    protected $table = 'rj_product_tags';

    protected $fillable = [
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(ProductTagTranslation::class, 'product_tag_id');
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

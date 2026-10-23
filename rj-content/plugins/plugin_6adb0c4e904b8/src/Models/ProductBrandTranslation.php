<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBrandTranslation extends Model
{
    protected $table = 'rj_product_brand_translations';

    protected $fillable = [
        'product_brand_id',
        'locale',
        'name',
        'description',
        'seo_title',
        'seo_description',
    ];

    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'product_brand_id');
    }
}

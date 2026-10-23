<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryTranslation extends Model
{
    protected $table = 'rj_product_category_translations';

    protected $fillable = [
        'product_category_id',
        'locale',
        'name',
        'description',
        'seo_title',
        'seo_description',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}

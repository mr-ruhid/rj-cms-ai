<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    protected $table = 'rj_product_translations';

    protected $fillable = [
        'product_id',
        'locale',
        'name',
        'short_description',
        'description',
        'seo_title',
        'seo_description',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

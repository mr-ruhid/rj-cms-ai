<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeTranslation extends Model
{
    protected $table = 'rj_product_attribute_translations';

    protected $fillable = [
        'product_attribute_id',
        'locale',
        'name',
    ];

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }
}

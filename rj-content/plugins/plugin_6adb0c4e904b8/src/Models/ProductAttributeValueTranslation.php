<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValueTranslation extends Model
{
    protected $table = 'rj_product_attribute_value_translations';

    protected $fillable = [
        'product_attribute_value_id',
        'locale',
        'name',
    ];

    public function value()
    {
        return $this->belongsTo(ProductAttributeValue::class, 'product_attribute_value_id');
    }
}

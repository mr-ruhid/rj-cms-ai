<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTagTranslation extends Model
{
    protected $table = 'rj_product_tag_translations';

    protected $fillable = [
        'product_tag_id',
        'locale',
        'name',
    ];

    public function tag()
    {
        return $this->belongsTo(ProductTag::class, 'product_tag_id');
    }
}

<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'rj_products';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_price',
        'sku',
        'stock_quantity',
        'manage_stock',
        'featured_image',
        'gallery',
        'status',
        'is_featured',
        'meta',
    ];

    protected $casts = [
        'gallery' => 'array',
        'meta' => 'array',
        'manage_stock' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
    ];
}

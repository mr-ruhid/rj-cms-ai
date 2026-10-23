<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'rj_order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'variation_id',
        'product_name',
        'variation_name',
        'price',
        'quantity',
        'total',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }
}

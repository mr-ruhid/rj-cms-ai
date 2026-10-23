<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $table = 'rj_shipping_methods';

    protected $fillable = [
        'shipping_zone_id',
        'name',
        'type',
        'cost',
        'min_amount',
        'is_active',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }
}

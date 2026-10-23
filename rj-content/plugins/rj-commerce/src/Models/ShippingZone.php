<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $table = 'rj_shipping_zones';

    protected $fillable = [
        'name',
        'regions',
    ];

    protected $casts = [
        'regions' => 'array',
    ];

    public function methods()
    {
        return $this->hasMany(ShippingMethod::class, 'shipping_zone_id');
    }
}

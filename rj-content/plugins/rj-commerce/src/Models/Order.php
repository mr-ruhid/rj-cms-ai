<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Order extends Model
{
    protected $table = 'rj_orders';

    protected $fillable = [
        'user_id',
        'status',
        'subtotal',
        'tax_total',
        'shipping_total',
        'discount_total',
        'total',
        'currency',
        'payment_method',
        'shipping_method',
        'billing_address',
        'shipping_address',
        'customer_note',
    ];

    protected $casts = [
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => __('Pending Payment'),
            'processing' => __('Processing'),
            'completed' => __('Completed'),
            'cancelled' => __('Cancelled'),
            'refunded' => __('Refunded'),
            'failed' => __('Failed'),
            default => ucfirst($this->status),
        };
    }
}

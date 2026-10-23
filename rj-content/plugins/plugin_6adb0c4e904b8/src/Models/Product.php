<?php

namespace RjCommerce\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'rj_products';

    protected $fillable = [
        'slug',
        'type',
        'sku',
        'barcode',
        'price',
        'compare_price',
        'manage_stock',
        'stock_quantity',
        'stock_status',
        'weight',
        'length',
        'width',
        'height',
        'downloadable_files',
        'download_limit',
        'download_expiry',
        'external_url',
        'license_keys_pool',
        'auto_generate_license',
        'category_id',
        'brand_id',
        'featured_image',
        'gallery',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'manage_stock' => 'boolean',
        'auto_generate_license' => 'boolean',
        'is_featured' => 'boolean',
        'downloadable_files' => 'array',
        'license_keys_pool' => 'array',
        'gallery' => 'array',
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'stock_quantity' => 'integer',
        'download_limit' => 'integer',
        'download_expiry' => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class, 'product_id');
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);

        if (!$translation) {
            $translation = $this->translations->firstWhere('locale', config('app.locale')) ?: $this->translations->first();
        }

        return $translation;
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    public function tags()
    {
        return $this->belongsToMany(ProductTag::class, 'rj_product_tag_pivot', 'product_id', 'product_tag_id');
    }

    public function isDigital(): bool
    {
        return $this->type === 'digital';
    }
}

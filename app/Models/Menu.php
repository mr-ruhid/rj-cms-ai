<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'rj_menus';

    protected $fillable = [
        'name',
        'slug',
        'location',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menu_id')->orderBy('sort_order');
    }

    public function rootItems()
    {
        return $this->items()->whereNull('parent_id');
    }

    public function tree()
    {
        $items = $this->items()->with('children.children.children')->get();
        return $items->whereNull('parent_id')->values();
    }

    public function itemCount(): int
    {
        return $this->items()->count();
    }

    public static function locations(): array
    {
        $custom = rj_get_option('menu_custom_locations');
        $custom = $custom ? json_decode($custom, true) : [];

        $defaults = [
            'header' => __('Header'),
            'footer' => __('Footer'),
            'mobile' => __('Mobile'),
            'sidebar' => __('Sidebar'),
        ];

        if (is_array($custom)) {
            foreach ($custom as $key => $label) {
                $defaults[$key] = $label;
            }
        }

        return $defaults;
    }

    public function locationLabel(): string
    {
        if (!$this->location) {
            return __('— Not assigned —');
        }

        $locations = static::locations();

        return $locations[$this->location] ?? $this->location;
    }

    public static function forLocation(string $location): ?self
    {
        return static::where('location', $location)
            ->where('is_active', true)
            ->first();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Taxonomy extends Model
{
    protected $table = 'rj_taxonomies';

    protected $fillable = [
        'name',
        'slug',
        'singular_name',
        'plural_name',
        'description',
        'is_hierarchical',
        'is_public',
        'show_in_menu',
        'post_types',
        'labels',
        'settings',
        'status',
        'author_id',
    ];

    protected $casts = [
        'is_hierarchical' => 'boolean',
        'is_public' => 'boolean',
        'show_in_menu' => 'boolean',
        'post_types' => 'array',
        'labels' => 'array',
        'settings' => 'array',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeHierarchical($query)
    {
        return $query->where('is_hierarchical', true);
    }

    public function scopeFlat($query)
    {
        return $query->where('is_hierarchical', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    public function postTypes(): array
    {
        if (!is_array($this->post_types)) {
            return [];
        }

        return $this->post_types;
    }

    public function appliesTo(string $postType): bool
    {
        return in_array($postType, $this->postTypes(), true);
    }

    public function labels(): array
    {
        $defaults = [
            'add_new' => __('Add New'),
            'add_new_item' => __('Add New :name', ['name' => $this->singular_name]),
            'edit_item' => __('Edit :name', ['name' => $this->singular_name]),
            'new_item' => __('New :name', ['name' => $this->singular_name]),
            'view_item' => __('View :name', ['name' => $this->singular_name]),
            'search_items' => __('Search :name', ['name' => $this->plural_name]),
            'not_found' => __('No :name found', ['name' => strtolower($this->plural_name)]),
            'all_items' => __('All :name', ['name' => $this->plural_name]),
            'parent_item' => __('Parent :name', ['name' => $this->singular_name]),
        ];

        if (!is_array($this->labels)) {
            return $defaults;
        }

        return array_merge($defaults, $this->labels);
    }

    public function settings(): array
    {
        $defaults = [
            'rewrite_slug' => $this->slug,
            'hierarchical_urls' => $this->is_hierarchical,
            'show_admin_column' => true,
        ];

        if (!is_array($this->settings)) {
            return $defaults;
        }

        return array_merge($defaults, $this->settings);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
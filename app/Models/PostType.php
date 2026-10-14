<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostType extends Model
{
    protected $table = 'rj_post_types';

    protected $fillable = [
        'name',
        'slug',
        'singular_name',
        'plural_name',
        'icon',
        'menu_position',
        'is_public',
        'has_archive',
        'is_hierarchical',
        'supports_comments',
        'supports_builder',
        'supports_thumbnail',
        'supports',
        'labels',
        'settings',
        'status',
        'author_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'has_archive' => 'boolean',
        'is_hierarchical' => 'boolean',
        'supports_comments' => 'boolean',
        'supports_builder' => 'boolean',
        'supports_thumbnail' => 'boolean',
        'supports' => 'array',
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

    public function scopeOrdered($query)
    {
        return $query->orderBy('menu_position')->orderBy('name');
    }

    public function supports(): array
    {
        $defaults = [
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'categories',
            'tags',
            'comments',
            'builder',
            'revisions',
        ];

        if (!is_array($this->supports)) {
            return $defaults;
        }

        return array_values(array_unique(array_merge($defaults, $this->supports)));
    }

    public function supportsFeature(string $feature): bool
    {
        return in_array($feature, $this->supports(), true);
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
            'archive_slug' => $this->slug,
            'capability_type' => $this->slug,
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

    public static function availableIcons(): array
    {
        return [
            'file' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>',
            'image' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>',
            'briefcase' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>',
            'user' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
            'star' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
            'music' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>',
            'video' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>',
            'calendar' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
            'tag' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><path d="M7 7h.01"/></svg>',
            'layers' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>',
        ];
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $table = 'rj_menu_items';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'type',
        'label',
        'url',
        'target',
        'object_id',
        'object_type',
        'sort_order',
        'css_class',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    public static function types(): array
    {
        return [
            'custom' => ['label' => __('Custom Link'), 'icon' => '🔗'],
            'page' => ['label' => __('Page'), 'icon' => '📄'],
            'post' => ['label' => __('Post'), 'icon' => '📝'],
            'category' => ['label' => __('Category'), 'icon' => '📁'],
            'tag' => ['label' => __('Tag'), 'icon' => '🏷️'],
        ];
    }

    public function typeLabel(): string
    {
        return static::types()[$this->type]['label'] ?? $this->type;
    }

    public function resolvedUrl(): string
    {
        if ($this->type === 'custom' && $this->url) {
            return $this->url;
        }

        if ($this->type === 'category' && $this->object_id) {
            $cat = Category::find($this->object_id);

            if ($cat) {
                return url('/category/' . $cat->slug);
            }
        }

        if ($this->type === 'tag' && $this->object_id) {
            $tag = Tag::find($this->object_id);

            if ($tag) {
                return url('/tag/' . $tag->slug);
            }
        }

        if ($this->url) {
            return $this->url;
        }

        return '#';
    }
}
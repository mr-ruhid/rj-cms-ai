<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'rj_templates';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'content',
        'status',
        'is_default',
        'conditions',
        'settings',
        'author_id',
    ];

    protected $casts = [
        'content' => 'array',
        'conditions' => 'array',
        'settings' => 'array',
        'is_default' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function blocks(): array
    {
        return $this->content['blocks'] ?? [];
    }

    public function blockCount(): int
    {
        return count($this->blocks());
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public static function types(): array
    {
        return [
            'header' => ['label' => __('Header'), 'icon' => 'layout-top'],
            'footer' => ['label' => __('Footer'), 'icon' => 'layout-bottom'],
            'page' => ['label' => __('Page'), 'icon' => 'file'],
            'popup' => ['label' => __('Popup'), 'icon' => 'window'],
            'block' => ['label' => __('Block'), 'icon' => 'grid'],
        ];
    }

    public function typeLabel(): string
    {
        return static::types()[$this->type]['label'] ?? $this->type;
    }

    public function popupSettings(): array
    {
        $defaults = [
            'trigger' => 'page_load',
            'trigger_value' => '',
            'delay' => 0,
            'frequency' => 'once_per_session',
            'position' => 'center',
            'width' => 'md',
            'overlay' => true,
            'overlay_color' => '#000000',
            'overlay_opacity' => 60,
            'close_on_overlay' => true,
            'close_button' => true,
            'animation' => 'fadeInUp',
            'animation_duration' => 'md',
            'auto_close' => 0,
        ];

        if (!is_array($this->settings)) {
            return $defaults;
        }

        return array_merge($defaults, $this->settings);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomField extends Model
{
    protected $table = 'rj_custom_fields';

    protected $fillable = [
        'name',
        'slug',
        'key',
        'type',
        'description',
        'placeholder',
        'default_value',
        'options',
        'validation',
        'settings',
        'post_types',
        'taxonomies',
        'is_required',
        'is_repeatable',
        'location',
        'sort_order',
        'status',
        'author_id',
    ];

    protected $casts = [
        'options' => 'array',
        'validation' => 'array',
        'settings' => 'array',
        'post_types' => 'array',
        'taxonomies' => 'array',
        'is_required' => 'boolean',
        'is_repeatable' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function values()
    {
        return $this->hasMany(CustomFieldValue::class, 'field_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLocation($query, string $location)
    {
        return $query->where('location', $location);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function options(): array
    {
        if (!is_array($this->options)) {
            return [];
        }

        return $this->options;
    }

    public function validation(): array
    {
        if (!is_array($this->validation)) {
            return [];
        }

        return $this->validation;
    }

    public function settings(): array
    {
        $defaults = [
            'show_in_rest' => true,
            'wrapper_class' => '',
            'instructions_position' => 'above',
        ];

        if (!is_array($this->settings)) {
            return $defaults;
        }

        return array_merge($defaults, $this->settings);
    }

    public function postTypes(): array
    {
        if (!is_array($this->post_types)) {
            return [];
        }

        return $this->post_types;
    }

    public function taxonomies(): array
    {
        if (!is_array($this->taxonomies)) {
            return [];
        }

        return $this->taxonomies;
    }

    public function appliesToPostType(string $postType): bool
    {
        if ($this->location !== 'post') {
            return false;
        }

        $postTypes = $this->postTypes();

        if (empty($postTypes)) {
            return true;
        }

        return in_array($postType, $postTypes, true);
    }

    public function appliesToTaxonomy(string $taxonomy): bool
    {
        if ($this->location !== 'taxonomy') {
            return false;
        }

        $taxonomies = $this->taxonomies();

        if (empty($taxonomies)) {
            return true;
        }

        return in_array($taxonomy, $taxonomies, true);
    }

    public function validationRules(): array
    {
        $rules = [];

        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        $config = $this->validation();

        $typeRules = match ($this->type) {
            'email' => ['email'],
            'url' => ['url'],
            'number' => ['numeric'],
            'date' => ['date'],
            'file' => ['file'],
            'image' => ['image'],
            default => ['string'],
        };

        $rules = array_merge($rules, $typeRules);

        if (!empty($config['min_length'])) {
            $rules[] = 'min:' . (int) $config['min_length'];
        }

        if (!empty($config['max_length'])) {
            $rules[] = 'max:' . (int) $config['max_length'];
        }

        if (!empty($config['min'])) {
            $rules[] = 'min:' . (int) $config['min'];
        }

        if (!empty($config['max'])) {
            $rules[] = 'max:' . (int) $config['max'];
        }

        if (!empty($config['pattern'])) {
            $rules[] = 'regex:/' . $config['pattern'] . '/';
        }

        if (in_array($this->type, ['select', 'radio'], true) && !empty($this->options())) {
            $values = array_column($this->options(), 'value');

            if (!empty($values)) {
                $rules[] = 'in:' . implode(',', $values);
            }
        }

        if ($this->is_repeatable) {
            $rules = ['array'];
            $innerRules = array_filter($rules, fn($r) => $r !== 'array');
            $rules = ['array'];
        }

        return $rules;
    }

    public function defaultReturnFormat(): string
    {
        return match ($this->type) {
            'image', 'file' => 'array',
            'gallery' => 'array',
            'repeater' => 'array',
            'true_false' => 'boolean',
            'number', 'range' => 'integer',
            default => 'string',
        };
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

    public static function uniqueKey(string $key, ?int $ignoreId = null): string
    {
        $base = preg_replace('/[^a-z0-9_]/i', '_', Str::snake($key));
        $candidate = $base;
        $i = 1;

        while (
            static::query()
                ->where('key', $candidate)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base . '_' . $i;
            $i++;
        }

        return $candidate;
    }

    public static function types(): array
    {
        return [
            'text' => ['label' => __('Text'), 'group' => 'basic'],
            'textarea' => ['label' => __('Textarea'), 'group' => 'basic'],
            'number' => ['label' => __('Number'), 'group' => 'basic'],
            'email' => ['label' => __('Email'), 'group' => 'basic'],
            'url' => ['label' => __('URL'), 'group' => 'basic'],
            'password' => ['label' => __('Password'), 'group' => 'basic'],

            'select' => ['label' => __('Select'), 'group' => 'choice'],
            'radio' => ['label' => __('Radio'), 'group' => 'choice'],
            'checkbox' => ['label' => __('Checkbox'), 'group' => 'choice'],
            'true_false' => ['label' => __('True / False'), 'group' => 'choice'],

            'date' => ['label' => __('Date'), 'group' => 'date'],
            'time' => ['label' => __('Time'), 'group' => 'date'],
            'datetime' => ['label' => __('Date & Time'), 'group' => 'date'],

            'image' => ['label' => __('Image'), 'group' => 'media'],
            'file' => ['label' => __('File'), 'group' => 'media'],
            'gallery' => ['label' => __('Gallery'), 'group' => 'media'],
            'video' => ['label' => __('Video'), 'group' => 'media'],

            'color' => ['label' => __('Color'), 'group' => 'advanced'],
            'range' => ['label' => __('Range'), 'group' => 'advanced'],
            'wysiwyg' => ['label' => __('WYSIWYG Editor'), 'group' => 'advanced'],
            'code' => ['label' => __('Code'), 'group' => 'advanced'],
            'oembed' => ['label' => __('oEmbed'), 'group' => 'advanced'],

            'relationship' => ['label' => __('Relationship'), 'group' => 'relational'],
            'taxonomy' => ['label' => __('Taxonomy'), 'group' => 'relational'],
            'user' => ['label' => __('User'), 'group' => 'relational'],
            'post_object' => ['label' => __('Post Object'), 'group' => 'relational'],

            'repeater' => ['label' => __('Repeater'), 'group' => 'layout'],
            'group' => ['label' => __('Group'), 'group' => 'layout'],
            'flexible_content' => ['label' => __('Flexible Content'), 'group' => 'layout'],
        ];
    }

    public static function typeGroups(): array
    {
        return [
            'basic' => __('Basic'),
            'choice' => __('Choice'),
            'date' => __('Date & Time'),
            'media' => __('Media'),
            'advanced' => __('Advanced'),
            'relational' => __('Relational'),
            'layout' => __('Layout'),
        ];
    }

    public function typeLabel(): string
    {
        return static::types()[$this->type]['label'] ?? $this->type;
    }
}
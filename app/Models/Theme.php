<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $table = 'rj_themes';

    protected $fillable = [
        'name',
        'slug',
        'version',
        'description',
        'author',
        'author_url',
        'preview_image',
        'folder',
        'status',
        'is_child',
        'parent_slug',
        'supports',
        'settings',
        'metadata',
        'error_message',
        'activated_at',
    ];

    protected $casts = [
        'is_child' => 'boolean',
        'supports' => 'array',
        'settings' => 'array',
        'metadata' => 'array',
        'activated_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeChild($query)
    {
        return $query->where('is_child', true);
    }

    public function scopeParent($query)
    {
        return $query->where('is_child', false);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isChild(): bool
    {
        return $this->is_child === true;
    }

public function supports(): array
{
    $defaults = [
        'header',
        'footer',
        'sidebar',
        'widgets',
        'menus',
        'custom_logo',
        'featured_image',
        'post_thumbnail',
        'customizer',
        'builder',
    ];

    if (!is_array($this->supports)) {
        return $defaults;
    }

    $features = $defaults;

    foreach ($this->supports as $key => $value) {
        if (is_int($key) && is_string($value)) {
            $features[] = $value;
        } elseif (is_string($key) && !empty($value)) {
            $features[] = $key;
        }
    }

    return array_values(array_unique($features));
}

    public function supportsFeature(string $feature): bool
    {
        return in_array($feature, $this->supports(), true);
    }

    public function settings(): array
    {
        if (!is_array($this->settings)) {
            return [];
        }

        return $this->settings;
    }

    public function metadata(): array
    {
        if (!is_array($this->metadata)) {
            return [];
        }

        return $this->metadata;
    }

    public function parent()
    {
        if (!$this->is_child || !$this->parent_slug) {
            return null;
        }

        return static::where('slug', $this->parent_slug)->first();
    }

    public function children()
    {
        return static::where('parent_slug', $this->slug)->get();
    }

    public function path(): string
    {
        return base_path('rj-content/themes/' . $this->folder);
    }

    public function viewPath(string $view = ''): string
    {
        $path = $this->path() . '/views';

        if ($view !== '') {
            $path .= '/' . ltrim($view, '/');
        }

        return $path;
    }

    public function assetUrl(string $relative = ''): string
    {
        $url = url('rj-content/themes/' . $this->folder);

        if ($relative !== '') {
            $url .= '/' . ltrim($relative, '/');
        }

        return $url;
    }

    public function previewUrl(): ?string
    {
        if (!$this->preview_image) {
            return null;
        }

        if (preg_match('/^https?:\/\//', $this->preview_image)) {
            return $this->preview_image;
        }

        return $this->assetUrl($this->preview_image);
    }

    public function customizations()
    {
        return $this->hasMany(ThemeCustomization::class, 'theme_slug', 'slug');
    }

    public function getCustomization(string $section, string $key, $default = null)
    {
        $row = $this->customizations()
            ->where('section', $section)
            ->where('key', $key)
            ->first();

        if (!$row) {
            return $default;
        }

        $decoded = json_decode($row->value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $row->value;
    }

    public function setCustomization(string $section, string $key, $value): ThemeCustomization
    {
        $encoded = is_scalar($value) || $value === null
            ? (string) $value
            : json_encode($value, JSON_UNESCAPED_UNICODE);

        return ThemeCustomization::updateOrCreate(
            [
                'theme_slug' => $this->slug,
                'section' => $section,
                'key' => $key,
            ],
            [
                'value' => $encoded,
            ]
        );
    }

    public function sectionCustomizations(string $section): array
    {
        $out = [];

        foreach ($this->customizations()->where('section', $section)->get() as $row) {
            $decoded = json_decode($row->value, true);

            $out[$row->key] = json_last_error() === JSON_ERROR_NONE ? $decoded : $row->value;
        }

        return $out;
    }

    public function markAsActive(): void
    {
        static::where('status', 'active')->update(['status' => 'inactive']);

        $this->update([
            'status' => 'active',
            'activated_at' => now(),
            'error_message' => null,
        ]);

        rj_set_option('active_theme', $this->slug);
    }

    public function markAsInactive(): void
    {
        $this->update(['status' => 'inactive']);

        if (rj_get_option('active_theme') === $this->slug) {
            rj_set_option('active_theme', '');
        }
    }

    public function markAsError(string $message): void
    {
        $this->update([
            'status' => 'error',
            'error_message' => $message,
        ]);
    }

    public static function active(): ?Theme
    {
        $slug = rj_get_option('active_theme', '');

        if ($slug) {
            $theme = static::where('slug', $slug)->where('status', 'active')->first();

            if ($theme) {
                return $theme;
            }
        }

        return static::where('status', 'active')->first();
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = \Illuminate\Support\Str::slug($name);
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

    public static function statuses(): array
    {
        return [
            'active' => ['label' => __('Active'), 'color' => '#10b981'],
            'inactive' => ['label' => __('Inactive'), 'color' => '#6b7280'],
            'error' => ['label' => __('Error'), 'color' => '#ef4444'],
        ];
    }

    public function statusLabel(): string
    {
        return static::statuses()[$this->status]['label'] ?? $this->status;
    }
}
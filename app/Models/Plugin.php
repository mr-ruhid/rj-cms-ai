<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $table = 'rj_plugins';

    protected $fillable = [
        'name',
        'slug',
        'version',
        'description',
        'author',
        'author_url',
        'plugin_url',
        'folder',
        'main_file',
        'status',
        'requires',
        'metadata',
        'settings',
        'error_message',
        'activated_at',
        'deactivated_at',
        'activated_by',
    ];

    protected $casts = [
        'requires' => 'array',
        'metadata' => 'array',
        'settings' => 'array',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    public function activator()
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeError($query)
    {
        return $query->where('status', 'error');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function hasError(): bool
    {
        return $this->status === 'error';
    }

    public function requires(): array
    {
        if (!is_array($this->requires)) {
            return [
                'php' => '8.2',
                'laravel' => '11.0',
                'rj_cms' => '1.0.0',
            ];
        }

        return $this->requires;
    }

    public function metadata(): array
    {
        if (!is_array($this->metadata)) {
            return [];
        }

        return $this->metadata;
    }

    public function settings(): array
    {
        if (!is_array($this->settings)) {
            return [];
        }

        return $this->settings;
    }

    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings();

        return $settings[$key] ?? $default;
    }

    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings();
        $settings[$key] = $value;

        $this->update(['settings' => $settings]);
    }

    public function path(): string
    {
        return base_path('rj-content/plugins/' . $this->folder);
    }

    public function mainFilePath(): string
    {
        return $this->path() . '/' . $this->main_file;
    }

    public function assetUrl(string $relative = ''): string
    {
        $url = url('rj-content/plugins/' . $this->folder);

        if ($relative !== '') {
            $url .= '/' . ltrim($relative, '/');
        }

        return $url;
    }

    public function meetsRequirements(): bool
    {
        $requirements = $this->requires();

        if (!empty($requirements['php']) && version_compare(PHP_VERSION, $requirements['php'], '<')) {
            return false;
        }

        if (!empty($requirements['laravel']) && version_compare(app()->version(), $requirements['laravel'], '<')) {
            return false;
        }

        if (!empty($requirements['rj_cms']) && defined('RJ_CMS_VERSION') && version_compare(RJ_CMS_VERSION, $requirements['rj_cms'], '<')) {
            return false;
        }

        return true;
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

    public function markAsActive(): void
    {
        $this->update([
            'status' => 'active',
            'error_message' => null,
            'activated_at' => now(),
            'deactivated_at' => null,
            'activated_by' => auth()->id(),
        ]);
    }

    public function markAsInactive(): void
    {
        $this->update([
            'status' => 'inactive',
            'deactivated_at' => now(),
        ]);
    }

    public function markAsError(string $message): void
    {
        $this->update([
            'status' => 'error',
            'error_message' => $message,
        ]);
    }
}
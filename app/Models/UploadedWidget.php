<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadedWidget extends Model
{
    protected $table = 'rj_widgets';

    protected $fillable = [
        'name',
        'slug',
        'class',
        'version',
        'description',
        'author',
        'author_url',
        'icon',
        'category',
        'folder',
        'main_file',
        'status',
        'schema',
        'defaults',
        'requires',
        'metadata',
        'error_message',
        'uploaded_by',
    ];

    protected $casts = [
        'schema' => 'array',
        'defaults' => 'array',
        'requires' => 'array',
        'metadata' => 'array',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
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

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function schema(): array
    {
        if (!is_array($this->schema)) {
            return [];
        }

        return $this->schema;
    }

    public function defaults(): array
    {
        if (!is_array($this->defaults)) {
            return [];
        }

        return $this->defaults;
    }

    public function requires(): array
    {
        if (!is_array($this->requires)) {
            return [];
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

    public function path(): string
    {
        return base_path('rj-content/widgets/' . $this->folder);
    }

    public function mainFilePath(): string
    {
        return $this->path() . '/' . $this->main_file;
    }

    public function assetUrl(string $relative = ''): string
    {
        $url = url('rj-content/widgets/' . $this->folder);

        if ($relative !== '') {
            $url .= '/' . ltrim($relative, '/');
        }

        return $url;
    }

    public function toWidgetArray(): array
    {
        return [
            'id' => $this->slug,
            'name' => $this->name,
            'category' => $this->category,
            'icon' => $this->icon,
            'schema' => $this->schema(),
            'defaults' => $this->defaults(),
            'source' => 'uploaded',
            'class' => $this->class,
        ];
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
        ]);
    }

    public function markAsInactive(): void
    {
        $this->update(['status' => 'inactive']);
    }

    public function markAsError(string $message): void
    {
        $this->update([
            'status' => 'error',
            'error_message' => $message,
        ]);
    }
}
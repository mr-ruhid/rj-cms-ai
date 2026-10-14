<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalWidget extends Model
{
    protected $table = 'rj_global_widgets';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'settings',
        'children',
        'description',
        'status',
        'author_id',
    ];

    protected $casts = [
        'settings' => 'array',
        'children' => 'array',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function toBlock(): array
    {
        return [
            'id' => 'gw_' . $this->id,
            'type' => $this->type,
            'settings' => $this->settings ?? [],
            'children' => $this->children ?? [],
            'global_id' => $this->id,
            'global_name' => $this->name,
        ];
    }

    public function blockCount(): int
    {
        if (!is_array($this->children)) {
            return 0;
        }

        return $this->countChildren($this->children);
    }

    protected function countChildren(array $children): int
    {
        $count = 0;

        foreach ($children as $child) {
            if (!is_array($child)) {
                continue;
            }

            $count++;

            if (!empty($child['children']) && is_array($child['children'])) {
                $count += $this->countChildren($child['children']);
            }
        }

        return $count;
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
}
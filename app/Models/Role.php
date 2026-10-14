<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'rj_roles';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
        'level',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'level' => 'integer',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'rj_role_permission', 'role_id', 'permission_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'rj_user_role', 'role_id', 'user_id');
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('level');
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->slug === 'super-admin') {
            return true;
        }

        return $this->permissions->contains('slug', $slug);
    }

    public function givePermission($permission): void
    {
        $id = $permission instanceof Permission ? $permission->id : (int) $permission;

        if (!$this->permissions()->where('permission_id', $id)->exists()) {
            $this->permissions()->attach($id);
            $this->load('permissions');
        }
    }

    public function revokePermission($permission): void
    {
        $id = $permission instanceof Permission ? $permission->id : (int) $permission;
        $this->permissions()->detach($id);
        $this->load('permissions');
    }

    public function syncPermissions(array $ids): void
    {
        $this->permissions()->sync($ids);
        $this->load('permissions');
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
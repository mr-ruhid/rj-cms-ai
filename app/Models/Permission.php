<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'rj_permissions';

    protected $fillable = [
        'name',
        'slug',
        'group',
        'description',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'rj_role_permission', 'permission_id', 'role_id');
    }

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('group')->orderBy('name');
    }

    public static function grouped(): array
    {
        $groups = [];

        foreach (static::ordered()->get() as $permission) {
            $key = $permission->group ?: 'general';

            if (!isset($groups[$key])) {
                $groups[$key] = [];
            }

            $groups[$key][] = $permission;
        }

        return $groups;
    }

    public static function groupLabels(): array
    {
        return [
            'general' => __('General'),
            'content' => __('Content'),
            'media' => __('Media'),
            'users' => __('Users'),
            'design' => __('Design'),
            'settings' => __('Settings'),
            'plugins' => __('Plugins'),
            'tools' => __('Tools'),
        ];
    }

    public function groupLabel(): string
    {
        return static::groupLabels()[$this->group] ?? ($this->group ?: __('General'));
    }

    public static function defaults(): array
    {
        return [
            ['name' => 'Access Dashboard', 'slug' => 'dashboard.view', 'group' => 'general'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'group' => 'settings'],
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'group' => 'users'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'group' => 'users'],

            ['name' => 'View Posts', 'slug' => 'posts.view', 'group' => 'content'],
            ['name' => 'Create Posts', 'slug' => 'posts.create', 'group' => 'content'],
            ['name' => 'Edit Posts', 'slug' => 'posts.edit', 'group' => 'content'],
            ['name' => 'Delete Posts', 'slug' => 'posts.delete', 'group' => 'content'],
            ['name' => 'Publish Posts', 'slug' => 'posts.publish', 'group' => 'content'],

            ['name' => 'View Pages', 'slug' => 'pages.view', 'group' => 'content'],
            ['name' => 'Create Pages', 'slug' => 'pages.create', 'group' => 'content'],
            ['name' => 'Edit Pages', 'slug' => 'pages.edit', 'group' => 'content'],
            ['name' => 'Delete Pages', 'slug' => 'pages.delete', 'group' => 'content'],

            ['name' => 'View Categories', 'slug' => 'categories.view', 'group' => 'content'],
            ['name' => 'Manage Categories', 'slug' => 'categories.manage', 'group' => 'content'],
            ['name' => 'View Tags', 'slug' => 'tags.view', 'group' => 'content'],
            ['name' => 'Manage Tags', 'slug' => 'tags.manage', 'group' => 'content'],

            ['name' => 'View Comments', 'slug' => 'comments.view', 'group' => 'content'],
            ['name' => 'Moderate Comments', 'slug' => 'comments.moderate', 'group' => 'content'],
            ['name' => 'Delete Comments', 'slug' => 'comments.delete', 'group' => 'content'],

            ['name' => 'View Media', 'slug' => 'media.view', 'group' => 'media'],
            ['name' => 'Upload Media', 'slug' => 'media.upload', 'group' => 'media'],
            ['name' => 'Delete Media', 'slug' => 'media.delete', 'group' => 'media'],

            ['name' => 'Use Builder', 'slug' => 'builder.use', 'group' => 'design'],
            ['name' => 'Manage Templates', 'slug' => 'templates.manage', 'group' => 'design'],
            ['name' => 'Manage Menus', 'slug' => 'menus.manage', 'group' => 'design'],
            ['name' => 'Manage Themes', 'slug' => 'themes.manage', 'group' => 'design'],
            ['name' => 'Manage Fonts', 'slug' => 'fonts.manage', 'group' => 'design'],

            ['name' => 'Manage Plugins', 'slug' => 'plugins.manage', 'group' => 'plugins'],
            ['name' => 'Manage Updates', 'slug' => 'updates.manage', 'group' => 'tools'],
            ['name' => 'Manage Backups', 'slug' => 'backups.manage', 'group' => 'tools'],
            ['name' => 'View Logs', 'slug' => 'logs.view', 'group' => 'tools'],
        ];
    }
}
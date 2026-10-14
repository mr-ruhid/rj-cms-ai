<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = Permission::defaults();

        foreach ($permissions as $item) {
            Permission::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'group' => $item['group'] ?? 'general',
                    'description' => $item['description'] ?? null,
                ]
            );
        }

        $this->seedRoles();
    }

    protected function seedRoles(): void
    {
        $superAdmin = Role::updateOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full system access. Cannot be deleted.',
                'is_system' => true,
                'level' => 100,
            ]
        );

        $superAdmin->syncPermissions(Permission::pluck('id')->toArray());

        $admin = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Site administrator with most capabilities.',
                'is_system' => true,
                'level' => 80,
            ]
        );

        $adminSlugs = [
            'dashboard.view',
            'posts.view', 'posts.create', 'posts.edit', 'posts.delete', 'posts.publish',
            'pages.view', 'pages.create', 'pages.edit', 'pages.delete',
            'categories.view', 'categories.manage',
            'tags.view', 'tags.manage',
            'comments.view', 'comments.moderate', 'comments.delete',
            'media.view', 'media.upload', 'media.delete',
            'builder.use', 'templates.manage', 'menus.manage', 'themes.manage', 'fonts.manage',
            'backups.manage', 'logs.view',
        ];

        $admin->syncPermissions(
            Permission::whereIn('slug', $adminSlugs)->pluck('id')->toArray()
        );

        $editor = Role::updateOrCreate(
            ['slug' => 'editor'],
            [
                'name' => 'Editor',
                'description' => 'Can manage content but not users or settings.',
                'is_system' => true,
                'level' => 60,
            ]
        );

        $editorSlugs = [
            'dashboard.view',
            'posts.view', 'posts.create', 'posts.edit', 'posts.delete', 'posts.publish',
            'pages.view', 'pages.create', 'pages.edit', 'pages.delete',
            'categories.view', 'categories.manage',
            'tags.view', 'tags.manage',
            'comments.view', 'comments.moderate',
            'media.view', 'media.upload',
            'builder.use', 'menus.manage',
        ];

        $editor->syncPermissions(
            Permission::whereIn('slug', $editorSlugs)->pluck('id')->toArray()
        );

        $author = Role::updateOrCreate(
            ['slug' => 'author'],
            [
                'name' => 'Author',
                'description' => 'Can create and manage own posts.',
                'is_system' => true,
                'level' => 40,
            ]
        );

        $authorSlugs = [
            'dashboard.view',
            'posts.view', 'posts.create', 'posts.edit', 'posts.publish',
            'media.view', 'media.upload',
            'comments.view',
            'builder.use',
        ];

        $author->syncPermissions(
            Permission::whereIn('slug', $authorSlugs)->pluck('id')->toArray()
        );

        $contributor = Role::updateOrCreate(
            ['slug' => 'contributor'],
            [
                'name' => 'Contributor',
                'description' => 'Can create posts but cannot publish.',
                'is_system' => true,
                'level' => 20,
            ]
        );

        $contributorSlugs = [
            'dashboard.view',
            'posts.view', 'posts.create', 'posts.edit',
            'media.view', 'media.upload',
            'builder.use',
        ];

        $contributor->syncPermissions(
            Permission::whereIn('slug', $contributorSlugs)->pluck('id')->toArray()
        );

        $subscriber = Role::updateOrCreate(
            ['slug' => 'subscriber'],
            [
                'name' => 'Subscriber',
                'description' => 'Basic read-only access.',
                'is_system' => true,
                'level' => 10,
            ]
        );

        $subscriber->syncPermissions(
            Permission::whereIn('slug', ['dashboard.view'])->pluck('id')->toArray()
        );
    }
}
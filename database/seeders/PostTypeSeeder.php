<?php

namespace Database\Seeders;

use App\Models\PostType;
use Illuminate\Database\Seeder;

class PostTypeSeeder extends Seeder
{
    public function run(): void
    {
        PostType::updateOrCreate(
            ['slug' => 'post'],
            [
                'name' => 'Posts',
                'singular_name' => 'Post',
                'plural_name' => 'Posts',
                'icon' => 'file',
                'menu_position' => '5',
                'is_public' => true,
                'has_archive' => true,
                'is_hierarchical' => false,
                'supports_comments' => true,
                'supports_builder' => true,
                'supports_thumbnail' => true,
                'supports' => [
                    'title', 'editor', 'excerpt', 'thumbnail',
                    'categories', 'tags', 'comments', 'builder', 'revisions',
                ],
                'settings' => [
                    'rewrite_slug' => 'blog',
                    'archive_slug' => 'blog',
                    'is_core' => true,
                ],
                'status' => 'active',
            ]
        );

        PostType::updateOrCreate(
            ['slug' => 'page'],
            [
                'name' => 'Pages',
                'singular_name' => 'Page',
                'plural_name' => 'Pages',
                'icon' => 'layers',
                'menu_position' => '6',
                'is_public' => true,
                'has_archive' => false,
                'is_hierarchical' => true,
                'supports_comments' => false,
                'supports_builder' => true,
                'supports_thumbnail' => true,
                'supports' => [
                    'title', 'editor', 'thumbnail',
                    'builder', 'revisions', 'page_attributes',
                ],
                'settings' => [
                    'rewrite_slug' => '',
                    'is_core' => true,
                ],
                'status' => 'active',
            ]
        );
    }
}
<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use Illuminate\Database\Seeder;

class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        Taxonomy::updateOrCreate(
            ['slug' => 'category'],
            [
                'name' => 'Categories',
                'singular_name' => 'Category',
                'plural_name' => 'Categories',
                'description' => 'Organize posts into hierarchical categories.',
                'is_hierarchical' => true,
                'is_public' => true,
                'show_in_menu' => true,
                'post_types' => ['post'],
                'settings' => [
                    'rewrite_slug' => 'category',
                    'hierarchical_urls' => true,
                    'show_admin_column' => true,
                    'is_core' => true,
                ],
                'status' => 'active',
            ]
        );

        Taxonomy::updateOrCreate(
            ['slug' => 'tag'],
            [
                'name' => 'Tags',
                'singular_name' => 'Tag',
                'plural_name' => 'Tags',
                'description' => 'Flat keywords to describe posts.',
                'is_hierarchical' => false,
                'is_public' => true,
                'show_in_menu' => true,
                'post_types' => ['post'],
                'settings' => [
                    'rewrite_slug' => 'tag',
                    'hierarchical_urls' => false,
                    'show_admin_column' => true,
                    'is_core' => true,
                ],
                'status' => 'active',
            ]
        );
    }
}
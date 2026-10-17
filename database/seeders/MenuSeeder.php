<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        if (Menu::where('location', 'header')->exists()) {
            return;
        }

        $menu = Menu::create([
            'name' => 'Header Menu',
            'slug' => 'header-menu',
            'location' => 'header',
            'description' => 'Main navigation menu.',
            'is_active' => true,
        ]);

        $items = [
            ['label' => 'Home', 'type' => 'custom', 'url' => '/', 'sort_order' => 1],
            ['label' => 'About', 'type' => 'page', 'slug' => 'about', 'sort_order' => 2],
            ['label' => 'Blog', 'type' => 'custom', 'url' => '/blog', 'sort_order' => 3],
            ['label' => 'Contact', 'type' => 'page', 'slug' => 'contact', 'sort_order' => 4],
        ];

        foreach ($items as $data) {
            $objectId = null;

            if ($data['type'] === 'page') {
                $objectId = Post::where('slug', $data['slug'])->where('type', 'page')->value('id');
            }

            MenuItem::create([
                'menu_id' => $menu->id,
                'parent_id' => null,
                'type' => $data['type'],
                'label' => $data['label'],
                'translations' => null,
                'url' => $data['url'] ?? null,
                'target' => '_self',
                'object_id' => $objectId,
                'object_type' => $data['type'] === 'page' ? 'page' : null,
                'sort_order' => $data['sort_order'],
                'css_class' => null,
                'icon' => null,
                'is_active' => true,
            ]);
        }
    }
}
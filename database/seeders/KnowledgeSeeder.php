<?php

namespace Database\Seeders;

use App\Models\Knowledge;
use Illuminate\Database\Seeder;

class KnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'slug' => 'info',
                'title' => 'About RJ CMS',
                'category' => 'info',
                'content' => '<p>RJ CMS is a modern, self-hosted Content Management System built on Laravel 12. It offers a WordPress-like experience but with a cleaner, faster, and more modern architecture.</p><p>Key features include:</p><ul><li>Visual Page Builder (RJBuilder)</li><li>Plugin & Theme Ecosystem</li><li>AI Integration (OpenRouter)</li><li>Multilingual Support</li><li>Advanced Role & Permission System</li></ul>',
            ],
            [
                'slug' => 'api',
                'title' => 'API Documentation',
                'category' => 'api',
                'content' => '<p>RJ CMS provides a RESTful API to manage your content externally.</p><h3>Authentication</h3><p>To access the API, you need to generate API keys from <strong>Settings > API & Integrations</strong>. You must pass the keys in the headers:</p><pre>X-Public-Key: YOUR_PUBLIC_KEY\nX-Private-Key: YOUR_PRIVATE_KEY</pre><h3>Endpoints</h3><ul><li><code>GET /api/v1/posts</code> - List all posts</li><li><code>GET /api/v1/posts/{id}</code> - Get a single post</li><li><code>GET /api/v1/pages</code> - List all pages</li><li><code>GET /api/v1/media</code> - List all media</li></ul>',
            ],
            [
                'slug' => 'theme',
                'title' => 'Theme Development',
                'category' => 'theme',
                'content' => '<p>Themes in RJ CMS are located in the <code>rj-content/themes</code> directory. Each theme requires a <code>theme.json</code> manifest file.</p><h3>theme.json Structure</h3><p>The manifest defines the theme name, version, author, supports, and customizer settings.</p><h3>Customizer</h3><p>Themes can define a <code>customizer</code> block in <code>theme.json</code> to allow users to change colors, fonts, and layouts from the admin panel.</p>',
            ],
            [
                'slug' => 'admin',
                'title' => 'Admin Panel Guide',
                'category' => 'admin',
                'content' => '<p>The RJ CMS admin panel is where you manage all your site content.</p><h3>Posts & Pages</h3><p>You can create, edit, and delete posts and pages. You can also toggle between the Classic Editor and the RJBuilder visual editor.</p><h3>Media</h3><p>Upload images, videos, and documents. You can manage them in the Media Library.</p><h3>Appearance</h3><p>Manage themes, menus, and widgets. The Customizer allows you to tweak your theme settings.</p>',
            ],
            [
                'slug' => 'plugin',
                'title' => 'Plugin Development',
                'category' => 'plugin',
                'content' => '<p>Plugins extend the functionality of RJ CMS. They are located in <code>rj-content/plugins</code>.</p><h3>plugin.json</h3><p>Each plugin requires a <code>plugin.json</code> manifest file defining its name, version, namespace, and service provider.</p><h3>Hooks</h3><p>Plugins can hook into the core functionality using Actions and Filters.</p>',
            ],
            [
                'slug' => 'developer',
                'title' => 'Developer Guide',
                'category' => 'developer',
                'content' => '<p>RJ CMS is built with Laravel 12, Blade, Tailwind CSS, and Alpine.js.</p><h3>Directory Structure</h3><ul><li><code>app/</code> - Core PHP classes</li><li><code>resources/views/</code> - Blade templates</li><li><code>rj-content/</code> - Themes and Plugins</li></ul><h3>Helper Functions</h3><p>RJ CMS provides helper functions like <code>rj_get_option()</code>, <code>rj_set_option()</code>, and <code>rj_render_blocks()</code>.</p>',
            ],
            [
                'slug' => 'hooks',
                'title' => 'Hooks Reference',
                'category' => 'hooks',
                'content' => '<p>RJ CMS uses an event-driven architecture inspired by WordPress.</p><h3>Actions</h3><p>Actions allow you to execute code at specific points:</p><pre>add_action("admin.menu", function() { ... });\ndo_action("admin.menu");</pre><h3>Filters</h3><p>Filters allow you to modify data before it is used:</p><pre>add_filter("builder.widgets", function($widgets) { return $widgets; });\napply_filters("builder.widgets", $widgets);</pre>',
            ],
            [
                'slug' => 'other',
                'title' => 'Updates & Maintenance',
                'category' => 'other',
                'content' => '<p>Keep your RJ CMS installation up to date to ensure security and stability.</p><p>Go to <strong>Updates</strong> in the admin panel to check for core, theme, and plugin updates.</p><p>Always backup your database and files before running updates using the <strong>Tools > Backup</strong> feature.</p>',
            ],
            [
                'slug' => 'credits',
                'title' => 'Credits',
                'category' => 'credits',
                'content' => '<p><strong>RJ CMS</strong> is developed and maintained by Ruhid Javadov.</p><p>Special thanks to the Laravel, Tailwind CSS, and Alpine.js communities for their incredible open-source tools.</p>',
            ],
            [
                'slug' => 'license',
                'title' => 'License',
                'category' => 'license',
                'content' => '<p>RJ CMS is open-source software licensed under the <strong>MIT license</strong>.</p><p>You are free to use, modify, and distribute this software for personal and commercial projects.</p>',
            ],
        ];

        foreach ($items as $item) {
            Knowledge::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
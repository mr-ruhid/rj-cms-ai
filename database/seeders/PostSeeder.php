<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::query()->value('id') ?? 1;
        $baseLocale = config('app.locale', 'en');

        $posts = [
            [
                'title' => 'Hello World',
                'slug' => 'hello-world',
                'excerpt' => 'Welcome to RJ CMS. This is your first blog post.',
                'content' => '<p>Welcome to RJ CMS! This is your first blog post. Edit or delete it, then start writing.</p><h2>What is RJ CMS?</h2><p>RJ CMS is a modern self-hosted CMS built on Laravel 12, featuring RJBuilder — a drag-and-drop visual page builder.</p><h2>Getting Started</h2><p>Visit the admin panel to create new posts, pages, menus, and more. You can switch any post or page to RJBuilder mode to design it visually.</p><p>Happy building!</p>',
                'edit_mode' => 'classic',
                'is_sticky' => true,
            ],
            [
                'title' => 'Getting Started with RJBuilder',
                'slug' => 'getting-started-rjbuilder',
                'excerpt' => 'Learn how to design beautiful pages with RJBuilder.',
                'content' => '<p>RJBuilder is the visual page builder included with RJ CMS. It provides a modern drag-and-drop interface for designing pages, headers, footers, and popups.</p><h2>Key Features</h2><ul><li>Drag-and-drop blocks</li><li>Responsive breakpoints (Desktop / Tablet / Mobile)</li><li>Custom CSS and animations</li><li>Dynamic tags for content</li><li>AI assistant integration</li></ul><h2>How to Use</h2><p>Open any post or page in edit mode, select "RJBuilder" as the edit mode, and click "Open Builder". You can then start adding blocks and customizing styles.</p>',
                'edit_mode' => 'classic',
                'is_sticky' => false,
            ],
        ];

        foreach ($posts as $index => $data) {
            $existing = Post::where('slug', $data['slug'])
                ->where('type', 'post')
                ->first();

            if ($existing) {
                continue;
            }

            $post = Post::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'content' => $data['content'] ?? '',
                'edit_mode' => $data['edit_mode'] ?? 'classic',
                'excerpt' => $data['excerpt'] ?? null,
                'type' => 'post',
                'status' => 'published',
                'is_sticky' => $data['is_sticky'] ?? false,
                'allow_comments' => true,
                'author_id' => $authorId,
                'settings' => [],
                'meta' => [],
                'published_at' => now()->subDays(count($posts) - $index),
            ]);

            PostTranslation::create([
                'post_id' => $post->id,
                'locale' => $baseLocale,
                'title' => $data['title'],
                'content' => $data['content'] ?? '',
                'excerpt' => $data['excerpt'] ?? null,
            ]);
        }
    }
}
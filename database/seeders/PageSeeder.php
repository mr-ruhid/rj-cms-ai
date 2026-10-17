<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\User;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::query()->value('id') ?? 1;
        $baseLocale = config('app.locale', 'en');

        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'excerpt' => 'Welcome to our website.',
                'content' => null,
                'edit_mode' => 'builder',
                'blocks' => ['blocks' => []],
                'status' => 'published',
                'is_front' => true,
            ],
            [
                'title' => 'About',
                'slug' => 'about',
                'excerpt' => 'Learn more about us.',
                'content' => '<h2>About Us</h2><p>This is the About page. Edit this content from the admin panel.</p><p>You can also switch to RJBuilder mode to design this page visually.</p>',
                'edit_mode' => 'classic',
                'status' => 'published',
            ],
            [
                'title' => 'Contact',
                'slug' => 'contact',
                'excerpt' => 'Get in touch with us.',
                'content' => null,
                'edit_mode' => 'theme',
                'theme_template' => 'contact',
                'status' => 'published',
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'excerpt' => 'Your privacy matters to us.',
                'content' => '<h2>Privacy Policy</h2><p>This is a placeholder for your privacy policy. Replace it with your own content that describes how you collect, use, and protect personal data.</p><h3>Data Collection</h3><p>Explain what data you collect from visitors.</p><h3>Cookies</h3><p>Explain your cookie usage.</p><h3>Third Parties</h3><p>Explain if you share data with third parties.</p>',
                'edit_mode' => 'classic',
                'status' => 'draft',
            ],
            [
                'title' => 'Sample Page',
                'slug' => 'sample-page',
                'excerpt' => 'A sample page to get started.',
                'content' => '<h2>Sample Page</h2><p>This is an example page. It\'s different from a blog post because it will stay in one place and will show up in your site navigation (in most themes).</p><p>Most people start with an About page that introduces them to potential site visitors. It might say something like this:</p><blockquote>Hi there! I\'m a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like piña coladas.</blockquote>',
                'edit_mode' => 'classic',
                'status' => 'published',
            ],
        ];

        foreach ($pages as $index => $data) {
            $existing = Post::where('slug', $data['slug'])
                ->where('type', 'page')
                ->first();

            if ($existing) {
                continue;
            }

            $post = Post::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'content' => $data['content'] ?? '',
                'blocks' => $data['blocks'] ?? null,
                'edit_mode' => $data['edit_mode'] ?? 'classic',
                'theme_template' => $data['theme_template'] ?? null,
                'excerpt' => $data['excerpt'] ?? null,
                'type' => 'page',
                'status' => $data['status'],
                'is_sticky' => false,
                'allow_comments' => false,
                'author_id' => $authorId,
                'settings' => [],
                'meta' => !empty($data['is_front']) ? ['is_front' => true] : [],
                'published_at' => $data['status'] === 'published' ? now()->subDays(count($pages) - $index) : null,
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
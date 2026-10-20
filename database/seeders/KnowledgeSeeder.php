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
                'category' => 'info',
                'translations' => [
                    'en' => [
                        'title' => 'About RJ CMS',
                        'content' => '<p>RJ CMS is a modern, self-hosted Content Management System built on Laravel 12. It offers a WordPress-like experience but with a cleaner, faster, and more modern architecture.</p><p>Key features include:</p><ul><li>Visual Page Builder (RJBuilder)</li><li>Plugin & Theme Ecosystem</li><li>AI Integration (OpenRouter)</li><li>Multilingual Support</li><li>Advanced Role & Permission System</li></ul>',
                    ],
                    'az' => [
                        'title' => 'RJ CMS Haqqında',
                        'content' => '<p>RJ CMS Laravel 12 üzərində qurulmuş müasir, self-hosted Kontent İdarəetmə Sistemidir. WordPress bənzəri təcrübə təqdim edir, lakin daha təmiz, sürətli və müasir arxitekturaya malikdir.</p><p>Əsas xüsusiyyətləri:</p><ul><li>Vizual Səhifə Qurucusu (RJBuilder)</li><li>Plugin və Tema Ekosistemi</li><li>AI İnteqrasiyası (OpenRouter)</li><li>Çoxdilli Dəstək</li><li>İnkişaf etmiş Rol və İcazə Sistemi</li></ul>',
                    ],
                ],
            ],
            [
                'slug' => 'api',
                'category' => 'api',
                'translations' => [
                    'en' => [
                        'title' => 'API Documentation',
                        'content' => '<p>RJ CMS provides a RESTful API to manage your content externally.</p><h3>Authentication</h3><p>To access the API, you need to generate API keys from <strong>Settings > API & Integrations</strong>. You must pass the keys in the headers:</p><pre>X-Public-Key: YOUR_PUBLIC_KEY\nX-Private-Key: YOUR_PRIVATE_KEY</pre><h3>Endpoints</h3><ul><li><code>GET /api/v1/posts</code> - List all posts</li><li><code>GET /api/v1/posts/{id}</code> - Get a single post</li><li><code>GET /api/v1/pages</code> - List all pages</li><li><code>GET /api/v1/media</code> - List all media</li></ul>',
                    ],
                    'az' => [
                        'title' => 'API Sənədləşməsi',
                        'content' => '<p>RJ CMS məzmununuzu xarici olaraq idarə etmək üçün RESTful API təqdim edir.</p><h3>Autentifikasiya</h3><p>API-yə giriş üçün <strong>Ayarlar > API və İnteqrasiyalar</strong> bölməsindən API açarları yaratmalısınız. Açarları başlıqlarda (headers) göndərməlisiniz:</p><pre>X-Public-Key: SİZİN_PUBLIC_AÇARINIZ\nX-Private-Key: SİZİN_PRIVATE_AÇARINIZ</pre><h3>Endpoint-lər</h3><ul><li><code>GET /api/v1/posts</code> - Bütün yazıları göstər</li><li><code>GET /api/v1/posts/{id}</code> - Tək yazını göstər</li><li><code>GET /api/v1/pages</code> - Bütün səhifələri göstər</li><li><code>GET /api/v1/media</code> - Bütün medianı göstər</li></ul>',
                    ],
                ],
            ],
            [
                'slug' => 'theme',
                'category' => 'theme',
                'translations' => [
                    'en' => [
                        'title' => 'Theme Development',
                        'content' => '<p>Themes in RJ CMS are located in the <code>rj-content/themes</code> directory. Each theme requires a <code>theme.json</code> manifest file.</p><h3>theme.json Structure</h3><p>The manifest defines the theme name, version, author, supports, and customizer settings.</p><h3>Customizer</h3><p>Themes can define a <code>customizer</code> block in <code>theme.json</code> to allow users to change colors, fonts, and layouts from the admin panel.</p>',
                    ],
                    'az' => [
                        'title' => 'Tema İnkişafı',
                        'content' => '<p>RJ CMS-də temalar <code>rj-content/themes</code> qovluğunda yerləşir. Hər tema <code>theme.json</code> manifest faylı tələb edir.</p><h3>theme.json Strukturu</h3><p>Manifest tema adını, versiyasını, müəllifini, dəstəklədiyi funksiyaları və customizer ayarlarını təyin edir.</p><h3>Customizer</h3><p>Temalar <code>theme.json</code> daxilində <code>customizer</code> bloku təyin edərək istifadəçilərə admin paneldən rəngləri, şriftləri və tərtibatı dəyişməyə imkan verə bilər.</p>',
                    ],
                ],
            ],
            [
                'slug' => 'admin',
                'category' => 'admin',
                'translations' => [
                    'en' => [
                        'title' => 'Admin Panel Guide',
                        'content' => '<p>The RJ CMS admin panel is where you manage all your site content.</p><h3>Posts & Pages</h3><p>You can create, edit, and delete posts and pages. You can also toggle between the Classic Editor and the RJBuilder visual editor.</p><h3>Media</h3><p>Upload images, videos, and documents. You can manage them in the Media Library.</p><h3>Appearance</h3><p>Manage themes, menus, and widgets. The Customizer allows you to tweak your theme settings.</p>',
                    ],
                    'az' => [
                        'title' => 'Admin Panel Bələdçisi',
                        'content' => '<p>RJ CMS admin paneli bütün sayt məzmununuzu idarə etdiyiniz yerdir.</p><h3>Yazılar və Səhifələr</h3><p>Yazılar və səhifələr yarada, redaktə edə və silə bilərsiniz. Klassik Redaktor və RJBuilder vizual redaktoru arasında keçid edə bilərsiniz.</p><h3>Media</h3><p>Şəkillər, videolar və sənədlər yükləyin. Onları Media Kitabxanasında idarə edə bilərsiniz.</p><h3>Görünüş</h3><p>Temaları, menyuları və vidcetləri idarə edin. Customizer tema ayarlarınızı dəyişməyə imkan verir.</p>',
                    ],
                ],
            ],
            [
                'slug' => 'plugin',
                'category' => 'plugin',
                'translations' => [
                    'en' => [
                        'title' => 'Plugin Development',
                        'content' => '<p>Plugins extend the functionality of RJ CMS. They are located in <code>rj-content/plugins</code>.</p><h3>plugin.json</h3><p>Each plugin requires a <code>plugin.json</code> manifest file defining its name, version, namespace, and service provider.</p><h3>Hooks</h3><p>Plugins can hook into the core functionality using Actions and Filters.</p>',
                    ],
                    'az' => [
                        'title' => 'Plugin İnkişafı',
                        'content' => '<p>Pluginlər RJ CMS-in funksionallığını genişləndirir. Onlar <code>rj-content/plugins</code> qovluğunda yerləşir.</p><h3>plugin.json</h3><p>Hər plugin adını, versiyasını, namespace-ni və service provider-i təyin edən <code>plugin.json</code> manifest faylı tələb edir.</p><h3>Hooks</h3><p>Pluginlər Actions və Filters istifadə edərək əsas funksionallığa qoşula bilər.</p>',
                    ],
                ],
            ],
            [
                'slug' => 'developer',
                'category' => 'developer',
                'translations' => [
                    'en' => [
                        'title' => 'Developer Guide',
                        'content' => '<p>RJ CMS is built with Laravel 12, Blade, Tailwind CSS, and Alpine.js.</p><h3>Directory Structure</h3><ul><li><code>app/</code> - Core PHP classes</li><li><code>resources/views/</code> - Blade templates</li><li><code>rj-content/</code> - Themes and Plugins</li></ul><h3>Helper Functions</h3><p>RJ CMS provides helper functions like <code>rj_get_option()</code>, <code>rj_set_option()</code>, and <code>rj_render_blocks()</code>.</p>',
                    ],
                    'az' => [
                        'title' => 'Developer Bələdçisi',
                        'content' => '<p>RJ CMS Laravel 12, Blade, Tailwind CSS və Alpine.js ilə qurulub.</p><h3>Qovluq Strukturu</h3><ul><li><code>app/</code> - Əsas PHP sinifləri</li><li><code>resources/views/</code> - Blade şablonları</li><li><code>rj-content/</code> - Temalar və Pluginlər</li></ul><h3>Köməkçi Funksiyalar</h3><p>RJ CMS <code>rj_get_option()</code>, <code>rj_set_option()</code> və <code>rj_render_blocks()</code> kimi köməkçi funksiyalar təqdim edir.</p>',
                    ],
                ],
            ],
            [
                'slug' => 'hooks',
                'category' => 'hooks',
                'translations' => [
                    'en' => [
                        'title' => 'Hooks Reference',
                        'content' => '<p>RJ CMS uses an event-driven architecture inspired by WordPress.</p><h3>Actions</h3><p>Actions allow you to execute code at specific points:</p><pre>add_action("admin.menu", function() { ... });\ndo_action("admin.menu");</pre><h3>Filters</h3><p>Filters allow you to modify data before it is used:</p><pre>add_filter("builder.widgets", function($widgets) { return $widgets; });\napply_filters("builder.widgets", $widgets);</pre>',
                    ],
                    'az' => [
                        'title' => 'Hooks Bələdçisi',
                        'content' => '<p>RJ CMS WordPress-dən ilhamlanan hadisə yönümlü arxitekturadan istifadə edir.</p><h3>Actions</h3><p>Actions müəyyən nöqtələrdə kod icra etməyə imkan verir:</p><pre>add_action("admin.menu", function() { ... });\ndo_action("admin.menu");</pre><h3>Filters</h3><p>Filters məlumat istifadə edilməzdən əvvəl onu dəyişdirməyə imkan verir:</p><pre>add_filter("builder.widgets", function($widgets) { return $widgets; });\napply_filters("builder.widgets", $widgets);</pre>',
                    ],
                ],
            ],
            [
                'slug' => 'other',
                'category' => 'other',
                'translations' => [
                    'en' => [
                        'title' => 'Updates & Maintenance',
                        'content' => '<p>Keep your RJ CMS installation up to date to ensure security and stability.</p><p>Go to <strong>Updates</strong> in the admin panel to check for core, theme, and plugin updates.</p><p>Always backup your database and files before running updates using the <strong>Tools > Backup</strong> feature.</p>',
                    ],
                    'az' => [
                        'title' => 'Yenilənmələr və Texniki Xidmət',
                        'content' => '<p>Təhlükəsizlik və sabitliyi təmin etmək üçün RJ CMS quraşdırmanızı yeniləyin.</p><p>Əsas, tema və plugin yeniləmələrini yoxlamaq üçün admin panelində <strong>Yenilənmələr</strong> bölməsinə keçin.</p><p>Yeniləmələri etməzdən əvvəl həmişə <strong>Alətlər > Backup</strong> funksiyası ilə verilənlər bazanızı və fayllarınızı ehtiyat nüsxəsini çıxarın.</p>',
                    ],
                ],
            ],
            [
                'slug' => 'credits',
                'category' => 'credits',
                'translations' => [
                    'en' => [
                        'title' => 'Credits',
                        'content' => '<p><strong>RJ CMS</strong> is developed and maintained by Ruhid Javadov.</p><p>Special thanks to the Laravel, Tailwind CSS, and Alpine.js communities for their incredible open-source tools.</p>',
                    ],
                    'az' => [
                        'title' => 'Təşəkkürlər',
                        'content' => '<p><strong>RJ CMS</strong> Ruhid Cəfərov tərəfindən hazırlanmış və idarə olunur.</p><p>Laravel, Tailwind CSS və Alpine.js icmalarına əvəzsiz açıq mənbə alətləri üçün xüsusi təşəkkürlər.</p>',
                    ],
                ],
            ],
            [
                'slug' => 'license',
                'category' => 'license',
                'translations' => [
                    'en' => [
                        'title' => 'License',
                        'content' => '<p>RJ CMS is open-source software licensed under the <strong>MIT license</strong>.</p><p>You are free to use, modify, and distribute this software for personal and commercial projects.</p>',
                    ],
                    'az' => [
                        'title' => 'Lisenziya',
                        'content' => '<p>RJ CMS <strong>MIT lisenziyası</strong> altında lisenziyalaşdırılmış açıq mənbə proqram təminatıdır.</p><p>Bu proqram təminatını şəxsi və kommersiya layihələri üçün istifadə etməkdə, dəyişdirməkdə və yaymaqda sərbəstsiniz.</p>',
                    ],
                ],
            ],
            [
                'slug' => 'user-guide',
                'category' => 'other',
                'translations' => [
                    'en' => [
                        'title' => 'User Guide',
                        'content' => '<h3>How to create a post?</h3><p>Go to <strong>Posts > Add New</strong>. Enter your title, content, and click Publish.</p><h3>How to change the theme?</h3><p>Go to <strong>Appearance > Themes</strong>, browse the available themes, and click Activate.</p><h3>How to add a menu?</h3><p>Go to <strong>Appearance > Menus</strong>, create a new menu, and drag items into it.</p>',
                    ],
                    'az' => [
                        'title' => 'İstifadəçi Bələdçisi',
                        'content' => '<h3>Yazı necə yaradılır?</h3><p><strong>Yazılar > Yeni Əlavə Et</strong> bölməsinə keçin. Başlığı, məzmunu daxil edin və Dərc et düyməsinə basın.</p><h3>Temanı necə dəyişmək olar?</h3><p><strong>Görünüş > Temalar</strong> bölməsinə keçin, mövcud temalara baxın və Aktiv et düyməsinə basın.</p><h3>Menyu necə əlavə edilir?</h3><p><strong>Görünüş > Menyular</strong> bölməsinə keçin, yeni menyu yaradın və elementləri ora dartıb əlavə edin.</p>',
                    ],
                ],
            ],
            [
                'slug' => 'faq',
                'category' => 'other',
                'translations' => [
                    'en' => [
                        'title' => 'FAQ',
                        'content' => '<h3>Can I use RJ CMS for commercial projects?</h3><p>Yes, RJ CMS is MIT licensed.</p><h3>Does it support multiple languages?</h3><p>Yes, the core supports AZ, EN, RU, TR, and dynamic languages can be added.</p><h3>How do I update the system?</h3><p>Use the <strong>Updates</strong> section in the admin panel.</p>',
                    ],
                    'az' => [
                        'title' => 'Tez-tez Verilən Suallar (FAQ)',
                        'content' => '<h3>RJ CMS-i kommersiya layihələrində istifadə edə bilərəm?</h3><p>Bəli, RJ CMS MIT lisenziyasına malikdir.</p><h3>Çoxdilli dəstəyi varmı?</h3><p>Bəli, nüvə AZ, EN, RU, TR dillərini dəstəkləyir və dinamik dillər əlavə edilə bilər.</p><h3>Sistemi necə yeniləyə bilərəm?</h3><p>Admin panelindəki <strong>Yenilənmələr</strong> bölməsindən istifadə edin.</p>',
                    ],
                ],
            ],
            [
                'slug' => 'troubleshooting',
                'category' => 'other',
                'translations' => [
                    'en' => [
                        'title' => 'Troubleshooting',
                        'content' => '<h3>500 Internal Server Error</h3><p>Check your <code>storage/logs/laravel.log</code> file for the exact error. It might be a missing PHP extension or permission issue.</p><h3>Changes not showing?</h3><p>Clear the cache from <strong>Settings > Cache</strong> or run <code>php artisan optimize:clear</code>.</p>',
                    ],
                    'az' => [
                        'title' => 'Problemlərin Həlli',
                        'content' => '<h3>500 Daxili Server Xətası</h3><p>Dəqiq xətanı görmək üçün <code>storage/logs/laravel.log</code> faylını yoxlayın. Bu, çatışmayan PHP genişləndirməsi və ya icazə problemi ola bilər.</p><h3>Dəyişikliklər görünmür?</h3><p><strong>Ayarlar > Keş</strong> bölməsindən keşi təmizləyin və ya <code>php artisan optimize:clear</code> əmrini icra edin.</p>',
                    ],
                ],
            ],
        ];

        foreach ($items as $item) {
            $knowledge = Knowledge::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category' => $item['category'],
                    'is_active' => true,
                ]
            );

            foreach ($item['translations'] as $locale => $translation) {
                $knowledge->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $translation['title'],
                        'content' => $translation['content'],
                    ]
                );
            }
        }
    }
}
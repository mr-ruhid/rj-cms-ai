<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\ThemeManager;
use App\Services\ThemeResolver;

class PageController extends Controller
{
    protected ThemeResolver $resolver;
    protected ThemeManager $themes;

    public function __construct(ThemeResolver $resolver, ThemeManager $themes)
    {
        $this->resolver = $resolver;
        $this->themes = $themes;
    }

    public function show(string $slug)
    {
        $page = Post::with(['author', 'translations', 'template'])
            ->where('type', 'page')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $page->incrementViews();

        $header = $this->resolver->header();
        $footer = $this->resolver->footer();

        view()->share('post', $page);

        if ($page->isThemeMode() && $page->theme_template) {
            $themeView = 'theme::templates.' . $page->theme_template;

            try {
                if (view()->exists($themeView)) {
                    return view($themeView, [
                        'page' => $page,
                        'header' => $header,
                        'footer' => $footer,
                        'title' => $page->title,
                    ]);
                }
            } catch (\Throwable $e) {
            }
        }

        return $this->themes->renderView('single-page', [
            'page' => $page,
            'header' => $header,
            'footer' => $footer,
            'title' => $page->title,
        ]);
    }
}
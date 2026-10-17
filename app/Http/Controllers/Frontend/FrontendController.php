<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Template;
use App\Services\ThemeManager;
use App\Services\ThemeResolver;

class FrontendController extends Controller
{
    protected ThemeResolver $resolver;
    protected ThemeManager $themes;

    public function __construct(ThemeResolver $resolver, ThemeManager $themes)
    {
        $this->resolver = $resolver;
        $this->themes = $themes;
    }

    public function home()
    {
        $header = $this->resolver->header();
        $footer = $this->resolver->footer();

        $page = $this->findFrontPage();

        if (!$page) {
            $page = $this->resolver->page();
        }

        $latestPosts = collect();

        if (!$page) {
            $latestPosts = Post::with(['author', 'categories', 'tags'])
                ->where('type', 'post')
                ->published()
                ->orderByDesc('published_at')
                ->limit(6)
                ->get();
        }

        view()->share('post', $page);

        return $this->themes->renderView('home', [
            'header' => $header,
            'footer' => $footer,
            'page' => $page,
            'latestPosts' => $latestPosts,
            'title' => $page?->title ?? rj_get_option('site_name', config('app.name')),
        ]);
    }

    protected function findFrontPage(): ?Post
    {
        return Post::with(['author', 'translations', 'template'])
            ->where('type', 'page')
            ->published()
            ->get()
            ->first(fn($p) => $p->isFrontPage());
    }

    public function preview(Template $template)
    {
        return view('frontend.preview', [
            'template' => $template,
            'title' => $template->name,
        ]);
    }
}
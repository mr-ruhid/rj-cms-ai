<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\ThemeResolver;

class PageController extends Controller
{
    protected ThemeResolver $resolver;

    public function __construct(ThemeResolver $resolver)
    {
        $this->resolver = $resolver;
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

        return view('frontend.single-page', [
            'page' => $page,
            'header' => $header,
            'footer' => $footer,
            'title' => $page->title,
        ]);
    }
}
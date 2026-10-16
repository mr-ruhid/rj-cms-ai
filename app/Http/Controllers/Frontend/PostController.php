<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Term;
use App\Services\ThemeManager;
use App\Services\ThemeResolver;
use Illuminate\Http\Request;

class PostController extends Controller
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
        $post = Post::with(['author', 'categories', 'tags', 'translations', 'template'])
            ->where('type', 'post')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $post->incrementViews();

        $related = Post::with('featured_image')
            ->where('type', 'post')
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->categories->isNotEmpty(), function ($q) use ($post) {
                $q->whereHas('categories', function ($inner) use ($post) {
                    $inner->whereIn('rj_categories.id', $post->categories->pluck('id'));
                });
            })
            ->latest('published_at')
            ->limit(4)
            ->get();

        $header = $this->resolver->header();
        $footer = $this->resolver->footer();

        view()->share('post', $post);

        return $this->themes->renderView('single-post', [
            'post' => $post,
            'related' => $related,
            'header' => $header,
            'footer' => $footer,
            'title' => $post->title,
        ]);
    }

    public function archive(Request $request)
    {
        $query = Post::with(['author', 'categories', 'tags'])
            ->where('type', 'post')
            ->published()
            ->orderByDesc('published_at');

        $archiveTitle = __('Blog');
        $archiveDescription = '';

        if ($term = $request->input('term')) {
            $taxonomy = $request->input('taxonomy', 'category');

            $termModel = Term::where('taxonomy', $taxonomy)
                ->where('slug', $term)
                ->first();

            if (!$termModel) {
                abort(404);
            }

            if ($taxonomy === 'category') {
                $query->whereHas('categories', fn($q) => $q->where('rj_categories.id', $termModel->id));
            } else {
                $query->whereHas('tags', fn($q) => $q->where('rj_tags.id', $termModel->id));
            }

            $archiveTitle = $termModel->name;
            $archiveDescription = (string) $termModel->description;
        }

        if ($search = $request->input('s')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });

            $archiveTitle = __('Search results for') . ': ' . $search;
        }

        $posts = $query->paginate(12)->withQueryString();

        $header = $this->resolver->header();
        $footer = $this->resolver->footer();

        view()->share('archive_title', $archiveTitle);
        view()->share('archive_description', $archiveDescription);

        return $this->themes->renderView('archive', [
            'posts' => $posts,
            'header' => $header,
            'footer' => $footer,
            'title' => $archiveTitle,
            'archiveTitle' => $archiveTitle,
            'archiveDescription' => $archiveDescription,
        ]);
    }
}
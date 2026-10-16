<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostType;
use App\Models\Tag;
use App\Models\Template;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected PostService $service;

    public function __construct(PostService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $type = $request->input('type', 'post');
        $query = Post::with(['author', 'categories', 'tags'])
            ->where('type', $type)
            ->orderByDesc('created_at');

        $status = $request->input('status', 'all');

        if ($status !== 'all') {
            $query->where('status', $status);
        } else {
            $query->where('status', '!=', 'trash');
        }

        if ($categoryId = $request->input('category')) {
            $query->whereHas('categories', fn($q) => $q->where('rj_categories.id', (int) $categoryId));
        }

        if ($tagId = $request->input('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('rj_tags.id', (int) $tagId));
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $posts = $query->paginate(20)->withQueryString();

        $counts = [
            'all' => Post::where('type', $type)->count(),
            'published' => Post::where('type', $type)->where('status', 'published')->count(),
            'draft' => Post::where('type', $type)->where('status', 'draft')->count(),
            'scheduled' => Post::where('type', $type)->where('status', 'scheduled')->count(),
            'trash' => Post::where('type', $type)->where('status', 'trash')->count(),
        ];

        $categories = Category::with('translations')->ordered()->get()->sortBy('name')->values();
        $tags = Tag::with('translations')->ordered()->get()->sortBy('name')->values();
        $postTypes = PostType::active()->ordered()->get();

        return view('admin.posts.index', compact('posts', 'counts', 'categories', 'tags', 'type', 'status', 'postTypes'));
    }

    public function create(Request $request)
    {
        $type = $request->input('type', 'post');

        $post = new Post([
            'type' => $type,
            'status' => 'draft',
            'allow_comments' => true,
            'edit_mode' => 'classic',
        ]);

        $categories = Category::with('translations')->ordered()->get()->sortBy('name')->values();
        $tags = Tag::with('translations')->ordered()->get()->sortBy('name')->values();
        $templates = Template::where('type', 'page')->published()->orderBy('name')->get();
        $locales = rj_available_locales();
        $baseLocale = config('app.locale');
        $themeTemplates = $this->resolveThemeTemplates();

        return view('admin.posts.create', compact('post', 'type', 'categories', 'tags', 'templates', 'locales', 'baseLocale', 'themeTemplates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $post = $this->service->create($validated, $request);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'post' => $post]);
        }

        if ($request->input('open_builder') === '1') {
            return redirect()
                ->route('admin.posts.build', $post)
                ->with('success', __('Post created. Start building!'));
        }

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', __('Post created.'));
    }

    public function show(Post $post)
    {
        $post->load(['author', 'categories', 'tags', 'translations', 'template']);

        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $post->load(['categories', 'tags', 'translations']);

        $categories = Category::with('translations')->ordered()->get()->sortBy('name')->values();
        $tags = Tag::with('translations')->ordered()->get()->sortBy('name')->values();
        $templates = Template::where('type', 'page')->published()->orderBy('name')->get();
        $locales = rj_available_locales();
        $baseLocale = config('app.locale');
        $themeTemplates = $this->resolveThemeTemplates();

        return view('admin.posts.edit', compact('post', 'categories', 'tags', 'templates', 'locales', 'baseLocale', 'themeTemplates'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate($this->rules($post));

        $post = $this->service->update($post, $validated, $request);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'post' => $post]);
        }

        return back()->with('success', __('Post saved.'));
    }

    public function destroy(Post $post)
    {
        $this->service->delete($post);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.posts.index', ['type' => $post->type])
            ->with('success', __('Post deleted.'));
    }

    public function duplicate(Post $post)
    {
        $copy = $this->service->duplicate($post);

        return redirect()
            ->route('admin.posts.edit', $copy)
            ->with('success', __('Post duplicated.'));
    }

    public function publish(Post $post)
    {
        $this->service->publish($post);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'status' => 'published']);
        }

        return back()->with('success', __('Post published.'));
    }

    public function unpublish(Post $post)
    {
        $this->service->unpublish($post);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'status' => 'draft']);
        }

        return back()->with('success', __('Post unpublished.'));
    }

    public function trash(Post $post)
    {
        $this->service->trash($post);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'status' => 'trash']);
        }

        return back()->with('success', __('Post moved to trash.'));
    }

    public function restore(Post $post)
    {
        $this->service->restore($post);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'status' => 'draft']);
        }

        return back()->with('success', __('Post restored.'));
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:rj_posts,id',
            'action' => 'required|in:publish,draft,trash,delete,sticky,unsticky',
        ]);

        $count = $this->service->bulkAction($validated['ids'], $validated['action']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => __(':n item(s) updated.', ['n' => $count]),
            ]);
        }

        return back()->with('success', __(':n item(s) updated.', ['n' => $count]));
    }

    public function build(Post $post)
    {
        $viewUrl = null;

        if ($post->status === 'published' && $post->slug) {
            $viewUrl = $post->type === 'page'
                ? url('/' . $post->slug)
                : url('/blog/' . $post->slug);
        }

        $builder = [
            'entityType' => 'post',
            'entityId' => $post->id,
            'name' => $post->title ?: __('Untitled'),
            'subtitle' => $post->typeLabel(),
            'templateType' => 'page',
            'blocks' => $post->blocks['blocks'] ?? [],
            'updateUrl' => route('admin.posts.blocks', $post),
            'backUrl' => route('admin.posts.edit', $post),
            'viewUrl' => $viewUrl,
            'status' => $post->status,
            'locales' => rj_available_locales(),
            'baseLocale' => config('app.locale'),
        ];

        return view('rjbuilder.edit', compact('builder'));
    }

    public function updateBlocks(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|array',
            'content.blocks' => 'present|array',
        ]);

        $post->update([
            'blocks' => $validated['content'],
            'edit_mode' => 'builder',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Post blocks saved.'),
                'blocks_count' => count($validated['content']['blocks'] ?? []),
            ]);
        }

        return back()->with('success', __('Post blocks saved.'));
    }

    public function analyzeSeo(Request $request, Post $post)
    {
        $locale = $request->input('locale', app()->getLocale());

        $translation = $post->translations->firstWhere('locale', $locale);

        $title = $locale === config('app.locale')
            ? $post->getRawOriginal('title')
            : ($translation?->title ?? '');

        $content = $locale === config('app.locale')
            ? $post->getRawOriginal('content')
            : ($translation?->content ?? '');

        $seoTitle = $translation?->seo_title ?? ($locale === config('app.locale') ? $post->getRawOriginal('title') : '');
        $seoDescription = $translation?->seo_description ?? ($locale === config('app.locale') ? $post->getRawOriginal('excerpt') : '');
        $focusKeyword = $translation?->focus_keyword;

        $score = 0;
        $issues = [];

        if (empty($seoTitle)) {
            $issues[] = __('SEO title is missing.');
        } else {
            $len = mb_strlen($seoTitle);
            if ($len >= 40 && $len <= 65) {
                $score += 20;
            } elseif ($len < 40) {
                $score += 10;
                $issues[] = __('SEO title is too short (:n chars, aim for 50-60).', ['n' => $len]);
            } else {
                $score += 10;
                $issues[] = __('SEO title is too long (:n chars, aim for 50-60).', ['n' => $len]);
            }
        }

        if (empty($seoDescription)) {
            $issues[] = __('SEO description is missing.');
        } else {
            $len = mb_strlen($seoDescription);
            if ($len >= 140 && $len <= 165) {
                $score += 20;
            } elseif ($len < 140) {
                $score += 10;
                $issues[] = __('SEO description is too short (:n chars, aim for 150-160).', ['n' => $len]);
            } else {
                $score += 10;
                $issues[] = __('SEO description is too long (:n chars, aim for 150-160).', ['n' => $len]);
            }
        }

        if (empty($focusKeyword)) {
            $issues[] = __('Focus keyword is not set.');
        } else {
            $score += 15;

            $inTitle = !empty($seoTitle) && stripos($seoTitle, $focusKeyword) !== false;
            $inContent = !empty($content) && stripos($content, $focusKeyword) !== false;

            if ($inTitle) {
                $score += 10;
            } else {
                $issues[] = __('Focus keyword is not in the SEO title.');
            }

            if ($inContent) {
                $score += 15;
            } else {
                $issues[] = __('Focus keyword is not in the content.');
            }
        }

        if (!empty($content)) {
            $wordCount = str_word_count(strip_tags($content));

            if ($wordCount >= 300) {
                $score += 10;
            } elseif ($wordCount >= 150) {
                $score += 5;
                $issues[] = __('Content is short (:n words, aim for 300+).', ['n' => $wordCount]);
            } else {
                $issues[] = __('Content is very short (:n words, aim for 300+).', ['n' => $wordCount]);
            }

            if (preg_match('/<h[1-3]/i', $content)) {
                $score += 10;
            } else {
                $issues[] = __('No subheadings (H2/H3) found in content.');
            }
        }

        $score = min(100, $score);

        $level = 'bad';
        if ($score >= 80) {
            $level = 'good';
        } elseif ($score >= 50) {
            $level = 'warn';
        }

        if (empty($issues)) {
            $issues[] = __('Great job! No issues found.');
        }

        return response()->json([
            'success' => true,
            'score' => $score,
            'level' => $level,
            'issues' => $issues,
        ]);
    }

    protected function rules(?Post $post = null): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'featured_image' => 'nullable|string|max:500',
            'type' => 'nullable|string|max:50',
            'status' => 'nullable|in:draft,published,scheduled,trash',
            'is_sticky' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
            'template_id' => 'nullable|integer|exists:rj_templates,id',
            'categories' => 'nullable|array',
            'tags' => 'nullable|array',
            'translations' => 'nullable|array',
            'published_at' => 'nullable|date',
            'edit_mode' => 'nullable|in:builder,classic,theme',
            'theme_template' => 'nullable|string|max:100',
            'settings' => 'nullable|array',
        ];

        $locales = rj_available_locales();
        $baseLocale = config('app.locale');

        foreach ($locales as $locale) {
            $prefix = $locale === $baseLocale ? '' : "translations.{$locale}.";

            $rules["{$prefix}seo_title"] = 'nullable|string|max:255';
            $rules["{$prefix}seo_description"] = 'nullable|string|max:500';
            $rules["{$prefix}seo_image"] = 'nullable|string|max:500';
            $rules["{$prefix}focus_keyword"] = 'nullable|string|max:100';
            $rules["{$prefix}canonical_url"] = 'nullable|url|max:500';
            $rules["{$prefix}og_title"] = 'nullable|string|max:255';
            $rules["{$prefix}og_description"] = 'nullable|string|max:500';
            $rules["{$prefix}og_image"] = 'nullable|string|max:500';
            $rules["{$prefix}twitter_card"] = 'nullable|in:summary,summary_large_image,app,player';
            $rules["{$prefix}twitter_title"] = 'nullable|string|max:255';
            $rules["{$prefix}twitter_description"] = 'nullable|string|max:500';
            $rules["{$prefix}twitter_image"] = 'nullable|string|max:500';
            $rules["{$prefix}robots"] = 'nullable|string|max:50';
            $rules["{$prefix}schema_type"] = 'nullable|string|max:50';
        }

        return $rules;
    }

    protected function resolveThemeTemplates(): array
    {
        $theme = \App\Models\Theme::active();

        if (!$theme) {
            return [];
        }

        $metadata = $theme->metadata();

        return $metadata['custom_templates'] ?? [];
    }
}
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
        ]);

        $categories = Category::with('translations')->ordered()->get()->sortBy('name')->values();
        $tags = Tag::with('translations')->ordered()->get()->sortBy('name')->values();
        $templates = Template::where('type', 'page')->published()->orderBy('name')->get();
        $locales = rj_available_locales();
        $baseLocale = config('app.locale');

        return view('admin.posts.create', compact('post', 'type', 'categories', 'tags', 'templates', 'locales', 'baseLocale'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'type' => 'nullable|string|max:50',
            'status' => 'nullable|in:draft,published,scheduled,trash',
            'is_sticky' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
            'template_id' => 'nullable|integer|exists:rj_templates,id',
            'categories' => 'nullable|array',
            'tags' => 'nullable|array',
            'translations' => 'nullable|array',
            'published_at' => 'nullable|date',
        ]);

        $post = $this->service->create($validated, $request);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'post' => $post]);
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

        return view('admin.posts.edit', compact('post', 'categories', 'tags', 'templates', 'locales', 'baseLocale'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'status' => 'nullable|in:draft,published,scheduled,trash',
            'is_sticky' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
            'template_id' => 'nullable|integer|exists:rj_templates,id',
            'categories' => 'nullable|array',
            'tags' => 'nullable|array',
            'translations' => 'nullable|array',
            'published_at' => 'nullable|date',
        ]);

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
        return view('rjbuilder.post-edit', compact('post'));
    }

    public function updateBlocks(Request $request, Post $post)
    {
        $validated = $request->validate([
            'blocks' => 'required|array',
            'blocks.blocks' => 'present|array',
        ]);

        $post->update(['blocks' => $validated['blocks']]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Post blocks saved.'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostType;
use Illuminate\Http\Request;

class PostTypeController extends Controller
{
    public function index()
    {
        $postTypes = PostType::with('author')
            ->orderBy('menu_position')
            ->orderBy('name')
            ->get();

        return view('admin.post-types.index', compact('postTypes'));
    }

    public function create()
    {
        $icons = PostType::availableIcons();

        return view('admin.post-types.create', compact('icons'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $postType = PostType::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? PostType::uniqueSlug($validated['name']),
            'singular_name' => $validated['singular_name'],
            'plural_name' => $validated['plural_name'],
            'icon' => $validated['icon'] ?? 'file',
            'menu_position' => $validated['menu_position'] ?? '25',
            'is_public' => $validated['is_public'] ?? true,
            'has_archive' => $validated['has_archive'] ?? true,
            'is_hierarchical' => $validated['is_hierarchical'] ?? false,
            'supports_comments' => $validated['supports_comments'] ?? true,
            'supports_builder' => $validated['supports_builder'] ?? true,
            'supports_thumbnail' => $validated['supports_thumbnail'] ?? true,
            'supports' => $validated['supports'] ?? [],
            'labels' => $validated['labels'] ?? [],
            'settings' => $validated['settings'] ?? [],
            'status' => $validated['status'] ?? 'active',
            'author_id' => auth()->id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'post_type' => $postType,
            ]);
        }

        return redirect()
            ->route('admin.post-types.index')
            ->with('success', __('Post type created.'));
    }

    public function edit(PostType $postType)
    {
        $icons = PostType::availableIcons();

        return view('admin.post-types.edit', compact('postType', 'icons'));
    }

    public function update(Request $request, PostType $postType)
    {
        $validated = $this->validateData($request, $postType);

        $postType->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $postType->slug,
            'singular_name' => $validated['singular_name'],
            'plural_name' => $validated['plural_name'],
            'icon' => $validated['icon'] ?? $postType->icon,
            'menu_position' => $validated['menu_position'] ?? $postType->menu_position,
            'is_public' => $validated['is_public'] ?? $postType->is_public,
            'has_archive' => $validated['has_archive'] ?? $postType->has_archive,
            'is_hierarchical' => $validated['is_hierarchical'] ?? $postType->is_hierarchical,
            'supports_comments' => $validated['supports_comments'] ?? $postType->supports_comments,
            'supports_builder' => $validated['supports_builder'] ?? $postType->supports_builder,
            'supports_thumbnail' => $validated['supports_thumbnail'] ?? $postType->supports_thumbnail,
            'supports' => $validated['supports'] ?? $postType->supports,
            'labels' => $validated['labels'] ?? $postType->labels,
            'settings' => $validated['settings'] ?? $postType->settings,
            'status' => $validated['status'] ?? $postType->status,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'post_type' => $postType]);
        }

        return back()->with('success', __('Post type saved.'));
    }

    public function destroy(PostType $postType)
    {
        if (in_array($postType->slug, ['post', 'page'], true)) {
            $message = __('Core post types cannot be deleted.');

            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $postType->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.post-types.index')
            ->with('success', __('Post type deleted.'));
    }

    protected function validateData(Request $request, ?PostType $postType = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|alpha_dash|unique:rj_post_types,slug' . ($postType ? ',' . $postType->id : ''),
            'singular_name' => 'required|string|max:100',
            'plural_name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'menu_position' => 'nullable|string|max:10',
            'is_public' => 'nullable|boolean',
            'has_archive' => 'nullable|boolean',
            'is_hierarchical' => 'nullable|boolean',
            'supports_comments' => 'nullable|boolean',
            'supports_builder' => 'nullable|boolean',
            'supports_thumbnail' => 'nullable|boolean',
            'supports' => 'nullable|array',
            'supports.*' => 'string',
            'labels' => 'nullable|array',
            'settings' => 'nullable|array',
            'status' => 'nullable|in:active,inactive',
        ]);
    }
}
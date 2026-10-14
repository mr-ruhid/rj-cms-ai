<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostType;
use App\Models\Taxonomy;
use Illuminate\Http\Request;

class TaxonomyController extends Controller
{
    public function index()
    {
        $taxonomies = Taxonomy::with('author')
            ->orderBy('name')
            ->get();

        return view('admin.taxonomies.index', compact('taxonomies'));
    }

    public function create()
    {
        $postTypes = PostType::active()->ordered()->get();

        return view('admin.taxonomies.create', compact('postTypes'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $taxonomy = Taxonomy::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Taxonomy::uniqueSlug($validated['name']),
            'singular_name' => $validated['singular_name'],
            'plural_name' => $validated['plural_name'],
            'description' => $validated['description'] ?? null,
            'is_hierarchical' => $validated['is_hierarchical'] ?? false,
            'is_public' => $validated['is_public'] ?? true,
            'show_in_menu' => $validated['show_in_menu'] ?? true,
            'post_types' => $validated['post_types'] ?? [],
            'labels' => $validated['labels'] ?? [],
            'settings' => $validated['settings'] ?? [],
            'status' => $validated['status'] ?? 'active',
            'author_id' => auth()->id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'taxonomy' => $taxonomy,
            ]);
        }

        return redirect()
            ->route('admin.taxonomies.index')
            ->with('success', __('Taxonomy created.'));
    }

    public function edit(Taxonomy $taxonomy)
    {
        $postTypes = PostType::active()->ordered()->get();

        return view('admin.taxonomies.edit', compact('taxonomy', 'postTypes'));
    }

    public function update(Request $request, Taxonomy $taxonomy)
    {
        $validated = $this->validateData($request, $taxonomy);

        $taxonomy->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $taxonomy->slug,
            'singular_name' => $validated['singular_name'],
            'plural_name' => $validated['plural_name'],
            'description' => $validated['description'] ?? $taxonomy->description,
            'is_hierarchical' => $validated['is_hierarchical'] ?? $taxonomy->is_hierarchical,
            'is_public' => $validated['is_public'] ?? $taxonomy->is_public,
            'show_in_menu' => $validated['show_in_menu'] ?? $taxonomy->show_in_menu,
            'post_types' => $validated['post_types'] ?? $taxonomy->post_types,
            'labels' => $validated['labels'] ?? $taxonomy->labels,
            'settings' => $validated['settings'] ?? $taxonomy->settings,
            'status' => $validated['status'] ?? $taxonomy->status,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'taxonomy' => $taxonomy]);
        }

        return back()->with('success', __('Taxonomy saved.'));
    }

    public function destroy(Taxonomy $taxonomy)
    {
        if (in_array($taxonomy->slug, ['category', 'tag'], true)) {
            $message = __('Core taxonomies cannot be deleted.');

            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $taxonomy->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.taxonomies.index')
            ->with('success', __('Taxonomy deleted.'));
    }

    public function options()
    {
        $taxonomies = Taxonomy::active()->orderBy('name')->get();

        return response()->json([
            'taxonomies' => $taxonomies->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'is_hierarchical' => $t->is_hierarchical,
                'post_types' => $t->postTypes(),
            ]),
        ]);
    }

    protected function validateData(Request $request, ?Taxonomy $taxonomy = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|alpha_dash|unique:rj_taxonomies,slug' . ($taxonomy ? ',' . $taxonomy->id : ''),
            'singular_name' => 'required|string|max:100',
            'plural_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'is_hierarchical' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
            'show_in_menu' => 'nullable|boolean',
            'post_types' => 'nullable|array',
            'post_types.*' => 'string',
            'labels' => 'nullable|array',
            'settings' => 'nullable|array',
            'status' => 'nullable|in:active,inactive',
        ]);
    }
}
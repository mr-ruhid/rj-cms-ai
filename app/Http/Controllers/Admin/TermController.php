<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index(Request $request)
    {
        $taxonomySlug = $request->input('taxonomy', '');

        $taxonomies = Taxonomy::active()->orderBy('name')->get();

        if ($taxonomySlug === '' && $taxonomies->isNotEmpty()) {
            $taxonomySlug = $taxonomies->first()->slug;
        }

        $taxonomy = $taxonomySlug
            ? Taxonomy::where('slug', $taxonomySlug)->first()
            : null;

        $terms = Term::with(['translations', 'parent'])
            ->where('taxonomy', $taxonomySlug)
            ->when($request->input('search'), function ($q, $s) {
                $q->where(function ($inner) use ($s) {
                    $inner->where('name', 'like', '%' . $s . '%')
                          ->orWhere('slug', 'like', '%' . $s . '%');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        $tree = $taxonomy && $taxonomy->is_hierarchical
            ? Term::tree($taxonomySlug)
            : [];

        $counts = [
            'all' => Term::where('taxonomy', $taxonomySlug)->count(),
            'active' => Term::where('taxonomy', $taxonomySlug)->where('is_active', true)->count(),
            'inactive' => Term::where('taxonomy', $taxonomySlug)->where('is_active', false)->count(),
        ];

        return view('admin.terms.index', compact('taxonomy', 'taxonomySlug', 'taxonomies', 'terms', 'tree', 'counts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'taxonomy' => 'required|string|exists:rj_taxonomies,slug',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:rj_terms,id',
            'featured_image' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'translations' => 'nullable|array',
        ]);

        $term = Term::create([
            'taxonomy' => $validated['taxonomy'],
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Term::uniqueSlug($validated['taxonomy'], $validated['name']),
            'description' => $validated['description'] ?? null,
            'featured_image' => $validated['featured_image'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $this->syncTranslations($term, $validated['translations'] ?? []);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'term' => $term->load('translations'),
            ]);
        }

        return back()->with('success', __('Term created.'));
    }

    public function show(Term $term)
    {
        $term->load(['translations', 'parent', 'children']);

        return view('admin.terms.show', compact('term'));
    }

    public function edit(Term $term)
    {
        $term->load(['translations', 'parent']);

        $taxonomies = Taxonomy::active()->orderBy('name')->get();
        $locales = rj_available_locales();
        $baseLocale = config('app.locale');

        $parents = Term::where('taxonomy', $term->taxonomy)
            ->where('id', '!=', $term->id)
            ->orderBy('name')
            ->get();

        return view('admin.terms.edit', compact('term', 'taxonomies', 'parents', 'locales', 'baseLocale'));
    }

    public function update(Request $request, Term $term)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:rj_terms,id',
            'featured_image' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'translations' => 'nullable|array',
        ]);

        if (isset($validated['parent_id']) && $validated['parent_id'] == $term->id) {
            unset($validated['parent_id']);
        }

        $term->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $term->slug,
            'description' => $validated['description'] ?? $term->description,
            'parent_id' => $validated['parent_id'] ?? $term->parent_id,
            'featured_image' => $validated['featured_image'] ?? $term->featured_image,
            'seo_title' => $validated['seo_title'] ?? $term->seo_title,
            'seo_description' => $validated['seo_description'] ?? $term->seo_description,
            'sort_order' => $validated['sort_order'] ?? $term->sort_order,
            'is_active' => $validated['is_active'] ?? $term->is_active,
        ]);

        if (array_key_exists('translations', $validated)) {
            $this->syncTranslations($term, $validated['translations'] ?? []);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'term' => $term->fresh()]);
        }

        return back()->with('success', __('Term saved.'));
    }

    public function destroy(Term $term)
    {
        if ($term->children()->exists()) {
            $message = __('Cannot delete a term that has children.');

            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $taxonomy = $term->taxonomy;

        $term->posts()->detach();
        $term->translations()->delete();
        $term->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.terms.index', ['taxonomy' => $taxonomy])
            ->with('success', __('Term deleted.'));
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:rj_terms,id',
            'action' => 'required|in:activate,deactivate,delete',
        ]);

        $query = Term::whereIn('id', $validated['ids']);

        $count = match ($validated['action']) {
            'activate' => $query->update(['is_active' => true]),
            'deactivate' => $query->update(['is_active' => false]),
            'delete' => $query->delete(),
            default => 0,
        };

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => __(':n term(s) updated.', ['n' => $count]),
            ]);
        }

        return back()->with('success', __(':n term(s) updated.', ['n' => $count]));
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:rj_terms,id',
            'items.*.sort_order' => 'required|integer|min:0',
            'items.*.parent_id' => 'nullable|integer',
        ]);

        foreach ($validated['items'] as $item) {
            Term::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
                'parent_id' => $item['parent_id'] ?? null,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Order updated.'));
    }

    public function options(Request $request)
    {
        $taxonomy = $request->input('taxonomy', '');

        if ($taxonomy === '') {
            return response()->json(['terms' => []]);
        }

        $terms = Term::where('taxonomy', $taxonomy)
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'terms' => $terms->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'parent_id' => $t->parent_id,
                'post_count' => $t->post_count,
            ]),
        ]);
    }

    protected function syncTranslations(Term $term, array $translations): void
    {
        foreach ($translations as $locale => $data) {
            if (!is_string($locale) || !is_array($data)) {
                continue;
            }

            if ($locale === config('app.locale')) {
                continue;
            }

            $payload = array_filter([
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
            ], fn($v) => $v !== null && $v !== '');

            if (empty($payload)) {
                $term->translations()->where('locale', $locale)->delete();
                continue;
            }

            $term->translations()->updateOrCreate(['locale' => $locale], $payload);
        }
    }
}
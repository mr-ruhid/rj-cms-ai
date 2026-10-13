<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with(['translations', 'parent.translations']);

        if ($search = $request->input('search')) {
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('slug', 'like', "%{$search}%");
        }

        $categories = $query->ordered()->get();

        $tree = $this->buildTree($categories);

        $stats = [
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'roots' => Category::whereNull('parent_id')->count(),
            'empty' => Category::where('post_count', 0)->count(),
        ];

        return view('admin.categories.index', compact('tree', 'stats'));
    }

    public function create()
    {
        $categories = Category::with('translations')->ordered()->get();
        $locales = rj_available_locales();

        return view('admin.categories.create', compact('categories', 'locales'));
    }

    public function store(Request $request)
    {
        $baseLocale = config('app.locale');
        $locales = rj_available_locales();

        $rules = [
            'slug' => ['nullable', 'string', 'max:200', 'regex:/^[a-z0-9\-]+$/', 'unique:rj_categories,slug'],
            'parent_id' => ['nullable', 'integer', 'exists:rj_categories,id'],
            'image' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'is_active' => ['nullable', 'boolean'],
            'translations' => ['required', 'array'],
        ];

        foreach ($locales as $locale) {
            $rules["translations.{$locale}.name"] = $locale === $baseLocale
                ? ['required', 'string', 'max:200']
                : ['nullable', 'string', 'max:200'];
            $rules["translations.{$locale}.description"] = ['nullable', 'string', 'max:5000'];
            $rules["translations.{$locale}.seo_title"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$locale}.seo_description"] = ['nullable', 'string', 'max:500'];
            $rules["translations.{$locale}.seo_image"] = ['nullable', 'string', 'max:500'];
        }

        $data = $request->validate($rules);

        $baseName = $data['translations'][$baseLocale]['name'];
        $slug = !empty($data['slug']) ? $data['slug'] : Str::slug($baseName);

        if (empty($slug)) {
            $slug = 'category-' . Str::random(6);
        }

        $slug = $this->uniqueSlug($slug);

        $category = Category::create([
            'slug' => $slug,
            'parent_id' => $data['parent_id'] ?? null,
            'image' => $data['image'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        foreach ($locales as $locale) {
            $t = $data['translations'][$locale] ?? [];
            $name = $t['name'] ?? null;

            if (empty($name)) {
                continue;
            }

            CategoryTranslation::create([
                'category_id' => $category->id,
                'locale' => $locale,
                'name' => $name,
                'description' => $t['description'] ?? null,
                'seo_title' => $t['seo_title'] ?? null,
                'seo_description' => $t['seo_description'] ?? null,
                'seo_image' => $t['seo_image'] ?? null,
            ]);
        }

        return redirect()->route('admin.posts.categories')
            ->with('success', __('Category created.'));
    }

    public function edit(Category $category)
    {
        $category->load('translations');

        $categories = Category::with('translations')
            ->where('id', '!=', $category->id)
            ->ordered()
            ->get();

        $excludeIds = $this->getDescendantIds($category);
        $categories = $categories->whereNotIn('id', $excludeIds);

        $locales = rj_available_locales();

        return view('admin.categories.edit', compact('category', 'categories', 'locales'));
    }

    public function update(Request $request, Category $category)
    {
        $baseLocale = config('app.locale');
        $locales = rj_available_locales();

        $rules = [
            'slug' => ['nullable', 'string', 'max:200', 'regex:/^[a-z0-9\-]+$/', Rule::unique('rj_categories', 'slug')->ignore($category->id)],
            'parent_id' => ['nullable', 'integer', 'exists:rj_categories,id'],
            'image' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'is_active' => ['nullable', 'boolean'],
            'translations' => ['required', 'array'],
        ];

        foreach ($locales as $locale) {
            $rules["translations.{$locale}.name"] = $locale === $baseLocale
                ? ['required', 'string', 'max:200']
                : ['nullable', 'string', 'max:200'];
            $rules["translations.{$locale}.description"] = ['nullable', 'string', 'max:5000'];
            $rules["translations.{$locale}.seo_title"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$locale}.seo_description"] = ['nullable', 'string', 'max:500'];
            $rules["translations.{$locale}.seo_image"] = ['nullable', 'string', 'max:500'];
        }

        $data = $request->validate($rules);

        if (!empty($data['parent_id']) && (int) $data['parent_id'] === $category->id) {
            return back()->with('error', __('A category cannot be its own parent.'))->withInput();
        }

        $baseName = $data['translations'][$baseLocale]['name'];
        $slug = !empty($data['slug']) ? $data['slug'] : Str::slug($baseName);
        $slug = $this->uniqueSlug($slug, $category->id);

        $category->update([
            'slug' => $slug,
            'parent_id' => $data['parent_id'] ?? null,
            'image' => $data['image'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        foreach ($locales as $locale) {
            $t = $data['translations'][$locale] ?? [];
            $name = $t['name'] ?? null;

            $existing = CategoryTranslation::where('category_id', $category->id)
                ->where('locale', $locale)
                ->first();

            if (empty($name)) {
                if ($existing && $locale !== $baseLocale) {
                    $existing->delete();
                }
                continue;
            }

            if ($existing) {
                $existing->update([
                    'name' => $name,
                    'description' => $t['description'] ?? null,
                    'seo_title' => $t['seo_title'] ?? null,
                    'seo_description' => $t['seo_description'] ?? null,
                    'seo_image' => $t['seo_image'] ?? null,
                ]);
            } else {
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'locale' => $locale,
                    'name' => $name,
                    'description' => $t['description'] ?? null,
                    'seo_title' => $t['seo_title'] ?? null,
                    'seo_description' => $t['seo_description'] ?? null,
                    'seo_image' => $t['seo_image'] ?? null,
                ]);
            }
        }

        return back()->with('success', __('Category updated.'));
    }

    public function destroy(Category $category)
    {
        if ($category->children()->count() > 0) {
            return back()->with('error', __('Cannot delete a category with subcategories. Delete or move them first.'));
        }

        $category->delete();

        return back()->with('success', __('Category deleted.'));
    }

    protected function buildTree($categories, $parentId = null, $depth = 0): array
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category->parent_id === $parentId) {
                $tree[] = [
                    'category' => $category,
                    'depth' => $depth,
                ];

                $children = $this->buildTree($categories, $category->id, $depth + 1);
                $tree = array_merge($tree, $children);
            }
        }

        return $tree;
    }

    protected function getDescendantIds(Category $category): array
    {
        $ids = [];
        $children = Category::where('parent_id', $category->id)->get();

        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getDescendantIds($child));
        }

        return $ids;
    }

    protected function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $i = 1;

        while (true) {
            $query = Category::where('slug', $slug);

            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $slug = $base . '-' . $i;
            $i++;

            if ($i > 100) {
                return $base . '-' . Str::random(6);
            }
        }
    }
}
<?php

namespace RjCommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use RjCommerce\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::with(['parent', 'translations'])->orderBy('sort_order')->paginate(15);
        $locales = rj_available_locales();
        return view('plugin-rj-commerce::admin.categories.index', compact('categories', 'locales'));
    }

    public function create()
    {
        $parents = ProductCategory::orderBy('id')->get();
        $locales = rj_available_locales();
        return view('plugin-rj-commerce::admin.categories.create', compact('parents', 'locales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'nullable|string|max:255|unique:rj_product_categories,slug',
            'parent_id' => 'nullable|exists:rj_product_categories,id',
            'image' => 'nullable|image|max:5120',
            'is_active' => 'boolean',
            'translations' => 'required|array',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_description' => 'nullable|string',
        ]);

        $baseName = $validated['translations'][config('app.locale')]['name'] ?? '';
        if (empty($baseName)) {
            foreach ($validated['translations'] as $trans) {
                if (!empty($trans['name'])) {
                    $baseName = $trans['name'];
                    break;
                }
            }
        }

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($baseName);
        if (empty($slug)) {
            $slug = 'category';
        }

        $originalSlug = $slug;
        $count = 1;
        while (ProductCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category = ProductCategory::create([
            'slug' => $slug,
            'parent_id' => $validated['parent_id'] ?? null,
            'image' => $imagePath,
            'is_active' => $request->boolean('is_active', true),
        ]);

        foreach ($validated['translations'] as $locale => $data) {
            if (!empty($data['name'])) {
                $category->translations()->create([
                    'locale' => $locale,
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'seo_title' => $data['seo_title'] ?? null,
                    'seo_description' => $data['seo_description'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.commerce.categories.index')->with('success', __('Category created successfully.'));
    }

    public function edit(ProductCategory $category)
    {
        $category->load('translations');
        $parents = ProductCategory::where('id', '!=', $category->id)->orderBy('id')->get();
        $locales = rj_available_locales();
        return view('plugin-rj-commerce::admin.categories.edit', compact('category', 'parents', 'locales'));
    }

    public function update(Request $request, ProductCategory $category)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:rj_product_categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:rj_product_categories,id',
            'image' => 'nullable|image|max:5120',
            'is_active' => 'boolean',
            'translations' => 'required|array',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_description' => 'nullable|string',
        ]);

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category->update([
            'slug' => Str::slug($validated['slug']),
            'parent_id' => $validated['parent_id'] ?? null,
            'image' => $imagePath,
            'is_active' => $request->boolean('is_active'),
        ]);

        foreach ($validated['translations'] as $locale => $data) {
            if (!empty($data['name'])) {
                $category->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $data['name'],
                        'description' => $data['description'] ?? null,
                        'seo_title' => $data['seo_title'] ?? null,
                        'seo_description' => $data['seo_description'] ?? null,
                    ]
                );
            } else {
                $category->translations()->where('locale', $locale)->delete();
            }
        }

        return redirect()->route('admin.commerce.categories.index')->with('success', __('Category updated successfully.'));
    }

    public function destroy(ProductCategory $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return back()->with('success', __('Category deleted successfully.'));
    }
}

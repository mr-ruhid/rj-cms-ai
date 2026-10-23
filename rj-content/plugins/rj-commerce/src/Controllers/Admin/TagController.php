<?php

namespace RjCommerce\Controllers;

use App\Http\Controllers\Controller;
use RjCommerce\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = ProductTag::with('translations')->latest()->paginate(15);
        $locales = rj_available_locales();

        return view('plugin-rj-commerce::admin.tags.index', compact('tags', 'locales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'nullable|string|max:255|unique:rj_product_tags,slug',
            'translations' => 'required|array',
            'translations.*.name' => 'nullable|string|max:255',
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
            $slug = 'tag';
        }

        $originalSlug = $slug;
        $count = 1;
        while (ProductTag::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $tag = ProductTag::create([
            'slug' => $slug,
            'is_active' => true,
        ]);

        foreach ($validated['translations'] as $locale => $data) {
            if (!empty($data['name'])) {
                $tag->translations()->create([
                    'locale' => $locale,
                    'name' => $data['name'],
                ]);
            }
        }

        return back()->with('success', __('Tag created successfully.'));
    }

    public function update(Request $request, ProductTag $tag)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:rj_product_tags,slug,' . $tag->id,
            'translations' => 'required|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $tag->update([
            'slug' => Str::slug($validated['slug']),
        ]);

        foreach ($validated['translations'] as $locale => $data) {
            if (!empty($data['name'])) {
                $tag->translations()->updateOrCreate(
                    ['locale' => $locale],
                    ['name' => $data['name']]
                );
            } else {
                $tag->translations()->where('locale', $locale)->delete();
            }
        }

        return back()->with('success', __('Tag updated successfully.'));
    }

    public function destroy(ProductTag $tag)
    {
        $tag->delete();

        return back()->with('success', __('Tag deleted successfully.'));
    }
}

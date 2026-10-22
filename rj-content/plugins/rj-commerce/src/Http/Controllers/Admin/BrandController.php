<?php

namespace RjCommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use RjCommerce\Models\ProductBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = ProductBrand::with('translations')->latest()->paginate(15);
        $locales = rj_available_locales();
        return view('plugin-rj-commerce::admin.brands.index', compact('brands', 'locales'));
    }

    public function create()
    {
        $locales = rj_available_locales();
        return view('plugin-rj-commerce::admin.brands.create', compact('locales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'nullable|string|max:255|unique:rj_product_brands,slug',
            'logo' => 'nullable|image|max:5120',
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
            $slug = 'brand';
        }

        $originalSlug = $slug;
        $count = 1;
        while (ProductBrand::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brands', 'public');
        }

        $brand = ProductBrand::create([
            'slug' => $slug,
            'logo' => $logoPath,
            'is_active' => $request->boolean('is_active', true),
        ]);

        foreach ($validated['translations'] as $locale => $data) {
            if (!empty($data['name'])) {
                $brand->translations()->create([
                    'locale' => $locale,
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'seo_title' => $data['seo_title'] ?? null,
                    'seo_description' => $data['seo_description'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.commerce.brands.index')->with('success', __('Brand created successfully.'));
    }

    public function edit(ProductBrand $brand)
    {
        $brand->load('translations');
        $locales = rj_available_locales();
        return view('plugin-rj-commerce::admin.brands.edit', compact('brand', 'locales'));
    }

    public function update(Request $request, ProductBrand $brand)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:rj_product_brands,slug,' . $brand->id,
            'logo' => 'nullable|image|max:5120',
            'is_active' => 'boolean',
            'translations' => 'required|array',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_description' => 'nullable|string',
        ]);

        $logoPath = $brand->logo;
        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $logoPath = $request->file('logo')->store('brands', 'public');
        }

        $brand->update([
            'slug' => Str::slug($validated['slug']),
            'logo' => $logoPath,
            'is_active' => $request->boolean('is_active'),
        ]);

        foreach ($validated['translations'] as $locale => $data) {
            if (!empty($data['name'])) {
                $brand->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $data['name'],
                        'description' => $data['description'] ?? null,
                        'seo_title' => $data['seo_title'] ?? null,
                        'seo_description' => $data['seo_description'] ?? null,
                    ]
                );
            } else {
                $brand->translations()->where('locale', $locale)->delete();
            }
        }

        return redirect()->route('admin.commerce.brands.index')->with('success', __('Brand updated successfully.'));
    }

    public function destroy(ProductBrand $brand)
    {
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return back()->with('success', __('Brand deleted successfully.'));
    }
}

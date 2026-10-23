<?php

namespace RjCommerce\Controllers\Admin;

use App\Http\Controllers\Controller;
use RjCommerce\Models\ProductAttribute;
use RjCommerce\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = ProductAttribute::with(['translations', 'values.translations'])->orderBy('sort_order')->paginate(15);
        $locales = rj_available_locales();
        return view('plugin-rj-commerce::admin.attributes.index', compact('attributes', 'locales'));
    }

    public function create()
    {
        $locales = rj_available_locales();
        return view('plugin-rj-commerce::admin.attributes.create', compact('locales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'nullable|string|max:255|unique:rj_product_attributes,slug',
            'type' => 'required|in:select,color,button',
            'translations' => 'required|array',
            'translations.*.name' => 'nullable|string|max:255',
            'values' => 'nullable|array',
            'values.*.translations.*.name' => 'nullable|string|max:255',
            'values.*.color_code' => 'nullable|string|max:20',
        ]);

        $baseName = $validated['translations'][config('app.locale')]['name'] ?? '';
        if (empty($baseName)) {
            foreach ($validated['translations'] as $trans) {
                if (!empty($trans['name'])) { $baseName = $trans['name']; break; }
            }
        }

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($baseName);
        if (empty($slug)) { $slug = 'attribute'; }

        $originalSlug = $slug;
        $count = 1;
        while (ProductAttribute::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        DB::transaction(function () use ($validated, $slug) {
            $attribute = ProductAttribute::create([
                'slug' => $slug,
                'type' => $validated['type'],
                'is_active' => true,
            ]);

            foreach ($validated['translations'] as $locale => $data) {
                if (!empty($data['name'])) {
                    $attribute->translations()->create([
                        'locale' => $locale,
                        'name' => $data['name'],
                    ]);
                }
            }

            if (!empty($validated['values'])) {
                foreach ($validated['values'] as $valIndex => $valData) {
                    $valBaseName = $valData['translations'][config('app.locale')]['name'] ?? '';
                    if (empty($valBaseName)) {
                        foreach ($valData['translations'] as $trans) {
                            if (!empty($trans['name'])) { $valBaseName = $trans['name']; break; }
                        }
                    }

                    $valSlug = Str::slug($valBaseName);
                    if (empty($valSlug)) { $valSlug = 'value-' . $valIndex; }

                    $value = $attribute->values()->create([
                        'slug' => $valSlug,
                        'color_code' => $valData['color_code'] ?? null,
                        'sort_order' => $valIndex,
                    ]);

                    foreach ($valData['translations'] as $locale => $transData) {
                        if (!empty($transData['name'])) {
                            $value->translations()->create([
                                'locale' => $locale,
                                'name' => $transData['name'],
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('admin.commerce.attributes.index')->with('success', __('Attribute created successfully.'));
    }

    public function edit(ProductAttribute $attribute)
    {
        $attribute->load(['translations', 'values.translations']);
        $locales = rj_available_locales();
        return view('plugin-rj-commerce::admin.attributes.edit', compact('attribute', 'locales'));
    }

    public function update(Request $request, ProductAttribute $attribute)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:rj_product_attributes,slug,' . $attribute->id,
            'type' => 'required|in:select,color,button',
            'translations' => 'required|array',
            'translations.*.name' => 'nullable|string|max:255',
            'values' => 'nullable|array',
            'values.*.id' => 'nullable|exists:rj_product_attribute_values,id',
            'values.*.translations.*.name' => 'nullable|string|max:255',
            'values.*.color_code' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($validated, $attribute) {
            $attribute->update([
                'slug' => Str::slug($validated['slug']),
                'type' => $validated['type'],
            ]);

            foreach ($validated['translations'] as $locale => $data) {
                if (!empty($data['name'])) {
                    $attribute->translations()->updateOrCreate(
                        ['locale' => $locale],
                        ['name' => $data['name']]
                    );
                } else {
                    $attribute->translations()->where('locale', $locale)->delete();
                }
            }

            $existingValueIds = [];
            if (!empty($validated['values'])) {
                foreach ($validated['values'] as $valIndex => $valData) {
                    $valBaseName = $valData['translations'][config('app.locale')]['name'] ?? '';
                    if (empty($valBaseName)) {
                        foreach ($valData['translations'] as $trans) {
                            if (!empty($trans['name'])) { $valBaseName = $trans['name']; break; }
                        }
                    }
                    $valSlug = Str::slug($valBaseName);
                    if (empty($valSlug)) { $valSlug = 'value-' . $valIndex; }

                    $value = $attribute->values()->updateOrCreate(
                        ['id' => $valData['id'] ?? null],
                        [
                            'slug' => $valSlug,
                            'color_code' => $valData['color_code'] ?? null,
                            'sort_order' => $valIndex,
                        ]
                    );

                    $existingValueIds[] = $value->id;

                    foreach ($valData['translations'] as $locale => $transData) {
                        if (!empty($transData['name'])) {
                            $value->translations()->updateOrCreate(
                                ['locale' => $locale],
                                ['name' => $transData['name']]
                            );
                        } else {
                            $value->translations()->where('locale', $locale)->delete();
                        }
                    }
                }
            }

            if (count($existingValueIds) > 0) {
                $attribute->values()->whereNotIn('id', $existingValueIds)->delete();
            } else {
                $attribute->values()->delete();
            }
        });

        return redirect()->route('admin.commerce.attributes.index')->with('success', __('Attribute updated successfully.'));
    }

    public function destroy(ProductAttribute $attribute)
    {
        $attribute->delete();
        return back()->with('success', __('Attribute deleted successfully.'));
    }
}

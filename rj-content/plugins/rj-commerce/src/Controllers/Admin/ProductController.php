<?php

namespace RjCommerce\Controllers\Admin;

use App\Http\Controllers\Controller;
use RjCommerce\Models\Product;
use RjCommerce\Models\ProductCategory;
use RjCommerce\Models\ProductBrand;
use RjCommerce\Models\ProductTag;
use RjCommerce\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['translation', 'category', 'brand'])->latest()->paginate(15);
        return view('plugin-rj-commerce::admin.products.index', compact('products'));
    }

    public function create()
    {
        $locales = rj_available_locales();
        $categories = ProductCategory::with('translations')->orderBy('sort_order')->get();
        $brands = ProductBrand::with('translations')->orderBy('id')->get();
        $tags = ProductTag::with('translations')->orderBy('id')->get();
        $attributes = \RjCommerce\Models\ProductAttribute::with(['translations', 'values.translations'])->where('is_active', true)->get();

        return view('plugin-rj-commerce::admin.products.create', compact('locales', 'categories', 'brands', 'tags', 'attributes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'nullable|string|max:255|unique:rj_products,slug',
            'type' => 'required|in:physical,digital,external',
            'sku' => 'nullable|string|max:255|unique:rj_products,sku',
            'barcode' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'stock_status' => 'required|in:in_stock,out_of_stock,on_backorder',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'external_url' => 'nullable|url|max:255',
            'download_limit' => 'nullable|integer|min:0',
            'download_expiry' => 'nullable|integer|min:0',
            'license_keys_pool' => 'nullable|string',
            'auto_generate_license' => 'boolean',
            'category_id' => 'nullable|exists:rj_product_categories,id',
            'brand_id' => 'nullable|exists:rj_product_brands,id',
            'status' => 'required|in:draft,published',
            'is_featured' => 'boolean',
            'featured_image' => 'nullable|image|max:5120',
            'gallery.*' => 'nullable|image|max:5120',
            'downloadable_file' => 'nullable|file|max:102400',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.short_description' => 'nullable|string',
            'translations.*.description' => 'nullable|string',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:rj_product_tags,id',
            'variations' => 'nullable|array',
            'variations.*.sku' => 'nullable|string|max:255',
            'variations.*.price' => 'nullable|numeric|min:0',
            'variations.*.compare_price' => 'nullable|numeric|min:0',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.attributes' => 'required|array',
            'variations.*.image' => 'nullable|image|max:5120',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $baseName = $validated['translations'][config('app.locale')]['name'] ?? '';
            if (empty($baseName)) {
                foreach ($validated['translations'] as $trans) {
                    if (!empty($trans['name'])) { $baseName = $trans['name']; break; }
                }
            }
            $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($baseName);
            if (empty($slug)) { $slug = 'product'; }

            $originalSlug = $slug;
            $count = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $featuredImage = $request->hasFile('featured_image') ? $request->file('featured_image')->store('products', 'public') : null;

            $gallery = [];
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $file) {
                    $gallery[] = $file->store('products/gallery', 'public');
                }
            }

            $downloadableFiles = [];
            if ($validated['type'] === 'digital' && $request->hasFile('downloadable_file')) {
                $filePath = $request->file('downloadable_file')->store('products/downloads', 'public');
                $downloadableFiles[] = [
                    'name' => $request->file('downloadable_file')->getClientOriginalName(),
                    'file' => $filePath
                ];
            }

            $licenseKeys = [];
            if (!empty($validated['license_keys_pool'])) {
                $licenseKeys = array_filter(array_map('trim', explode("\n", $validated['license_keys_pool'])));
            }

            $product = Product::create([
                'slug' => $slug,
                'type' => $validated['type'],
                'sku' => $validated['sku'] ?? null,
                'barcode' => $validated['barcode'] ?? null,
                'price' => $validated['price'],
                'compare_price' => $validated['compare_price'] ?? null,
                'manage_stock' => $request->boolean('manage_stock'),
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'stock_status' => $validated['stock_status'],
                'weight' => $validated['weight'] ?? null,
                'length' => $validated['length'] ?? null,
                'width' => $validated['width'] ?? null,
                'height' => $validated['height'] ?? null,
                'downloadable_files' => $downloadableFiles,
                'download_limit' => $validated['download_limit'] ?? null,
                'download_expiry' => $validated['download_expiry'] ?? null,
                'external_url' => $validated['external_url'] ?? null,
                'license_keys_pool' => $licenseKeys,
                'auto_generate_license' => $request->boolean('auto_generate_license'),
                'category_id' => $validated['category_id'] ?? null,
                'brand_id' => $validated['brand_id'] ?? null,
                'featured_image' => $featuredImage,
                'gallery' => $gallery,
                'is_featured' => $request->boolean('is_featured'),
                'status' => $validated['status'],
            ]);

            foreach ($validated['translations'] as $locale => $data) {
                if (!empty($data['name'])) {
                    $product->translations()->create([
                        'locale' => $locale,
                        'name' => $data['name'],
                        'short_description' => $data['short_description'] ?? null,
                        'description' => $data['description'] ?? null,
                        'seo_title' => $data['seo_title'] ?? null,
                        'seo_description' => $data['seo_description'] ?? null,
                    ]);
                }
            }

            if (!empty($validated['tags'])) {
                $product->tags()->sync($validated['tags']);
            }

            if (!empty($validated['variations'])) {
                foreach ($validated['variations'] as $index => $varData) {
                    $varImage = null;
                    if ($request->hasFile("variations.$index.image")) {
                        $varImage = $request->file("variations.$index.image")->store('products/variations', 'public');
                    }

                    $product->variations()->create([
                        'sku' => $varData['sku'] ?? null,
                        'price' => $varData['price'] ?? null,
                        'compare_price' => $varData['compare_price'] ?? null,
                        'stock_quantity' => $varData['stock_quantity'] ?? 0,
                        'attributes' => $varData['attributes'],
                        'image' => $varImage,
                    ]);
                }
            }
        });

        return redirect()->route('admin.commerce.products.index')->with('success', __('Product created successfully.'));
    }

    public function edit(Product $product)
    {
        $product->load(['translations', 'variations', 'tags']);
        $locales = rj_available_locales();
        $categories = ProductCategory::with('translations')->orderBy('sort_order')->get();
        $brands = ProductBrand::with('translations')->orderBy('id')->get();
        $tags = ProductTag::with('translations')->orderBy('id')->get();
        $attributes = \RjCommerce\Models\ProductAttribute::with(['translations', 'values.translations'])->where('is_active', true)->get();

        return view('plugin-rj-commerce::admin.products.edit', compact('product', 'locales', 'categories', 'brands', 'tags', 'attributes'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:rj_products,slug,' . $product->id,
            'type' => 'required|in:physical,digital,external',
            'sku' => 'nullable|string|max:255|unique:rj_products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'stock_status' => 'required|in:in_stock,out_of_stock,on_backorder',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'external_url' => 'nullable|url|max:255',
            'download_limit' => 'nullable|integer|min:0',
            'download_expiry' => 'nullable|integer|min:0',
            'license_keys_pool' => 'nullable|string',
            'auto_generate_license' => 'boolean',
            'category_id' => 'nullable|exists:rj_product_categories,id',
            'brand_id' => 'nullable|exists:rj_product_brands,id',
            'status' => 'required|in:draft,published',
            'is_featured' => 'boolean',
            'featured_image' => 'nullable|image|max:5120',
            'gallery.*' => 'nullable|image|max:5120',
            'downloadable_file' => 'nullable|file|max:102400',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.short_description' => 'nullable|string',
            'translations.*.description' => 'nullable|string',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:rj_product_tags,id',
            'variations' => 'nullable|array',
            'variations.*.id' => 'nullable|exists:rj_product_variations,id',
            'variations.*.sku' => 'nullable|string|max:255',
            'variations.*.price' => 'nullable|numeric|min:0',
            'variations.*.compare_price' => 'nullable|numeric|min:0',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.attributes' => 'required|array',
            'variations.*.image' => 'nullable|image|max:5120',
        ]);

        DB::transaction(function () use ($request, $validated, $product) {
            $featuredImage = $product->featured_image;
            if ($request->hasFile('featured_image')) {
                if ($featuredImage) Storage::disk('public')->delete($featuredImage);
                $featuredImage = $request->file('featured_image')->store('products', 'public');
            }

            $gallery = $product->gallery ?? [];
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $file) {
                    $gallery[] = $file->store('products/gallery', 'public');
                }
            }

            $downloadableFiles = $product->downloadable_files ?? [];
            if ($validated['type'] === 'digital' && $request->hasFile('downloadable_file')) {
                $filePath = $request->file('downloadable_file')->store('products/downloads', 'public');
                $downloadableFiles[] = [
                    'name' => $request->file('downloadable_file')->getClientOriginalName(),
                    'file' => $filePath
                ];
            }

            $licenseKeys = $product->license_keys_pool ?? [];
            if (isset($validated['license_keys_pool'])) {
                $licenseKeys = array_filter(array_map('trim', explode("\n", $validated['license_keys_pool'])));
            }

            $product->update([
                'slug' => Str::slug($validated['slug']),
                'type' => $validated['type'],
                'sku' => $validated['sku'] ?? null,
                'barcode' => $validated['barcode'] ?? null,
                'price' => $validated['price'],
                'compare_price' => $validated['compare_price'] ?? null,
                'manage_stock' => $request->boolean('manage_stock'),
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'stock_status' => $validated['stock_status'],
                'weight' => $validated['weight'] ?? null,
                'length' => $validated['length'] ?? null,
                'width' => $validated['width'] ?? null,
                'height' => $validated['height'] ?? null,
                'downloadable_files' => $downloadableFiles,
                'download_limit' => $validated['download_limit'] ?? null,
                'download_expiry' => $validated['download_expiry'] ?? null,
                'external_url' => $validated['external_url'] ?? null,
                'license_keys_pool' => $licenseKeys,
                'auto_generate_license' => $request->boolean('auto_generate_license'),
                'category_id' => $validated['category_id'] ?? null,
                'brand_id' => $validated['brand_id'] ?? null,
                'featured_image' => $featuredImage,
                'gallery' => $gallery,
                'is_featured' => $request->boolean('is_featured'),
                'status' => $validated['status'],
            ]);

            foreach ($validated['translations'] as $locale => $data) {
                if (!empty($data['name'])) {
                    $product->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $data['name'],
                            'short_description' => $data['short_description'] ?? null,
                            'description' => $data['description'] ?? null,
                            'seo_title' => $data['seo_title'] ?? null,
                            'seo_description' => $data['seo_description'] ?? null,
                        ]
                    );
                } else {
                    $product->translations()->where('locale', $locale)->delete();
                }
            }

            $product->tags()->sync($validated['tags'] ?? []);

            $existingVarIds = [];
            if (!empty($validated['variations'])) {
                foreach ($validated['variations'] as $index => $varData) {
                    $varImage = null;
                    if ($request->hasFile("variations.$index.image")) {
                        $varImage = $request->file("variations.$index.image")->store('products/variations', 'public');
                    }

                    $variation = $product->variations()->updateOrCreate(
                        ['id' => $varData['id'] ?? null],
                        [
                            'sku' => $varData['sku'] ?? null,
                            'price' => $varData['price'] ?? null,
                            'compare_price' => $varData['compare_price'] ?? null,
                            'stock_quantity' => $varData['stock_quantity'] ?? 0,
                            'attributes' => $varData['attributes'],
                            'image' => $varImage ?? (isset($varData['id']) ? ProductVariation::find($varData['id'])->image : null),
                        ]
                    );
                    $existingVarIds[] = $variation->id;
                }
            }

            if (count($existingVarIds) > 0) {
                $product->variations()->whereNotIn('id', $existingVarIds)->delete();
            } else {
                $product->variations()->delete();
            }
        });

        return redirect()->route('admin.commerce.products.index')->with('success', __('Product updated successfully.'));
    }

    public function destroy(Product $product)
    {
        if ($product->featured_image) Storage::disk('public')->delete($product->featured_image);
        if (!empty($product->gallery)) {
            foreach ($product->gallery as $img) Storage::disk('public')->delete($img);
        }
        if (!empty($product->downloadable_files)) {
            foreach ($product->downloadable_files as $file) {
                if (isset($file['file'])) Storage::disk('public')->delete($file['file']);
            }
        }
        foreach ($product->variations as $var) {
            if ($var->image) Storage::disk('public')->delete($var->image);
        }

        $product->delete();
        return back()->with('success', __('Product deleted successfully.'));
    }
}

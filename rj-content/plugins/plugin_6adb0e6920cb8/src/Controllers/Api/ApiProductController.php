<?php

namespace RjCommerce\Controllers\Api;

use App\Http\Controllers\Controller;
use RjCommerce\Models\Product;
use RjCommerce\Models\ProductCategory;
use RjCommerce\Models\ProductBrand;
use Illuminate\Http\Request;

class ApiProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['translations', 'category.translations', 'brand.translations', 'variations'])
            ->where('status', 'published')
            ->latest();

        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('brand_id') && $request->brand_id !== '') {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('translations', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show($slug)
    {
        $product = Product::with(['translations', 'category.translations', 'brand.translations', 'variations', 'tags.translations'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => __('Product not found.')
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function categories()
    {
        $categories = ProductCategory::with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function brands()
    {
        $brands = ProductBrand::with('translations')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $brands
        ]);
    }
}

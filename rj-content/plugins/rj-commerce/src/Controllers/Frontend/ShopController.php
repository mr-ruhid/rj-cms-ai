<?php

namespace RjCommerce\Controllers\Frontend;

use App\Http\Controllers\Controller;
use RjCommerce\Models\Product;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::with(['translations', 'category', 'brand'])
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        // Əgər tema daxilində shop.blade.php varsa onu, yoxdursa pluginin öz dizaynını yükləyir
        $view = 'plugin-rj-commerce::frontend.shop.index';
        if (view()->exists('theme::commerce.shop.index')) {
            $view = 'theme::commerce.shop.index';
        }

        return view($view, compact('products'));
    }

    public function showProduct($slug)
    {
        $product = Product::with(['translations', 'category', 'brand', 'variations', 'tags'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Əgər tema daxilində single.blade.php varsa onu, yoxdursa pluginin öz dizaynını yükləyir
        $view = 'plugin-rj-commerce::frontend.shop.single';
        if (view()->exists('theme::commerce.shop.single')) {
            $view = 'theme::commerce.shop.single';
        }

        return view($view, compact('product'));
    }
}

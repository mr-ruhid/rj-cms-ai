<?php

namespace RjCommerce\Controllers\Frontend;

use App\Http\Controllers\Controller;
use RjCommerce\Models\Product;
use App\Models\Post;

class ShopController extends Controller
{
    public function index()
    {
        $assignedPageId = rj_get_option('commerce_page_shop');

        if ($assignedPageId) {
            $page = Post::find($assignedPageId);
            if ($page && $page->status === 'published') {
                return redirect()->to($page->public_url);
            }
        }

        $products = Product::with(['translations', 'category', 'brand'])
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

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

        $view = 'plugin-rj-commerce::frontend.shop.single';
        if (view()->exists('theme::commerce.shop.single')) {
            $view = 'theme::commerce.shop.single';
        }

        return view($view, compact('product'));
    }
}

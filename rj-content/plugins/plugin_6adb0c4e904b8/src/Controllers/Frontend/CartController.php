<?php

namespace RjCommerce\Controllers\Frontend;

use App\Http\Controllers\Controller;
use RjCommerce\Models\Product;
use RjCommerce\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $assignedPageId = rj_get_option('commerce_page_cart');

        if ($assignedPageId) {
            $page = \App\Models\Post::find($assignedPageId);
            if ($page && $page->status === 'published') {
                return redirect()->to($page->public_url);
            }
        }

        $cart = session()->get('rj_cart', []);
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $view = 'plugin-rj-commerce::frontend.cart.index';
        if (view()->exists('theme::commerce.cart.index')) {
            $view = 'theme::commerce.cart.index';
        }

        return view($view, compact('cart', 'subtotal'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:rj_products,id',
            'quantity' => 'nullable|integer|min:1',
            'variation_id' => 'nullable|exists:rj_product_variations,id',
        ]);

        $product = Product::with('translations')->findOrFail($validated['product_id']);
        $quantity = $validated['quantity'] ?? 1;
        $variationId = $validated['variation_id'] ?? null;

        $price = $product->price;
        $variationName = null;
        $variationImage = null;

        if ($variationId) {
            $variation = ProductVariation::find($variationId);
            if ($variation) {
                if ($variation->price) {
                    $price = $variation->price;
                }
                $variationName = $variation->sku ?: __('Option');
                if ($variation->image) {
                    $variationImage = Str::startsWith($variation->image, 'http') ? $variation->image : (Str::startsWith($variation->image, 'media/') ? asset($variation->image) : asset('storage/' . $variation->image));
                }
            }
        }

        $image = null;
        if ($product->featured_image) {
            $image = Str::startsWith($product->featured_image, 'http') ? $product->featured_image : (Str::startsWith($product->featured_image, 'media/') ? asset($product->featured_image) : asset('storage/' . $product->featured_image));
        }

        $cartKey = $product->id . ($variationId ? '_' . $variationId : '');
        $cart = session()->get('rj_cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'id' => $product->id,
                'variation_id' => $variationId,
                'name' => $product->translation()?->name ?? __('Product'),
                'variation_name' => $variationName,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $variationImage ?? $image,
                'url' => route('commerce.product.show', $product->slug),
            ];
        }

        session()->put('rj_cart', $cart);

        return response()->json([
            'success' => true,
            'message' => __('Product added to cart successfully.'),
            'cart_count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'cart_key' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('rj_cart', []);

        if (isset($cart[$validated['cart_key']])) {
            $cart[$validated['cart_key']]['quantity'] = $validated['quantity'];
            session()->put('rj_cart', $cart);
            return response()->json(['success' => true, 'message' => __('Cart updated.')]);
        }

        return response()->json(['success' => false, 'message' => __('Item not found.')], 404);
    }

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'cart_key' => 'required|string',
        ]);

        $cart = session()->get('rj_cart', []);

        if (isset($cart[$validated['cart_key']])) {
            unset($cart[$validated['cart_key']]);
            session()->put('rj_cart', $cart);
        }

        return response()->json([
            'success' => true,
            'message' => __('Item removed from cart.'),
            'cart_count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    public function clear()
    {
        session()->forget('rj_cart');

        return response()->json([
            'success' => true,
            'message' => __('Cart cleared.'),
            'cart_count' => 0
        ]);
    }
}

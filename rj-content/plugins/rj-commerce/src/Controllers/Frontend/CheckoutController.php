<?php

namespace RjCommerce\Controllers\Frontend;

use App\Http\Controllers\Controller;
use RjCommerce\Models\Order;
use RjCommerce\Models\OrderItem;
use RjCommerce\Models\Product;
use RjCommerce\Services\OrderMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $assignedPageId = rj_get_option('commerce_page_checkout');

        if ($assignedPageId) {
            $page = \App\Models\Post::find($assignedPageId);
            if ($page && $page->status === 'published') {
                return redirect()->to($page->public_url);
            }
        }

        $cart = session()->get('rj_cart', []);

        if (empty($cart)) {
            return redirect()->route('commerce.cart.index')->with('error', __('Your cart is empty.'));
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $taxEnabled = rj_get_option('commerce_tax_enabled') == '1';
        $taxRate = $taxEnabled ? (float) rj_get_option('commerce_default_tax_rate', 0) : 0;
        $taxTotal = $subtotal * ($taxRate / 100);

        $shippingTotal = 0;
        $total = $subtotal + $taxTotal + $shippingTotal;

        $currency = rj_get_option('commerce_currency', 'USD');
        $paymentMethods = [];

        if (rj_get_option('commerce_cod_enabled', '1') == '1') {
            $paymentMethods['cod'] = __('Cash on Delivery');
        }
        if (rj_get_option('commerce_stripe_enabled', '0') == '1') {
            $paymentMethods['stripe'] = __('Credit Card (Stripe)');
        }
        if (rj_get_option('commerce_paypal_enabled', '0') == '1') {
            $paymentMethods['paypal'] = __('PayPal');
        }

        $view = 'plugin-rj-commerce::frontend.checkout.index';
        if (view()->exists('theme::commerce.checkout.index')) {
            $view = 'theme::commerce.checkout.index';
        }

        return view($view, compact('cart', 'subtotal', 'taxTotal', 'shippingTotal', 'total', 'currency', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('rj_cart', []);

        if (empty($cart)) {
            return redirect()->route('commerce.cart.index')->with('error', __('Your cart is empty.'));
        }

        $validated = $request->validate([
            'billing_first_name' => 'required|string|max:255',
            'billing_last_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'required|string|max:50',
            'billing_address_1' => 'required|string|max:255',
            'billing_city' => 'required|string|max:255',
            'billing_country' => 'required|string|max:255',
            'billing_postal_code' => 'required|string|max:50',

            'shipping_first_name' => 'nullable|string|max:255',
            'shipping_last_name' => 'nullable|string|max:255',
            'shipping_address_1' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_country' => 'nullable|string|max:255',
            'shipping_postal_code' => 'nullable|string|max:50',

            'payment_method' => 'required|string',
            'customer_note' => 'nullable|string',
        ]);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $taxEnabled = rj_get_option('commerce_tax_enabled') == '1';
        $taxRate = $taxEnabled ? (float) rj_get_option('commerce_default_tax_rate', 0) : 0;
        $taxTotal = $subtotal * ($taxRate / 100);
        $shippingTotal = 0;
        $total = $subtotal + $taxTotal + $shippingTotal;

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => auth()->id() ?? null,
                'status' => $validated['payment_method'] === 'cod' ? 'processing' : 'pending',
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'shipping_total' => $shippingTotal,
                'discount_total' => 0,
                'total' => $total,
                'currency' => rj_get_option('commerce_currency', 'USD'),
                'payment_method' => $validated['payment_method'],
                'billing_address' => [
                    'first_name' => $validated['billing_first_name'],
                    'last_name' => $validated['billing_last_name'],
                    'email' => $validated['billing_email'],
                    'phone' => $validated['billing_phone'],
                    'address_line_1' => $validated['billing_address_1'],
                    'city' => $validated['billing_city'],
                    'country' => $validated['billing_country'],
                    'postal_code' => $validated['billing_postal_code'],
                ],
                'shipping_address' => [
                    'first_name' => $validated['shipping_first_name'] ?: $validated['billing_first_name'],
                    'last_name' => $validated['shipping_last_name'] ?: $validated['billing_last_name'],
                    'address_line_1' => $validated['shipping_address_1'] ?: $validated['billing_address_1'],
                    'city' => $validated['shipping_city'] ?: $validated['billing_city'],
                    'country' => $validated['shipping_country'] ?: $validated['billing_country'],
                    'postal_code' => $validated['shipping_postal_code'] ?: $validated['billing_postal_code'],
                ],
                'customer_note' => $validated['customer_note'],
            ]);

            foreach ($cart as $key => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'variation_id' => $item['variation_id'] ?? null,
                    'product_name' => $item['name'],
                    'variation_name' => $item['variation_name'] ?? null,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity'],
                ]);

                $product = Product::find($item['id']);
                if ($product && $product->manage_stock) {
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }

            OrderMailer::send($order, 'created');

            session()->forget('rj_cart');

            DB::commit();

            return redirect()->route('commerce.checkout.success', $order->id);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', __('There was an error processing your order. Please try again.') . ' ' . $e->getMessage())->withInput();
        }
    }

    public function success($orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);

        $view = 'plugin-rj-commerce::frontend.checkout.success';
        if (view()->exists('theme::commerce.checkout.success')) {
            $view = 'theme::commerce.checkout.success';
        }

        return view($view, compact('order'));
    }
}

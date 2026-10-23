<?php

namespace RjCommerce\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use RjCommerce\Models\Order;
use Illuminate\Http\Request;

class ApiOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items', 'user'])->latest();

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'items.variation', 'user']);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,refunded,failed',
        ]);

        $order->update([
            'status' => $validated['status']
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Order status updated successfully.'),
            'data' => $order
        ]);
    }

    public function updateShipping(Request $request, Order $order)
    {
        $validated = $request->validate([
            'tracking_number' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|url|max:255',
            'shipping_carrier' => 'nullable|string|max:255',
        ]);

        $shippingAddress = $order->shipping_address ?? [];

        if (isset($validated['tracking_number'])) {
            $shippingAddress['tracking_number'] = $validated['tracking_number'];
        }
        if (isset($validated['tracking_url'])) {
            $shippingAddress['tracking_url'] = $validated['tracking_url'];
        }
        if (isset($validated['shipping_carrier'])) {
            $shippingAddress['shipping_carrier'] = $validated['shipping_carrier'];
        }

        $order->update([
            'shipping_address' => $shippingAddress
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Shipping information updated successfully.'),
            'data' => $order->shipping_address
        ]);
    }
}

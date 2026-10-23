<?php

namespace RjCommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use RjCommerce\Models\Order;
use RjCommerce\Services\OrderMailer;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(20);
        return view('plugin-rj-commerce::admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'items.variation', 'user']);
        return view('plugin-rj-commerce::admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,refunded,failed',
        ]);

        $order->update([
            'status' => $validated['status']
        ]);

        OrderMailer::send($order, 'updated');

        return back()->with('success', __('Order status updated successfully.'));
    }
}

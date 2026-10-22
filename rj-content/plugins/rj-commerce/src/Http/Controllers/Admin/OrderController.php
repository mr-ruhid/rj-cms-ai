<?php

namespace RjCommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('plugin-rj-commerce::admin.orders.index');
    }

    public function show($id)
    {
        return view('plugin-rj-commerce::admin.orders.show', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return back()->with('success', __('Order status updated successfully.'));
    }
}

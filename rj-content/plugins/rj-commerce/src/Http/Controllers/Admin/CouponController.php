<?php

namespace RjCommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use RjCommerce\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(15);
        return view('plugin-rj-commerce::admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('plugin-rj-commerce::admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:rj_coupons,code',
            'type' => 'required|in:percentage,fixed_cart',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'minimum_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Coupon::create($validated);

        return redirect()->route('admin.commerce.coupons.index')->with('success', __('Coupon created successfully.'));
    }

    public function edit(Coupon $coupon)
    {
        return view('plugin-rj-commerce::admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:rj_coupons,code,' . $coupon->id,
            'type' => 'required|in:percentage,fixed_cart',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'minimum_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        $coupon->update($validated);

        return redirect()->route('admin.commerce.coupons.index')->with('success', __('Coupon updated successfully.'));
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', __('Coupon deleted successfully.'));
    }
}

<?php

namespace RjCommerce\Controllers\Admin;

use App\Http\Controllers\Controller;
use RjCommerce\Models\ShippingZone;
use RjCommerce\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::with('methods')->orderBy('id')->get();
        return view('plugin-rj-commerce::admin.shipping.index', compact('zones'));
    }

    public function storeZone(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'regions' => 'nullable|array',
        ]);

        ShippingZone::create([
            'name' => $validated['name'],
            'regions' => $validated['regions'] ?? [],
        ]);

        return back()->with('success', __('Shipping zone created successfully.'));
    }

    public function updateZone(Request $request, ShippingZone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'regions' => 'nullable|array',
        ]);

        $zone->update([
            'name' => $validated['name'],
            'regions' => $validated['regions'] ?? [],
        ]);

        return back()->with('success', __('Shipping zone updated successfully.'));
    }

    public function destroyZone(ShippingZone $zone)
    {
        $zone->delete();
        return back()->with('success', __('Shipping zone deleted successfully.'));
    }

    public function storeMethod(Request $request, ShippingZone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:flat_rate,free_shipping',
            'cost' => 'nullable|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
        ]);

        $zone->methods()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'cost' => $validated['cost'] ?? 0,
            'min_amount' => $validated['min_amount'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', __('Shipping method added successfully.'));
    }

    public function destroyMethod(ShippingMethod $method)
    {
        $method->delete();
        return back()->with('success', __('Shipping method deleted successfully.'));
    }
}

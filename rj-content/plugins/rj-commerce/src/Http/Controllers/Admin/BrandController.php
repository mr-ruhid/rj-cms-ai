<?php

namespace RjCommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        return view('plugin-rj-commerce::admin.brands.index');
    }

    public function create()
    {
        return view('plugin-rj-commerce::admin.brands.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.commerce.brands.index')->with('success', __('Brand created successfully.'));
    }

    public function edit($id)
    {
        return view('plugin-rj-commerce::admin.brands.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.commerce.brands.index')->with('success', __('Brand updated successfully.'));
    }

    public function destroy($id)
    {
        return back()->with('success', __('Brand deleted successfully.'));
    }
}

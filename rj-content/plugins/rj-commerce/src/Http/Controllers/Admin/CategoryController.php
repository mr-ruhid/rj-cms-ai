<?php

namespace RjCommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('plugin-rj-commerce::admin.categories.index');
    }

    public function create()
    {
        return view('plugin-rj-commerce::admin.categories.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.commerce.categories.index')->with('success', __('Category created successfully.'));
    }

    public function edit($id)
    {
        return view('plugin-rj-commerce::admin.categories.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.commerce.categories.index')->with('success', __('Category updated successfully.'));
    }

    public function destroy($id)
    {
        return back()->with('success', __('Category deleted successfully.'));
    }
}

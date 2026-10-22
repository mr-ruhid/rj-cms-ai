<?php

namespace RjCommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return view('plugin-rj-commerce::admin.tags.index');
    }

    public function create()
    {
        return view('plugin-rj-commerce::admin.tags.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.commerce.tags.index')->with('success', __('Tag created successfully.'));
    }

    public function edit($id)
    {
        return view('plugin-rj-commerce::admin.tags.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.commerce.tags.index')->with('success', __('Tag updated successfully.'));
    }

    public function destroy($id)
    {
        return back()->with('success', __('Tag deleted successfully.'));
    }
}

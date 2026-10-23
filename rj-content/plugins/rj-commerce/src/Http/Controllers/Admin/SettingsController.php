<?php

namespace RjCommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('plugin-rj-commerce::admin.settings.index');
    }

    public function update(Request $request)
    {
        return back()->with('success', __('Commerce settings updated successfully.'));
    }

    public function apiDocs()
    {
        return view('plugin-rj-commerce::admin.api-docs');
    }
}

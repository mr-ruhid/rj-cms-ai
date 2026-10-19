<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppearanceOptionsController extends Controller
{
    public function index()
    {
        $options = rj_site_options_all();
        $defaults = rj_site_options_defaults();

        return view('admin.appearance.options', compact('options', 'defaults'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'logo_light' => 'nullable|string|max:500',
            'logo_dark' => 'nullable|string|max:500',
            'favicon' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'custom_css' => 'nullable|string|max:200000',
            'custom_js' => 'nullable|string|max:200000',
            'head_html' => 'nullable|string|max:200000',
            'body_start_html' => 'nullable|string|max:200000',
            'body_end_html' => 'nullable|string|max:200000',
            'ga4_id' => 'nullable|string|max:50',
            'gtm_id' => 'nullable|string|max:50',
            'fb_pixel_id' => 'nullable|string|max:50',
            'tracking_script' => 'nullable|string|max:200000',
        ]);

        foreach ($validated as $key => $value) {
            rj_set_site_option($key, $value ?? '');
        }

        rj_flush_site_options();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Options saved.'),
            ]);
        }

        return back()->with('success', __('Options saved.'));
    }

    public function reset(Request $request)
    {
        foreach (array_keys(rj_site_options_defaults()) as $key) {
            rj_delete_option('site_opt_' . $key);
        }

        rj_flush_site_options();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Options reset to defaults.'),
                'options' => rj_site_options_all(),
            ]);
        }

        return back()->with('success', __('Options reset to defaults.'));
    }
}
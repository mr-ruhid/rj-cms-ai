<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Knowledge;

class AboutController extends Controller
{
    public function index()
    {
        $items = Knowledge::active()->orderBy('id')->get();
        
        $categories = [
            'info' => ['label' => __('Info'), 'icon' => 'info'],
            'api' => ['label' => __('API Document'), 'icon' => 'code'],
            'theme' => ['label' => __('Theme Document'), 'icon' => 'palette'],
            'admin' => ['label' => __('Admin Document'), 'icon' => 'settings'],
            'plugin' => ['label' => __('Plugin Document'), 'icon' => 'puzzle'],
            'developer' => ['label' => __('Developer Guide'), 'icon' => 'terminal'],
            'hooks' => ['label' => __('Hooks Reference'), 'icon' => 'link'],
            'other' => ['label' => __('Other Documents'), 'icon' => 'document'],
            'credits' => ['label' => __('Credits'), 'icon' => 'star'],
            'license' => ['label' => __('License'), 'icon' => 'shield'],
        ];

        return view('admin.about.index', compact('items', 'categories'));
    }
}
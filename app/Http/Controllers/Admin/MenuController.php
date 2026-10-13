<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::withCount('items')->orderBy('name')->get();
        $locations = Menu::locations();

        return view('admin.menus.index', compact('menus', 'locations'));
    }



    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'location' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $slug = $this->uniqueSlug(Str::slug($data['name']));

        $menu = Menu::create([
            'name' => $data['name'],
            'slug' => $slug,
            'location' => $data['location'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('admin.menus.edit', $menu)
            ->with('success', __('Menu created. Add items below.'));
    }

    public function edit(Menu $menu)
    {
        $menu->load('items.children.children');

        $locations = Menu::locations();

        $pages = [];
        $posts = [];
        $categories = \App\Models\Category::with('translations')->ordered()->get();
        $tags = \App\Models\Tag::with('translations')->ordered()->get();

        return view('admin.menus.edit', compact('menu', 'locations', 'pages', 'posts', 'categories', 'tags'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'location' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $menu->update($data);

        return back()->with('success', __('Menu updated.'));
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', __('Menu deleted.'));
    }

    public function addLocation(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9_\-]+$/'],
            'label' => ['required', 'string', 'max:100'],
        ]);

        $current = rj_get_option('menu_custom_locations');
        $current = $current ? json_decode($current, true) : [];

        $current[$data['key']] = $data['label'];

        rj_set_option('menu_custom_locations', json_encode($current), 'menus');

        return back()->with('success', __('Location added.'));
    }

    protected function uniqueSlug(string $slug): string
    {
        $base = $slug ?: 'menu';
        $slug = $base;
        $i = 1;

        while (Menu::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;

            if ($i > 100) {
                return $base . '-' . Str::random(6);
            }
        }

        return $slug;
    }
}
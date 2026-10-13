<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function update(Request $request, Menu $menu, MenuItem $item)
    {
        if ($item->menu_id !== $menu->id) {
            abort(404);
        }

        $data = $request->validate([
            'label' => ['required', 'string', 'max:200'],
            'translations' => ['nullable', 'array'],
            'translations.*' => ['nullable', 'string', 'max:200'],
            'url' => ['nullable', 'string', 'max:500'],
            'target' => ['nullable', 'in:_self,_blank'],
            'css_class' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($item->type === 'custom') {
            $translations = !empty($data['translations'])
                ? array_filter($data['translations'], fn($v) => !empty($v))
                : null;
            $data['translations'] = !empty($translations) ? $translations : null;
        } else {
            unset($data['translations']);
        }

        $item->update($data);

        return back()->with('success', __('Menu item updated.'));
    }

    public function update(Request $request, Menu $menu, MenuItem $item)
    {
        if ($item->menu_id !== $menu->id) {
            abort(404);
        }

        $data = $request->validate([
            'label' => ['required', 'string', 'max:200'],
            'url' => ['nullable', 'string', 'max:500'],
            'target' => ['nullable', 'in:_self,_blank'],
            'css_class' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $item->update($data);

        return back()->with('success', __('Menu item updated.'));
    }

    public function destroy(Menu $menu, MenuItem $item)
    {
        if ($item->menu_id !== $menu->id) {
            abort(404);
        }

        $item->children()->update(['parent_id' => $item->parent_id]);
        $item->delete();

        return back()->with('success', __('Menu item deleted.'));
    }

    public function reorder(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer'],
            'items.*.parent_id' => ['nullable', 'integer'],
            'items.*.sort_order' => ['required', 'integer'],
        ]);

        foreach ($data['items'] as $item) {
            MenuItem::where('id', $item['id'])
                ->where('menu_id', $menu->id)
                ->update([
                    'parent_id' => $item['parent_id'] ?? null,
                    'sort_order' => $item['sort_order'],
                ]);
        }

        return response()->json(['success' => true]);
    }

    public function options(Request $request)
    {
        $type = $request->input('type');

        $result = [];

        if ($type === 'category') {
            $categories = \App\Models\Category::with('translations')->ordered()->get();
            foreach ($categories as $c) {
                $result[] = ['id' => $c->id, 'label' => $c->name, 'url' => '/category/' . $c->slug];
            }
        } elseif ($type === 'tag') {
            $tags = \App\Models\Tag::with('translations')->ordered()->get();
            foreach ($tags as $t) {
                $result[] = ['id' => $t->id, 'label' => $t->name, 'url' => '/tag/' . $t->slug];
            }
        } elseif ($type === 'page') {
            $result = [];
        } elseif ($type === 'post') {
            $result = [];
        }

        return response()->json($result);
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function store(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'type' => ['required', 'in:custom,page,post,category,tag'],
            'label' => ['nullable', 'string', 'max:200'], // nullable etdik
            'translations' => ['nullable', 'array'],
            'translations.*' => ['nullable', 'string', 'max:200'],
            'url' => ['nullable', 'string', 'max:500'],
            'object_id' => ['nullable', 'integer'],
            'parent_id' => ['nullable', 'integer', 'exists:rj_menu_items,id'],
            'target' => ['nullable', 'in:_self,_blank'],
            'css_class' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
        ]);

        if ($data['type'] === 'custom' && empty($data['url'])) {
            return back()->with('error', __('URL is required for custom links.'))->withInput();
        }

        // Label boşdursa, obyektin adını götürürük
        $label = $data['label'] ?? '';
        if (empty($label) && !empty($data['object_id'])) {
            if ($data['type'] === 'page' || $data['type'] === 'post') {
                $label = Post::find($data['object_id'])?->title;
            } elseif ($data['type'] === 'category') {
                $label = \App\Models\Category::find($data['object_id'])?->name;
            } elseif ($data['type'] === 'tag') {
                $label = \App\Models\Tag::find($data['object_id'])?->name;
            }
        }
        if (empty($label)) {
            $label = __('Item');
        }

        $translations = null;
        if ($data['type'] === 'custom' && !empty($data['translations'])) {
            $translations = array_filter($data['translations'], fn($v) => !empty($v));
            $translations = !empty($translations) ? $translations : null;
        }

        $sortOrder = MenuItem::where('menu_id', $menu->id)
            ->where('parent_id', $data['parent_id'] ?? null)
            ->max('sort_order') + 1;

        MenuItem::create([
            'menu_id' => $menu->id,
            'parent_id' => $data['parent_id'] ?? null,
            'type' => $data['type'],
            'label' => $label,
            'translations' => $translations,
            'url' => $data['url'] ?? null,
            'target' => $data['target'] ?? '_self',
            'object_id' => $data['object_id'] ?? null,
            'object_type' => $data['type'] !== 'custom' ? $data['type'] : null,
            'sort_order' => $sortOrder,
            'css_class' => $data['css_class'] ?? null,
            'icon' => $data['icon'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', __('Menu item added.'));
    }

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
            $pages = Post::with('translations')
                ->where('type', 'page')
                ->where('status', 'published')
                ->orderByDesc('created_at')
                ->get();

            foreach ($pages as $p) {
                $result[] = ['id' => $p->id, 'label' => $p->title, 'url' => '/' . $p->slug];
            }
        } elseif ($type === 'post') {
            $posts = Post::with('translations')
                ->where('type', 'post')
                ->where('status', 'published')
                ->orderByDesc('created_at')
                ->get();

            foreach ($posts as $p) {
                $result[] = ['id' => $p->id, 'label' => $p->title, 'url' => '/blog/' . $p->slug];
            }
        }

        return response()->json($result);
    }
}
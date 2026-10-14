<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalWidget;
use Illuminate\Http\Request;

class GlobalWidgetController extends Controller
{
    public function index(Request $request)
    {
        $query = GlobalWidget::with('author')->orderByDesc('updated_at');

        if ($type = $request->input('type')) {
            $query->ofType($type);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $widgets = $query->paginate(24)->withQueryString();

        $types = GlobalWidget::query()
            ->select('type')
            ->distinct()
            ->pluck('type')
            ->toArray();

        return view('admin.global-widgets.index', compact('widgets', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:100',
            'settings' => 'nullable|array',
            'children' => 'nullable|array',
        ]);

        $widget = GlobalWidget::create([
            'name' => $validated['name'],
            'slug' => GlobalWidget::uniqueSlug($validated['name']),
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'settings' => $validated['settings'] ?? [],
            'children' => $validated['children'] ?? [],
            'status' => 'active',
            'author_id' => auth()->id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'widget' => [
                    'id' => $widget->id,
                    'name' => $widget->name,
                    'slug' => $widget->slug,
                    'type' => $widget->type,
                ],
            ]);
        }

        return redirect()
            ->route('admin.global-widgets.index')
            ->with('success', __('Global widget created.'));
    }

    public function edit(GlobalWidget $globalWidget)
    {
        return view('admin.global-widgets.edit', ['widget' => $globalWidget]);
    }

    public function update(Request $request, GlobalWidget $globalWidget)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
            'children' => 'nullable|array',
            'status' => 'nullable|in:active,inactive',
        ]);

        $globalWidget->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'settings' => $validated['settings'] ?? [],
            'children' => $validated['children'] ?? [],
            'status' => $validated['status'] ?? $globalWidget->status,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Global widget saved.'));
    }

    public function destroy(GlobalWidget $globalWidget)
    {
        $globalWidget->delete();

        return redirect()
            ->route('admin.global-widgets.index')
            ->with('success', __('Global widget deleted.'));
    }

    public function duplicate(GlobalWidget $globalWidget)
    {
        $copy = $globalWidget->replicate(['slug']);
        $copy->name = $globalWidget->name . ' (Copy)';
        $copy->slug = GlobalWidget::uniqueSlug($copy->name);
        $copy->author_id = auth()->id();
        $copy->save();

        return redirect()
            ->route('admin.global-widgets.index')
            ->with('success', __('Global widget duplicated.'));
    }

    public function options()
    {
        $widgets = GlobalWidget::active()
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'slug']);

        return response()->json([
            'widgets' => $widgets->map(fn($w) => [
                'id' => $w->id,
                'name' => $w->name,
                'slug' => $w->slug,
                'type' => $w->type,
            ]),
        ]);
    }
}
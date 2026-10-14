<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BuilderController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'header');

        $allowedTypes = array_keys(Template::types());

        if (!in_array($type, $allowedTypes, true)) {
            $type = 'header';
        }

        $query = Template::with('author')->type($type)->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $templates = $query->paginate(20)->withQueryString();

        $counts = [];
        foreach ($allowedTypes as $t) {
            $counts[$t] = Template::type($t)->count();
        }

        return view('admin.rjbuilder.index', compact('templates', 'type', 'counts'));
    }

    public function create(Request $request)
    {
        $type = $request->input('type', 'header');

        if (!array_key_exists($type, Template::types())) {
            $type = 'header';
        }

        return view('admin.rjbuilder.create', compact('type'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'type' => ['required', 'string', 'in:header,footer,page,popup,block'],
        ]);

        $slug = $this->uniqueSlug(Str::slug($data['name']));

        $template = Template::create([
            'name' => $data['name'],
            'slug' => $slug,
            'type' => $data['type'],
            'content' => ['blocks' => []],
            'status' => 'draft',
            'author_id' => $request->user()->id,
        ]);

        return redirect()->route('admin.rjbuilder.edit', $template)
            ->with('success', __('Template created. Start building!'));
    }

    public function edit(Template $template)
    {
        return view('admin.rjbuilder.edit', compact('template'));
    }

    public function build(Template $template)
    {
        return view('rjbuilder.edit', compact('template'));
    }

    public function update(Request $request, Template $template)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'content' => ['nullable', 'array'],
            'status' => ['nullable', 'in:draft,published'],
        ]);

        $template->update([
            'name' => $data['name'],
            'content' => $data['content'] ?? ['blocks' => []],
            'status' => $data['status'] ?? $template->status,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Template saved.'),
                'blocks_count' => count($data['content']['blocks'] ?? []),
            ]);
        }

        return back()->with('success', __('Template saved.'));
    }

    public function destroy(Template $template)
    {
        if ($template->is_default) {
            return back()->with('error', __('Cannot delete the default template. Set another as default first.'));
        }

        $type = $template->type;
        $template->delete();

        return redirect()->route('admin.rjbuilder.index', ['type' => $type])
            ->with('success', __('Template deleted.'));
    }

    public function duplicate(Template $template)
    {
        $new = $template->replicate();
        $new->name = $template->name . ' (Copy)';
        $new->slug = $this->uniqueSlug(Str::slug($new->name));
        $new->status = 'draft';
        $new->is_default = false;
        $new->save();

        return back()->with('success', __('Template duplicated.'));
    }

    public function setDefault(Template $template)
    {
        Template::type($template->type)->update(['is_default' => false]);

        $template->update(['is_default' => true]);

        return back()->with('success', __('Default :type set.', ['type' => strtolower($template->typeLabel())]));
    }

        public function updateConditions(Request $request, Template $template)
    {
        $validated = $request->validate([
            'conditions' => 'nullable|array',
            'conditions.match' => 'nullable|in:all,any',
            'conditions.rules' => 'nullable|array',
            'conditions.rules.*.type' => 'required|string',
            'conditions.rules.*.value' => 'nullable|string',
        ]);

        $conditions = $validated['conditions'] ?? [];

        if (empty($conditions['rules'])) {
            $conditions = null;
        }

        $template->update([
            'conditions' => $conditions,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'conditions' => $template->conditions,
            ]);
        }

        return back()->with('success', __('Conditions saved.'));
    }

        public function updatePopupSettings(Request $request, Template $template)
    {
        if ($template->type !== 'popup') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Not a popup template.'], 422);
            }
            return back();
        }

        $validated = $request->validate([
            'settings' => 'nullable|array',
            'settings.trigger' => 'nullable|in:page_load,scroll_percent,after_inactivity,click_element,exit_intent',
            'settings.trigger_value' => 'nullable|string',
            'settings.delay' => 'nullable|integer|min:0|max:60',
            'settings.frequency' => 'nullable|in:always,once_per_session,once_per_day,once_per_week,once',
            'settings.position' => 'nullable|in:center,top,bottom,top-left,top-right,bottom-left,bottom-right',
            'settings.width' => 'nullable|in:sm,md,lg,xl,full',
            'settings.overlay' => 'nullable|boolean',
            'settings.overlay_color' => 'nullable|string',
            'settings.overlay_opacity' => 'nullable|integer|min:0|max:100',
            'settings.close_on_overlay' => 'nullable|boolean',
            'settings.close_button' => 'nullable|boolean',
            'settings.animation' => 'nullable|in:none,fadeIn,fadeInUp,fadeInDown,zoomIn,slideInLeft,slideInRight',
            'settings.animation_duration' => 'nullable|in:sm,md,lg,xl',
            'settings.auto_close' => 'nullable|integer|min:0|max:120',
        ]);

        $settings = $validated['settings'] ?? [];

        $template->update([
            'settings' => $settings,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'settings' => $template->popupSettings(),
            ]);
        }

        return back()->with('success', __('Popup settings saved.'));
    }

    protected function uniqueSlug(string $slug): string
    {
        $base = $slug ?: 'template';
        $slug = $base;
        $i = 1;

        while (Template::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;

            if ($i > 100) {
                return $base . '-' . Str::random(6);
            }
        }

        return $slug;
    }
}
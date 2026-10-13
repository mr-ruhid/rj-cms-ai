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
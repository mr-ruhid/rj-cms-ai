<?php

namespace RjPlugins\FormBuilder\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RjPlugins\FormBuilder\Models\Form;

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::withCount('submissions')->orderByDesc('created_at')->get();

        return view('plugin-form-builder::admin.index', compact('forms'));
    }

    public function create()
    {
        return view('plugin-form-builder::admin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['slug'] = Form::uniqueSlug($validated['slug'] ?: $validated['name']);
        $validated['status'] = $validated['status'] ?? 'active';

        $form = Form::create($validated);

        return redirect()
            ->route('admin.plugin.forms.edit', $form)
            ->with('success', __('Form created.'));
    }

    public function edit(Form $form)
    {
        $form->load('fields');

        return view('plugin-form-builder::admin.edit', compact('form'));
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['slug'] = Form::uniqueSlug($validated['slug'] ?: $validated['name'], $form->id);

        $form->update($validated);

        return back()->with('success', __('Form saved.'));
    }

    public function destroy(Form $form)
    {
        $form->delete();

        return redirect()
            ->route('admin.plugin.forms.index')
            ->with('success', __('Form deleted.'));
    }
}

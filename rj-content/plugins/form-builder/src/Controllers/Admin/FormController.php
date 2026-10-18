<?php

namespace RjPlugins\FormBuilder\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RjPlugins\FormBuilder\Models\Form;
use RjPlugins\FormBuilder\Models\FormField;

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
            'fields' => 'nullable|string',
        ]);

        $validated['slug'] = Form::uniqueSlug($validated['slug'] ?: $validated['name'], $form->id);

        $form->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        if (isset($validated['fields'])) {
            $fieldsData = json_decode($validated['fields'], true);

            if (is_array($fieldsData)) {
                $form->fields()->delete();

                foreach ($fieldsData as $index => $field) {
                    $fieldName = !empty($field['name']) ? Str::slug($field['name'], '_') : 'field_' . Str::random(5);
                    $fieldLabel = !empty($field['label']) ? $field['label'] : ucfirst(str_replace('_', ' ', $fieldName));

                    FormField::create([
                        'form_id' => $form->id,
                        'type' => $field['type'] ?? 'text',
                        'name' => $fieldName,
                        'label' => $fieldLabel,
                        'placeholder' => $field['placeholder'] ?? null,
                        'is_required' => filter_var($field['is_required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'sort_order' => $index,
                        'options' => $field['options'] ?? null,
                        'settings' => null,
                    ]);
                }
            }
        }

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

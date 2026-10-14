<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormField;
use App\Services\FormService;
use Illuminate\Http\Request;

class FormController extends Controller
{
    protected FormService $service;

    public function __construct(FormService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $forms = Form::withCount(['submissions', 'fields'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.forms.index', compact('forms'));
    }

    public function create()
    {
        return view('admin.forms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $form = Form::create([
            'name' => $validated['name'],
            'slug' => $this->service->uniqueSlug($validated['name']),
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'author_id' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.forms.edit', $form)
            ->with('success', __('Form created.'));
    }

    public function edit(Form $form)
    {
        $form->load(['fields' => fn($q) => $q->orderBy('sort_order')]);

        return view('admin.forms.edit', compact('form'));
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'settings' => 'nullable|array',
            'notifications' => 'nullable|array',
            'actions' => 'nullable|array',
            'fields' => 'nullable|array',
            'fields.*.id' => 'nullable|integer',
            'fields.*.name' => 'required|string|max:100',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|string',
            'fields.*.required' => 'nullable|boolean',
            'fields.*.placeholder' => 'nullable|string',
            'fields.*.help_text' => 'nullable|string',
            'fields.*.default_value' => 'nullable|string',
            'fields.*.options' => 'nullable|array',
            'fields.*.validation' => 'nullable|array',
            'fields.*.sort_order' => 'nullable|integer',
        ]);

        $form->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? $form->status,
            'settings' => $validated['settings'] ?? [],
            'notifications' => $validated['notifications'] ?? [],
            'actions' => $validated['actions'] ?? [],
        ]);

        $this->syncFields($form, $validated['fields'] ?? []);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Form saved.'));
    }

    public function destroy(Form $form)
    {
        $form->delete();

        return redirect()
            ->route('admin.forms.index')
            ->with('success', __('Form deleted.'));
    }

    public function duplicate(Form $form)
    {
        $copy = $form->replicate(['slug']);
        $copy->name = $form->name . ' (Copy)';
        $copy->slug = $this->service->uniqueSlug($copy->name);
        $copy->save();

        foreach ($form->fields as $field) {
            $newField = $field->replicate();
            $newField->form_id = $copy->id;
            $newField->save();
        }

        return redirect()
            ->route('admin.forms.edit', $copy)
            ->with('success', __('Form duplicated.'));
    }

    protected function syncFields(Form $form, array $fields): void
    {
        $keepIds = [];

        foreach ($fields as $index => $fieldData) {
            $data = [
                'form_id' => $form->id,
                'name' => $fieldData['name'],
                'label' => $fieldData['label'],
                'type' => $fieldData['type'],
                'placeholder' => $fieldData['placeholder'] ?? null,
                'help_text' => $fieldData['help_text'] ?? null,
                'default_value' => $fieldData['default_value'] ?? null,
                'options' => $fieldData['options'] ?? [],
                'validation' => $fieldData['validation'] ?? [],
                'required' => !empty($fieldData['required']),
                'sort_order' => $fieldData['sort_order'] ?? $index,
            ];

            if (!empty($fieldData['id'])) {
                $field = FormField::where('form_id', $form->id)->find($fieldData['id']);
                if ($field) {
                    $field->update($data);
                    $keepIds[] = $field->id;
                    continue;
                }
            }

            $new = FormField::create($data);
            $keepIds[] = $new->id;
        }

        FormField::where('form_id', $form->id)
            ->whereNotIn('id', $keepIds)
            ->delete();
    }
}
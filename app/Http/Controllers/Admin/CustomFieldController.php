<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\PostType;
use App\Models\Taxonomy;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomField::with('author')->ordered();

        if ($location = $request->input('location')) {
            $query->where('location', $location);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('key', 'like', '%' . $search . '%');
            });
        }

        $fields = $query->paginate(30)->withQueryString();

        $types = CustomField::types();
        $typeGroups = CustomField::typeGroups();

        $counts = [
            'all' => CustomField::count(),
            'post' => CustomField::where('location', 'post')->count(),
            'taxonomy' => CustomField::where('location', 'taxonomy')->count(),
            'user' => CustomField::where('location', 'user')->count(),
        ];

        return view('admin.custom-fields.index', compact('fields', 'types', 'typeGroups', 'counts'));
    }

    public function create(Request $request)
    {
        $field = new CustomField([
            'type' => $request->input('type', 'text'),
            'location' => $request->input('location', 'post'),
            'is_required' => false,
            'is_repeatable' => false,
            'status' => 'active',
        ]);

        $types = CustomField::types();
        $typeGroups = CustomField::typeGroups();
        $postTypes = PostType::active()->ordered()->get();
        $taxonomies = Taxonomy::active()->orderBy('name')->get();

        return view('admin.custom-fields.create', compact('field', 'types', 'typeGroups', 'postTypes', 'taxonomies'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $field = CustomField::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? CustomField::uniqueSlug($validated['name']),
            'key' => $validated['key'] ?? CustomField::uniqueKey($validated['name']),
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'placeholder' => $validated['placeholder'] ?? null,
            'default_value' => $validated['default_value'] ?? null,
            'options' => $validated['options'] ?? [],
            'validation' => $validated['validation'] ?? [],
            'settings' => $validated['settings'] ?? [],
            'post_types' => $validated['post_types'] ?? [],
            'taxonomies' => $validated['taxonomies'] ?? [],
            'is_required' => $validated['is_required'] ?? false,
            'is_repeatable' => $validated['is_repeatable'] ?? false,
            'location' => $validated['location'] ?? 'post',
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $validated['status'] ?? 'active',
            'author_id' => auth()->id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'field' => $field,
            ]);
        }

        return redirect()
            ->route('admin.custom-fields.index')
            ->with('success', __('Custom field created.'));
    }

    public function edit(CustomField $customField)
    {
        $field = $customField;

        $types = CustomField::types();
        $typeGroups = CustomField::typeGroups();
        $postTypes = PostType::active()->ordered()->get();
        $taxonomies = Taxonomy::active()->orderBy('name')->get();

        return view('admin.custom-fields.edit', compact('field', 'types', 'typeGroups', 'postTypes', 'taxonomies'));
    }

    public function update(Request $request, CustomField $customField)
    {
        $validated = $this->validateData($request, $customField);

        $customField->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $customField->slug,
            'key' => $validated['key'] ?? $customField->key,
            'type' => $validated['type'],
            'description' => $validated['description'] ?? $customField->description,
            'placeholder' => $validated['placeholder'] ?? $customField->placeholder,
            'default_value' => $validated['default_value'] ?? $customField->default_value,
            'options' => $validated['options'] ?? $customField->options,
            'validation' => $validated['validation'] ?? $customField->validation,
            'settings' => $validated['settings'] ?? $customField->settings,
            'post_types' => $validated['post_types'] ?? $customField->post_types,
            'taxonomies' => $validated['taxonomies'] ?? $customField->taxonomies,
            'is_required' => $validated['is_required'] ?? $customField->is_required,
            'is_repeatable' => $validated['is_repeatable'] ?? $customField->is_repeatable,
            'location' => $validated['location'] ?? $customField->location,
            'sort_order' => $validated['sort_order'] ?? $customField->sort_order,
            'status' => $validated['status'] ?? $customField->status,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'field' => $customField]);
        }

        return back()->with('success', __('Custom field saved.'));
    }

    public function destroy(CustomField $customField)
    {
        $customField->values()->delete();
        $customField->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.custom-fields.index')
            ->with('success', __('Custom field deleted.'));
    }

    public function duplicate(CustomField $customField)
    {
        $copy = $customField->replicate(['slug', 'key']);
        $copy->name = $customField->name . ' (Copy)';
        $copy->slug = CustomField::uniqueSlug($copy->name);
        $copy->key = CustomField::uniqueKey($copy->name);
        $copy->author_id = auth()->id();
        $copy->save();

        return redirect()
            ->route('admin.custom-fields.edit', $copy)
            ->with('success', __('Custom field duplicated.'));
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:rj_custom_fields,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            CustomField::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Order updated.'));
    }

    public function options(Request $request)
    {
        $location = $request->input('location');
        $postType = $request->input('post_type');
        $taxonomy = $request->input('taxonomy');

        $query = CustomField::active()->ordered();

        if ($location) {
            $query->where('location', $location);
        }

        $fields = $query->get();

        $filtered = $fields->filter(function ($field) use ($postType, $taxonomy) {
            if ($postType && $field->location === 'post') {
                return $field->appliesToPostType($postType);
            }

            if ($taxonomy && $field->location === 'taxonomy') {
                return $field->appliesToTaxonomy($taxonomy);
            }

            return true;
        });

        return response()->json([
            'fields' => $filtered->map(fn($f) => [
                'id' => $f->id,
                'name' => $f->name,
                'key' => $f->key,
                'type' => $f->type,
                'location' => $f->location,
                'options' => $f->options(),
            ])->values(),
        ]);
    }

    protected function validateData(Request $request, ?CustomField $field = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|alpha_dash|unique:rj_custom_fields,slug' . ($field ? ',' . $field->id : ''),
            'key' => 'nullable|string|max:255|alpha_dash|unique:rj_custom_fields,key' . ($field ? ',' . $field->id : ''),
            'type' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'placeholder' => 'nullable|string|max:255',
            'default_value' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*.value' => 'required_with:options|string',
            'options.*.label' => 'required_with:options|string',
            'validation' => 'nullable|array',
            'settings' => 'nullable|array',
            'post_types' => 'nullable|array',
            'post_types.*' => 'string',
            'taxonomies' => 'nullable|array',
            'taxonomies.*' => 'string',
            'is_required' => 'nullable|boolean',
            'is_repeatable' => 'nullable|boolean',
            'location' => 'nullable|in:post,taxonomy,user,option',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive',
        ]);
    }
}
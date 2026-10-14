<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::withCount('roles')->ordered();

        if ($group = $request->input('group')) {
            $query->where('group', $group);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        $permissions = $query->get();

        $grouped = $permissions->groupBy(fn($p) => $p->group ?: 'general');

        $groupLabels = Permission::groupLabels();

        $counts = [
            'all' => Permission::count(),
            'groups' => Permission::query()
                ->select('group')
                ->distinct()
                ->pluck('group')
                ->filter()
                ->count(),
        ];

        $roleCount = Role::count();

        return view('admin.permissions.index', compact('grouped', 'groupLabels', 'counts', 'roleCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|alpha_dash|unique:rj_permissions,slug',
            'group' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'group' => $validated['group'] ?? 'general',
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'permission' => $permission]);
        }

        return back()->with('success', __('Permission created.'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|alpha_dash|unique:rj_permissions,slug,' . $permission->id,
            'group' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        $permission->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'permission' => $permission]);
        }

        return back()->with('success', __('Permission saved.'));
    }

    public function destroy(Permission $permission)
    {
        $permission->roles()->detach();
        $permission->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Permission deleted.'));
    }

    public function roles(Permission $permission)
    {
        $permission->load('roles');

        $allRoles = Role::orderBy('name')->get();

        $assignedIds = $permission->roles->pluck('id')->toArray();

        return response()->json([
            'permission' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'slug' => $permission->slug,
            ],
            'roles' => $allRoles->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'slug' => $r->slug,
                'assigned' => in_array($r->id, $assignedIds, true),
            ]),
        ]);
    }

    public function syncRoles(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'integer|exists:rj_roles,id',
        ]);

        $permission->roles()->sync($validated['role_ids']);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'count' => count($validated['role_ids'])]);
        }

        return back()->with('success', __('Roles updated.'));
    }

    public function sync(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|integer|exists:rj_roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:rj_permissions,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);

        if ($role->slug === 'super-admin') {
            $role->syncPermissions(Permission::pluck('id')->toArray());
        } else {
            $role->syncPermissions($validated['permissions'] ?? []);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Permissions updated.'));
    }

    public function groupStore(Request $request)
    {
        $validated = $request->validate([
            'group' => 'required|string|max:50',
            'permissions' => 'required|array|min:1',
            'permissions.*.name' => 'required|string|max:255',
            'permissions.*.slug' => 'required|string|max:100|alpha_dash',
        ]);

        $created = 0;

        foreach ($validated['permissions'] as $item) {
            if (Permission::where('slug', $item['slug'])->exists()) {
                continue;
            }

            Permission::create([
                'name' => $item['name'],
                'slug' => $item['slug'],
                'group' => $validated['group'],
            ]);

            $created++;
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'created' => $created]);
        }

        return back()->with('success', __(':n permission(s) created.', ['n' => $created]));
    }
}
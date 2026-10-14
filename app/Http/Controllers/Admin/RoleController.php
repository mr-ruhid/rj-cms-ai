<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->withCount('users')
            ->ordered()
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::grouped();
        $groupLabels = Permission::groupLabels();

        return view('admin.roles.create', compact('permissions', 'groupLabels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|alpha_dash|unique:rj_roles,slug',
            'description' => 'nullable|string|max:1000',
            'level' => 'nullable|integer|min:0|max:100',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:rj_permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Role::uniqueSlug($validated['name']),
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'] ?? 0,
            'is_system' => false,
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'role' => $role->load('permissions')]);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role created.'));
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);

        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $role->load('permissions');

        $permissions = Permission::grouped();
        $groupLabels = Permission::groupLabels();
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'groupLabels', 'rolePermissionIds'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|alpha_dash|unique:rj_roles,slug,' . $role->id,
            'description' => 'nullable|string|max:1000',
            'level' => 'nullable|integer|min:0|max:100',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:rj_permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $role->slug,
            'description' => $validated['description'] ?? $role->description,
            'level' => $validated['level'] ?? $role->level,
        ]);

        if (array_key_exists('permissions', $validated)) {
            if ($role->slug === 'super-admin') {
                $role->syncPermissions(Permission::pluck('id')->toArray());
            } else {
                $role->syncPermissions($validated['permissions'] ?? []);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'role' => $role->fresh('permissions')]);
        }

        return back()->with('success', __('Role saved.'));
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            $message = __('System roles cannot be deleted.');

            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $role->users()->detach();
        $role->permissions()->detach();
        $role->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role deleted.'));
    }

    public function duplicate(Role $role)
    {
        $copy = $role->replicate(['slug']);
        $copy->name = $role->name . ' (Copy)';
        $copy->slug = Role::uniqueSlug($copy->name);
        $copy->is_system = false;
        $copy->save();

        $copy->syncPermissions($role->permissions->pluck('id')->toArray());

        return redirect()
            ->route('admin.roles.edit', $copy)
            ->with('success', __('Role duplicated.'));
    }

    public function assignUsers(Request $request, Role $role)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $role->users()->sync($validated['user_ids']);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'count' => count($validated['user_ids'])]);
        }

        return back()->with('success', __('Users assigned to role.'));
    }

    public function users(Role $role)
    {
        $assignedIds = $role->users()->pluck('users.id')->toArray();

        $allUsers = User::orderBy('name')->get();

        return response()->json([
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
            ],
            'assigned_ids' => $assignedIds,
            'users' => $allUsers->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'assigned' => in_array($u->id, $assignedIds, true),
            ]),
        ]);
    }
}
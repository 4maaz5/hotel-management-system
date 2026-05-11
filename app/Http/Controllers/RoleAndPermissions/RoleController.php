<?php

namespace App\Http\Controllers\RoleAndPermissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        // Get all roles with their permissions
        $roles = Role::with('permissions')->get();

        return view('Admin.Backend.RoleandPermissions.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();

        return view('Admin.Backend.RoleandPermissions.create', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'role' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['role']]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('dashboard.setting.role.index')->with('success', __('messages.role_created_successfully'));
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);

        // Get all permissions
        $permissions = Permission::all();

        return view('Admin.Backend.RoleandPermissions.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|string|unique:roles,name,'.$role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Update role name
        $role->name = $validated['role'];
        $role->save();

        // Sync permissions
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('dashboard.setting.role.index')
            ->with('success', __('messages.role_updated_successfully'));
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Prevent deleting Super Admin
        if ($role->name === 'Super Admin') {
            return redirect()->back()->with('error', 'You cannot delete the Super Admin role.');
        }

        // Delete role
        $role->delete();

        return redirect()->route('dashboard.setting.role.index')
            ->with('success', __('messages.role_deleted_successfully'));
    }
}

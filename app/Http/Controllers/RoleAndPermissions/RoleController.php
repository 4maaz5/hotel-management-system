<?php

namespace App\Http\Controllers\RoleAndPermissions;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = $this->mutableRolesForUser(Role::with('permissions'), auth()->user())->get();

        return view('Admin.Backend.RoleandPermissions.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();

        return view('Admin.Backend.RoleandPermissions.create', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'role' => [
                'required',
                'string',
                Rule::unique('roles', 'name')->where(fn ($query) => $this->roleTenantConstraint($query, $request->user())),
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create([
            'company_id' => $this->roleCompanyId($request->user()),
            'name' => $validated['role'],
            'guard_name' => 'web',
        ]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('dashboard.setting.role.index')->with('success', __('messages.role_created_successfully'));
    }

    public function edit($id)
    {
        $role = $this->mutableRolesForUser(Role::query(), auth()->user())->findOrFail($id);

        // Get all permissions
        $permissions = Permission::all();

        return view('Admin.Backend.RoleandPermissions.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $role = $this->mutableRolesForUser(Role::query(), $request->user())->findOrFail($id);

        $validated = $request->validate([
            'role' => [
                'required',
                'string',
                Rule::unique('roles', 'name')
                    ->ignore($role->id)
                    ->where(fn ($query) => $query->where('company_id', $role->company_id)),
            ],
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
        $role = $this->mutableRolesForUser(Role::query(), auth()->user())->findOrFail($id);

        // Prevent deleting Super Admin
        if ($role->name === 'Super Admin') {
            return redirect()->back()->with('error', 'You cannot delete the Super Admin role.');
        }

        // Delete role
        $role->delete();

        return redirect()->route('dashboard.setting.role.index')
            ->with('success', __('messages.role_deleted_successfully'));
    }

    private function mutableRolesForUser($query, $user)
    {
        if ($user?->isSuperAdmin()) {
            return $query;
        }

        return $query->where('company_id', $user?->company_id);
    }

    private function roleTenantConstraint($query, $user)
    {
        return $query->where('company_id', $this->roleCompanyId($user));
    }

    private function roleCompanyId($user): ?int
    {
        return $user?->isSuperAdmin() ? null : $user?->company_id;
    }
}

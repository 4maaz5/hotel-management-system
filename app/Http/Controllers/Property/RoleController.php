<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = $this->mutableRolesForUser(Role::withCount('permissions'), $request->user())

            ->when($request->filled('name'), function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->name.'%');
            })

            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.role.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->groupPermissions();

        return view('admin.role.create', compact('permissions'));
    }

    public function view(Role $role)
    {
        $this->authorizeTenantRole($role);

        $permissions = $this->groupPermissions();

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.role.view', compact(
            'role',
            'permissions',
            'rolePermissions'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('roles', 'name')->where(fn ($query) => $this->roleTenantConstraint($query, $request->user())),
            ],
            'description' => 'nullable|string',
            'access_type' => 'required|in:full,limited',
            'permissions' => 'array',
        ]);

        DB::transaction(function () use ($request) {

            //  Create role
            $role = Role::create([
                'company_id' => $this->roleCompanyId($request->user()),
                'name' => $request->name,
                'guard_name' => 'web',
                'description' => $request->description,
                'status' => 'ACTIVE',
            ]);

            //  Assign permissions
            if ($request->access_type === 'full') {

                // FULL ACCESS
                $role->syncPermissions(Permission::all());

            } else {

                // LIMITED ACCESS
                $role->syncPermissions($request->permissions ?? []);
            }
        });

        return redirect()
            ->route('setup-sidebar.property-role.index')
            ->with('success', __('messages.role_created_successfully'));
    }

    public function edit(Role $role)
    {
        $this->authorizeTenantRole($role);

        $permissions = $this->groupPermissions();

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.role.edit', compact(
            'role',
            'permissions',
            'rolePermissions'
        ));
    }

    public function copy(Role $role)
    {
        $this->authorizeTenantRole($role);

        $permissions = $this->groupPermissions();

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.role.copy', compact(
            'role',
            'permissions',
            'rolePermissions'
        ));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorizeTenantRole($role);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')
                    ->ignore($role->id)
                    ->where(fn ($query) => $query->where('company_id', $role->company_id)),
            ],
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->has('status') && $request->status === 'ACTIVE' ? 'ACTIVE' : 'INACTIVE',
        ]);

        if ($request->access_type === 'full') {
            $role->syncPermissions(Permission::all());
        } else {
            $role->syncPermissions($request->permissions ?? []);
        }

        return redirect()
            ->route('setup-sidebar.property-role.index')
            ->with('success', __('messages.role_updated_successfully'));
    }

    public function delete(Role $role)
    {
        $this->authorizeTenantRole($role);

        $role->delete();

        return redirect()->route('setup-sidebar.property-role.index')->with('danger', __('messages.role_deleted_successfully'));
    }

    private function groupPermissions()
    {
        return Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy(function (Permission $permission): string {
                $name = $permission->name;
                if (str_contains($name, '.')) {
                    return explode('.', $name, 2)[0];
                }
                $parts = explode('_', $name);
                return lcfirst($parts[1] ?? 'other');
            });
    }

    private function mutableRolesForUser($query, $user)
    {
        if ($user?->isSuperAdmin()) {
            return $query;
        }

        return $query->where('company_id', $user?->company_id);
    }

    private function authorizeTenantRole(Role $role): void
    {
        abort_unless($this->mutableRolesForUser(Role::whereKey($role->id), auth()->user())->exists(), 404);
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

<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::withCount('permissions')

            ->when($request->filled('name'), function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->name.'%');
            })

            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })

            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.role.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        return view('admin.role.create', compact('permissions'));
    }

    public function view(Role $role)
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

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
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'access_type' => 'required|in:full,limited',
            'permissions' => 'array',
        ]);

        DB::transaction(function () use ($request) {

            //  Create role
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web',
                'description' => $request->description,
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
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.role.edit', compact(
            'role',
            'permissions',
            'rolePermissions'
        ));
    }

    public function copy(Role $role)
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.role.copy', compact(
            'role',
            'permissions',
            'rolePermissions'
        ));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:100',
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
        $role->delete();

        return redirect()->route('setup-sidebar.property-role.index')->with('danger', __('messages.role_deleted_successfully'));
    }
}

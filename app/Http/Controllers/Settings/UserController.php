<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();

        $users = $this->scopeUsersForUser(User::with('branch'), $user)->get();
        $branches = $this->scopeBranchesForUser(Branch::query(), $user)->get();
        $roles = Role::query()
            ->visibleToUser($user)
            ->when(! $this->isSuperAdmin($user), fn ($query) => $query->where('name', '!=', 'super_admin'))
            ->get();

        return view('Admin.Backend.User.index', compact('users', 'branches', 'roles'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        // Check if current password matches
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => __('messages.current_password_incorrect')]);
        }

        // Prevent reusing the same password
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => __('messages.new_password_same_as_current')]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', __('messages.password_updated_successfully'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();
        $branchIds = $this->accessibleBranchIds($authUser);
        $companyId = $this->companyIdForUserPayload($request, $authUser);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                $this->tenantScopedUserEmailRule($companyId),
            ],
            'password' => 'required|min:6|confirmed',
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')->when(
                    ! $this->isSuperAdmin($authUser),
                    fn ($rule) => $rule->where(fn ($query) => $query
                        ->where('name', '!=', 'super_admin')
                        ->where(fn ($roleQuery) => $roleQuery->whereNull('company_id')->orWhere('company_id', $authUser->company_id)))
                ),
            ],
            'branch' => [
                'required',
                'integer',
                Rule::exists('branches', 'id')->when(
                    $branchIds !== null,
                    fn ($rule) => $rule->where(fn ($query) => $query->whereIn('id', $branchIds))
                ),
            ],
        ], $this->userValidationMessages());

        $branch = Branch::findOrFail($validated['branch']);

        $role = $this->roleForUserByName($validated['role'], $authUser);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'company_id' => $branch->company_id,
            'branch_id' => $validated['branch'],
            'role' => $role->name,
        ]);

        // Assign role via Spatie
        $user->assignRole($role);

        // Return JSON
        return response()->json([
            'success' => true,
            'message' => __('messages.user_created_successfully'),
            'data' => [
                'id' => $user->id,
                'index' => $this->scopeUsersForUser(User::query(), $authUser)->count(),
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(), // returns role name
                'branch_id' => $user->branch_id,
                'branch_name' => $user->branch ? $user->branch->name : '-',
            ],
        ]);
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email', // remove unique rule because we allow update
    //         'password' => 'required|min:6|confirmed',
    //         'role' => 'required|string|exists:roles,name',
    //         'branch' => 'required|integer|exists:branches,id',
    //     ]);

    //     // Check if user with this email already exists
    //     $user = User::where('email', $validated['email'])->first();

    //     if ($user) {
    //         // UPDATE existing user
    //         $user->update([
    //             'name' => $validated['name'],
    //             'branch_id' => $validated['branch'],
    //             'role' => $validated['role'] ?? $user->role,
    //             'password' => bcrypt($validated['password']),
    //         ]);

    //         // Sync role via Spatie
    //         $user->syncRoles([$validated['role']]);

    //         $message = __('messages.user_updated_successfully');
    //     } else {
    //         // CREATE new user
    //         $user = User::create([
    //             'name' => $validated['name'],
    //             'email' => $validated['email'],
    //             'password' => bcrypt($validated['password']),
    //             'branch_id' => $validated['branch'],
    //             'role' => $validated['role'] ?? 'employee',
    //         ]);

    //         // Assign role via Spatie
    //         $user->assignRole($validated['role']);

    //         $message = __('messages.user_created_successfully');
    //     }

    //     // Return JSON
    //     return response()->json([
    //         'success' => true,
    //         'message' => $message,
    //         'data' => [
    //             'id' => $user->id,
    //             'index' => User::count(),
    //             'name' => $user->name,
    //             'email' => $user->email,
    //             'role' => $user->getRoleNames()->first(),
    //             'branch_id' => $user->branch_id,
    //             'branch_name' => $user->branch ? $user->branch->name : '-',
    //         ],
    //     ]);
    // }

    public function update(Request $request, $id)
    {
        $authUser = auth()->user();
        $branchIds = $this->accessibleBranchIds($authUser);
        $user = $this->scopeUsersForUser(User::query(), $authUser)->findOrFail($id);
        $companyId = $this->companyIdForUserPayload($request, $authUser, $user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                $this->tenantScopedUserEmailRule($companyId, $user->id),
            ],
            'password' => 'nullable|min:6|confirmed',
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')->when(
                    ! $this->isSuperAdmin($authUser),
                    fn ($rule) => $rule->where(fn ($query) => $query
                        ->where('name', '!=', 'super_admin')
                        ->where(fn ($roleQuery) => $roleQuery->whereNull('company_id')->orWhere('company_id', $authUser->company_id)))
                ),
            ],
            'branch' => [
                'required',
                'integer',
                Rule::exists('branches', 'id')->when(
                    $branchIds !== null,
                    fn ($rule) => $rule->where(fn ($query) => $query->whereIn('id', $branchIds))
                ),
            ],
        ], $this->userValidationMessages());

        $branch = Branch::findOrFail($validated['branch']);

        $role = $this->roleForUserByName($validated['role'], $authUser);

        // Update users table
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company_id' => $branch->company_id,
            'branch_id' => $validated['branch'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
            'role' => $role->name,
        ]);

        // Update Spatie roles
        $user->syncRoles([$role]);

        return response()->json([
            'success' => true,
            'message' => __('messages.user_updated_successfully'),
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
                'branch_id' => $user->branch_id,
                'branch_name' => $user->branch->name ?? '-',
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|exists:users,id']);
        $user = $this->scopeUsersForUser(User::query(), auth()->user())->findOrFail($request->id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.user_deleted_successfully'),
            'id' => $request->id,
        ]);
    }

    public function updatePasswordView()
    {
        return view('Admin.Backend.ChangePassword.index');
    }

    private function scopeUsersForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->where('company_id', $user->company_id);
    }

    private function roleForUserByName(string $roleName, $user): Role
    {
        return Role::query()
            ->visibleToUser($user)
            ->when(! $this->isSuperAdmin($user), fn ($query) => $query->where('name', '!=', 'super_admin'))
            ->where('name', $roleName)
            ->orderByRaw('CASE WHEN company_id IS NULL THEN 1 ELSE 0 END')
            ->firstOrFail();
    }

    private function tenantScopedUserEmailRule(?int $companyId, ?int $ignoreUserId = null)
    {
        $rule = Rule::unique('users', 'email')
            ->where(fn ($query) => $companyId
                ? $query->where('company_id', $companyId)
                : $query->whereNull('company_id'));

        if ($ignoreUserId) {
            $rule->ignore($ignoreUserId);
        }

        return $rule;
    }

    private function companyIdForUserPayload(Request $request, $authUser, ?User $user = null): ?int
    {
        if ($request->filled('branch')) {
            $companyId = Branch::query()
                ->whereKey($request->integer('branch'))
                ->value('company_id');

            if ($companyId) {
                return (int) $companyId;
            }
        }

        return $user?->company_id ?: $authUser?->company_id;
    }

    private function userValidationMessages(): array
    {
        return [
            'email.unique' => 'This email is already used by another user in this tenant.',
            'email.email' => 'Please enter a valid email address.',
            'email.required' => 'Email address is required.',
        ];
    }
}

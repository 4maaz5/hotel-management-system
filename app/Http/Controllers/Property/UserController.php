<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use App\Support\PropertyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'assignedProperties', 'property']);

        // Filters
        if ($request->filled('full_name')) {
            $query->whereJsonContains('profile_data->first_name_en', $request->full_name);
        }

        if ($request->filled('username')) {
            $query->where('name', 'like', '%'.$request->username.'%');
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('mobile_number')) {
            $query->whereJsonContains('contact_info->mobile_number', $request->mobile_number);
        }

        if ($request->filled('email')) {
            $query->whereJsonContains('contact_info->email', $request->email);
        }

        $users = $query->latest()->paginate(10);
        $properties = $this->availableProperties();
        $outlets = Outlet::all();

        return view('admin.user.index', compact('users', 'properties', 'outlets'));
    }

    public function create()
    {
        $roles = $this->availableRoles();
        $properties = $this->availableProperties();

        return view('admin.user.create', compact('roles', 'properties'));
    }

    public function view($id)
    {
        $user = User::with(['assignedProperties', 'property'])->findOrFail($id);

        return view('admin.user.view', compact('user'));
    }

    public function store(Request $request)
    {
        $currentPropertyId = app(PropertyContext::class)->id();
        $propertySelection = $this->normalizePropertySelection($request, $currentPropertyId);

        $request->merge([
            'role_id' => $request->input('user_role'),
            'default_language' => $request->input('default_lang'),
            'first_name_en' => $request->input('full_name'),
            'description_en' => $request->input('description'),
            'mobile_number' => $request->input('employee_contact'),
            'business_phone' => $request->input('business_mobile_no'),
            'email' => $request->input('employee_email'),
            'default_property_id' => $propertySelection['default_property_id'],
            'property_ids' => $propertySelection['property_ids'],
        ]);

        $validated = $request->validate([
            'username' => 'required|unique:users,name|max:255',
            'password' => 'required|min:8|string|confirmed',
            'role_id' => ['required', 'integer', ...$this->roleRuleSet()],
            'first_name_en' => 'required|string|max:255',
            'default_language' => 'required',
            'expiry_date' => 'nullable|date',
            'title' => 'nullable|string',
            'status' => 'nullable|string',
            'user_type' => 'nullable|string',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'employee_code' => 'nullable|string',
            'nationality' => 'nullable|string',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'description_en' => 'nullable|string',
            'email' => 'nullable|email',
            'mobile_number' => 'required|string',
            'business_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
            'default_property_id' => ['nullable', 'integer', ...$this->propertyRuleSet()],
            'property_ids' => ['nullable', 'array'],
            'property_ids.*' => ['integer', ...$this->propertyRuleSet()],
        ]);

        // Handle file uploads
        $photoPath = null;
        $signaturePath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('user-photos', 'public');
        }

        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('user-signatures', 'public');
        }

        // Create user with JSON data
        $user = User::create([
            'company_id' => $request->user()->company_id,
            'name' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'default_language' => $validated['default_language'],
            'expiry_date' => $validated['expiry_date'],
            'status' => $validated['status'],
            'user_type' => $validated['user_type'],
            'email' => $validated['email'],
            'branch_id' => $this->propertyIdToBranchId($validated['default_property_id'] ?? null),

            'profile_data' => [
                'first_name_en' => $validated['first_name_en'],
                'middle_name_en' => $request->input('middle_name_en'),
                'last_name_en' => $request->input('last_name_en'),
                'username' => $request->input('username'),
                'photo_path' => $photoPath,
                'signature_path' => $signaturePath,
            ],

            'employment_data' => [
                'title' => $validated['title'],
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'employee_code' => $validated['employee_code'],
                'nationality' => $validated['nationality'],
                'department' => $validated['department'],
                'position' => $validated['position'],
                'description_en' => $validated['description_en'],
            ],

            'contact_info' => [
                'email' => $validated['email'],
                'mobile_number' => $validated['mobile_number'],
                'business_phone' => $validated['business_phone'],
                'address' => $validated['address'],
            ],

        ]);

        // Assign role using Spatie
        $role = $this->availableRoles()->firstWhere('id', (int) $validated['role_id']);
        abort_unless($role, 403);
        $user->assignRole($role);
        $branchIds = $this->propertyIdsToBranchIds($validated['property_ids'] ?? []);
        if (! empty($branchIds)) {
            $user->assignedProperties()->sync($branchIds);
        }

        return redirect()->route('setup-sidebar.property-user.index')
            ->with('success', __('messages.user_created_successfully'));
    }

    public function edit($id)
    {
        $user = User::with(['assignedProperties', 'roles'])->findOrFail($id);
        $roles = $this->availableRoles();
        $properties = $this->availableProperties();

        return view('admin.user.edit', compact('user', 'roles', 'properties'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentPropertyId = app(PropertyContext::class)->id();
        $propertySelection = $this->normalizePropertySelection(
            $request,
            $this->branchIdToPropertyId($user->branch_id) ?: $currentPropertyId
        );

        // Merge inputs to match store format
        $request->merge([
            'role_id' => $request->input('user_role'),
            'default_language' => $request->input('default_lang'),
            'first_name_en' => $request->input('full_name'),
            'description_en' => $request->input('description'),
            'mobile_number' => $request->input('employee_contact'),
            'business_phone' => $request->input('business_mobile_no'),
            'email' => $request->input('employee_email'),
            'default_property_id' => $propertySelection['default_property_id'],
            'property_ids' => $propertySelection['property_ids'],
        ]);

        // Validation
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,name,'.$user->id,
            'password' => 'nullable|min:8|string|confirmed',
            'role_id' => ['required', 'integer', ...$this->roleRuleSet()],
            'first_name_en' => 'required|string|max:255',
            'default_language' => 'required',
            'expiry_date' => 'nullable|date',
            'status' => 'nullable|string',
            'user_type' => 'nullable|string',
            'title' => 'nullable|string',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'employee_code' => 'nullable|string',
            'nationality' => 'nullable|string',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'description_en' => 'nullable|string',
            'email' => 'nullable|email',
            'mobile_number' => 'required|string',
            'business_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
            'default_property_id' => ['nullable', 'integer', ...$this->propertyRuleSet()],
            'property_ids' => ['nullable', 'array'],
            'property_ids.*' => ['integer', ...$this->propertyRuleSet()],
        ]);

        // Update password if provided
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Handle file uploads
        $profileData = $user->profile_data ?? [];
        if ($request->hasFile('photo')) {
            if (! empty($profileData['photo_path'])) {
                \Storage::disk('public')->delete($profileData['photo_path']);
            }
            $profileData['photo_path'] = $request->file('photo')->store('user-photos', 'public');
        }

        if ($request->hasFile('signature')) {
            if (! empty($profileData['signature_path'])) {
                \Storage::disk('public')->delete($profileData['signature_path']);
            }
            $profileData['signature_path'] = $request->file('signature')->store('user-signatures', 'public');
        }

        // Update JSON fields
        $user->profile_data = array_merge($profileData, [
            'first_name_en' => $validated['first_name_en'],
            'middle_name_en' => $request->input('middle_name_en'),
            'last_name_en' => $request->input('last_name_en'),
            'username' => $validated['username'],
        ]);

        $user->employment_data = array_merge($user->employment_data ?? [], [
            'title' => $validated['title'],
            'gender' => $validated['gender'],
            'date_of_birth' => $validated['date_of_birth'],
            'employee_code' => $validated['employee_code'],
            'nationality' => $validated['nationality'],
            'department' => $validated['department'],
            'position' => $validated['position'],
            'description_en' => $validated['description_en'],
        ]);

        $user->contact_info = array_merge($user->contact_info ?? [], [
            'email' => $validated['email'],
            'mobile_number' => $validated['mobile_number'],
            'business_phone' => $validated['business_phone'],
            'address' => $validated['address'],
        ]);

        // Update simple columns
        $user->company_id = $request->user()->company_id;
        $user->name = $validated['username'];
        $user->default_language = $validated['default_language'];

        if (array_key_exists('expiry_date', $validated)) {
            $user->expiry_date = $validated['expiry_date'];
        }
        if (array_key_exists('status', $validated)) {
            $user->status = $validated['status'];
        }
        if (array_key_exists('user_type', $validated)) {
            $user->user_type = $validated['user_type'];
        }
        if (array_key_exists('property_ids', $validated)) {
            // Property IDs are now handled via pivot table only
        }
        if (array_key_exists('default_property_id', $validated)) {
            $user->branch_id = $this->propertyIdToBranchId($validated['default_property_id'] ?: $currentPropertyId)
                ?: $user->branch_id;
        } else {
            $user->branch_id = $this->propertyIdToBranchId($currentPropertyId) ?: $user->branch_id;
        }

        $user->save();

        // Sync role
        $role = $this->availableRoles()->firstWhere('id', (int) $validated['role_id']);
        abort_unless($role, 403);
        $user->syncRoles($role);
        $branchIds = $this->propertyIdsToBranchIds($validated['property_ids'] ?? []);
        if (! empty($branchIds)) {
            $user->assignedProperties()->sync($branchIds);
        } else {
            $user->assignedProperties()->detach();
        }

        return redirect()->route('setup-sidebar.property-user.index')
            ->with('success', __('messages.user_updated_successfully'));
    }

    public function deactivate($id)
    {
        $user = User::findOrFail($id);

        // Delete uploaded files if they exist
        if (! empty($user->profile_data['photo_path'])) {
            Storage::disk('public')->delete($user->profile_data['photo_path']);
        }

        if (! empty($user->profile_data['signature_path'])) {
            Storage::disk('public')->delete($user->profile_data['signature_path']);
        }

        // Delete the user
        $user->delete();

        return redirect()->route('setup-sidebar.property-user.index')
            ->with('danger', __('messages.user_deactivated_successfully'));
    }

    public function assignOutlet(Request $request, User $user)
    {
        $propertyId = app(PropertyContext::class)->id();

        $validated = $request->validate([
            'property_id' => [
                'required',
                Rule::exists('properties', 'id')->where(fn ($query) => $query->where('company_id', auth()->user()->company_id)),
            ],
            'outlet_id' => ['required', 'exists:outlets,id'],
        ]);

        abort_if($propertyId && (int) $validated['property_id'] !== (int) $propertyId, 403);

        $branchId = $this->propertyIdToBranchId($validated['property_id']);
        abort_unless($branchId, 422, 'Selected property is not linked to a branch.');

        $user->update([
            'branch_id' => $branchId,
            'outlet_id' => $validated['outlet_id'],
        ]);

        $user->assignedProperties()->syncWithoutDetaching([$branchId]);

        return back()->with('success', __('messages.outlet_assigned_successfully'));
    }

    protected function availableProperties()
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'accessiblePropertiesQuery') && ! $user->isSuperAdmin()) {
            return $user->accessiblePropertiesQuery()
                ->orderBy('property_name_en')
                ->get();
        }

        return Property::query()->orderBy('property_name_en')->get();
    }

    protected function availableRoles()
    {
        $user = auth()->user();

        return Role::query()
            ->visibleToUser($user)
            ->where('name', '!=', 'super_admin')
            ->orderBy('name')
            ->get();
    }

    protected function roleRuleSet(): array
    {
        $roleIds = $this->availableRoles()->pluck('id')->map(fn ($id) => (int) $id)->all();

        return [
            Rule::exists('roles', 'id')->where(function ($query) use ($roleIds) {
                if (! empty($roleIds)) {
                    $query->whereIn('id', $roleIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }),
        ];
    }

    protected function propertyRuleSet(): array
    {
        $tenantId = auth()->user()?->company_id;
        $propertyIds = $this->availableProperties()->pluck('id')->map(fn ($id) => (int) $id)->all();

        return [
            Rule::exists('properties', 'id')->where(function ($query) use ($tenantId, $propertyIds) {
                if ($tenantId) {
                    $query->where('company_id', $tenantId);
                }

                if (! empty($propertyIds)) {
                    $query->whereIn('id', $propertyIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }),
        ];
    }

    protected function normalizePropertySelection(Request $request, ?int $fallbackPropertyId = null): array
    {
        $defaultPropertyId = $request->filled('default_property_id')
            ? (int) $request->input('default_property_id')
            : ($request->filled('company') ? (int) $request->input('company') : null);

        $propertyIds = collect($request->input('property_ids', []))
            ->filter()
            ->map(fn ($propertyId) => (int) $propertyId)
            ->values();

        if ($defaultPropertyId) {
            $propertyIds->prepend($defaultPropertyId);
        }

        if ($propertyIds->isEmpty() && $fallbackPropertyId) {
            $propertyIds->push((int) $fallbackPropertyId);
        }

        $propertyIds = $propertyIds
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (! $defaultPropertyId) {
            $defaultPropertyId = $propertyIds[0] ?? $fallbackPropertyId;
        }

        if ($defaultPropertyId && ! in_array((int) $defaultPropertyId, $propertyIds, true)) {
            $propertyIds[] = (int) $defaultPropertyId;
        }

        return [
            'default_property_id' => $defaultPropertyId ? (int) $defaultPropertyId : null,
            'property_ids' => array_values(array_unique(array_filter(array_map('intval', $propertyIds)))),
        ];
    }

    protected function propertyIdToBranchId(?int $propertyId): ?int
    {
        if (! $propertyId) {
            return null;
        }

        $branchId = Property::query()
            ->whereKey($propertyId)
            ->value('branch_id');

        return $branchId ? (int) $branchId : null;
    }

    protected function branchIdToPropertyId(?int $branchId): ?int
    {
        if (! $branchId) {
            return null;
        }

        $propertyId = Property::query()
            ->where('branch_id', $branchId)
            ->value('id');

        return $propertyId ? (int) $propertyId : null;
    }

    protected function propertyIdsToBranchIds(array $propertyIds): array
    {
        return Property::query()
            ->whereIn('id', array_values(array_unique(array_filter($propertyIds))))
            ->pluck('branch_id')
            ->filter()
            ->map(fn ($branchId) => (int) $branchId)
            ->unique()
            ->values()
            ->all();
    }
}

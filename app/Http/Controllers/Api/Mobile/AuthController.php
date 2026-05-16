<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Housekeeper;
use App\Models\MobileApiToken;
use App\Models\Property;
use App\Models\User;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ]);

        $user = User::withoutGlobalScopes()
            ->where('email', $validated['login'])
            ->orWhere('name', $validated['login'])
            ->orWhere('contact_info->email', $validated['login'])
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->status && strtolower($user->status) !== 'active') {
            throw ValidationException::withMessages([
                'login' => ['This account is inactive.'],
            ]);
        }

        app(TenantContext::class)->setTenantId($user->company_id);

        $property = $this->resolveProperty($user, $validated['branch_id'] ?? null);
        app(PropertyContext::class)->setProperty($property);

        $branchId = $property->branch_id;

        $housekeeper = Housekeeper::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->first();

        if (! $housekeeper) {
            throw ValidationException::withMessages([
                'login' => ['This user is not an active housekeeper for the selected property.'],
            ]);
        }

        $plainToken = Str::random(64);
        $expiresAt = now()->addDays((int) config('mobile_attendance.token_ttl_days', 30));
        MobileApiToken::create([
            'user_id' => $user->id,
            'branch_id' => $branchId,
            'token_hash' => hash('sha256', $plainToken),
            'device_name' => $validated['device_name'] ?? null,
            'expires_at' => $expiresAt,
        ]);

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $plainToken,
            'expires_at' => $expiresAt?->toISOString(),
            'user' => $this->userPayload($user, $housekeeper, $property),
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink(['email' => $validated['email']]);

        return response()->json([
            'message' => 'If this email exists, a password reset link has been sent.',
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $property = app(PropertyContext::class)->property();

        abort_unless($property, 422, 'No branch context is available.');

        $housekeeper = Housekeeper::with('user')
            ->where('user_id', $user->id)
            ->where('branch_id', $property->branch_id)
            ->first();

        return response()->json([
            'user' => $this->userPayload($user, $housekeeper, $property),
        ]);
    }

    public function logout(Request $request)
    {
        $plainToken = $request->bearerToken();

        if ($plainToken) {
            MobileApiToken::where('token_hash', hash('sha256', $plainToken))
                ->where('user_id', $request->user()->id)
                ->update(['revoked_at' => now()]);
        }

        return response()->json(['message' => 'Logged out successfully.']);
    }

    protected function resolveProperty(User $user, ?int $requestedBranchId): Property
    {
        $property = null;

        if ($requestedBranchId) {
            $property = $user->accessiblePropertiesQuery()
                ->where('branch_id', $requestedBranchId)
                ->first();
        }

        if (! $property && $user->branch_id) {
            $property = $user->accessiblePropertiesQuery()
                ->where('branch_id', $user->branch_id)
                ->first();
        }

        if (! $property) {
            $property = $user->accessiblePropertiesQuery()->orderBy('property_name_en')->first();
        }

        if (! $property) {
            throw ValidationException::withMessages([
                'branch_id' => ['No accessible branch was found for this user.'],
            ]);
        }

        return $property;
    }

    protected function userPayload(User $user, ?Housekeeper $housekeeper, ?Property $property): array
    {
        $photoPath = $user->profile_data['photo_path'] ?? null;
        $photoUrl = $photoPath ? Storage::disk('public')->url($photoPath) : null;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'employee_name' => $user->profile_data['first_name_en'] ?? $user->name,
            'mobile_number' => $user->contact_info['mobile_number'] ?? null,
            'photo_url' => $photoUrl,
            'branch_id' => $property?->branch_id,
            'housekeeper_id' => $housekeeper?->id,
            'roles' => $user->getRoleNames()->values(),
        ];
    }
}

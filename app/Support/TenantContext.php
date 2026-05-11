<?php

namespace App\Support;

use App\Models\Company;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class TenantContext
{
    protected ?Company $tenant = null;

    protected ?int $tenantId = null;

    public function setTenant(?Company $tenant): void
    {
        $this->tenant = $tenant;
        $this->tenantId = $tenant?->getKey();
    }

    public function setTenantId(?int $tenantId): void
    {
        $this->tenant = null;
        $this->tenantId = $tenantId;
    }

    public function tenant(): ?Company
    {
        if ($this->tenant) {
            return $this->tenant;
        }

        if (! $this->tenantId) {
            return null;
        }

        return $this->tenant = Company::find($this->tenantId);
    }

    public function id(): ?int
    {
        if ($this->tenantId) {
            return $this->tenantId;
        }

        $user = $this->loadedUser();

        if (! $user) {
            return null;
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return null;
        }

        if ($user->company_id) {
            return (int) $user->company_id;
        }

        if ($user->branch && $user->branch->company_id) {
            return (int) $user->branch->company_id;
        }

        return null;
    }

    public function forget(): void
    {
        $this->tenant = null;
        $this->tenantId = null;
    }

    protected function loadedUser(): ?Authenticatable
    {
        $guard = Auth::guard();

        if (method_exists($guard, 'hasUser') && $guard->hasUser()) {
            return $guard->user();
        }

        return null;
    }
}

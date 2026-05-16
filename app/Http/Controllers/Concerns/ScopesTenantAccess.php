<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Support\Collection;

trait ScopesTenantAccess
{
    protected function isSuperAdmin($user): bool
    {
        if (! $user) {
            return false;
        }

        return method_exists($user, 'isSuperAdmin')
            ? $user->isSuperAdmin()
            : $user->hasRole('super_admin') || $user->role === 'super_admin';
    }

    protected function accessibleBranchIds($user): ?Collection
    {
        if ($this->isSuperAdmin($user)) {
            return null;
        }

        if ($user->branch_id) {
            return collect([(int) $user->branch_id]);
        }

        if ($user->company_id) {
            return Branch::where('company_id', $user->company_id)->pluck('id');
        }

        return collect();
    }

    protected function scopeBranchesForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->whereKey($user->branch_id);
        }

        return $query->where('company_id', $user->company_id);
    }

    protected function scopeWarehousesForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->where('company_id', $user->company_id);
    }

    protected function userCanAccessBranch(?int $branchId, $user): bool
    {
        if (! $branchId) {
            return false;
        }

        if ($this->isSuperAdmin($user)) {
            return Branch::whereKey($branchId)->exists();
        }

        if ($user->branch_id) {
            return (int) $branchId === (int) $user->branch_id;
        }

        return Branch::whereKey($branchId)
            ->where('company_id', $user->company_id)
            ->exists();
    }

    protected function userCanAccessWarehouse(int $warehouseId, $user): bool
    {
        return $this->scopeWarehousesForUser(Warehouse::whereKey($warehouseId), $user)->exists();
    }
}

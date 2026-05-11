<?php

namespace App\Models\Concerns;

use App\Models\Company;
use App\Models\Scopes\TenantScope;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        $instance = new static;

        $table = $instance->getTable();

        if (! Schema::hasTable($table)) {
            return;
        }

        $column = static::getTenantColumn();

        if (! Schema::hasColumn($table, $column)) {
            return;
        }

        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model) use ($column): void {
            if (! empty($model->{$column})) {
                return;
            }

            $tenantId = app(TenantContext::class)->id();
            $guard = Auth::guard();
            $user = method_exists($guard, 'hasUser') && $guard->hasUser()
                ? $guard->user()
                : null;

            $resolvedId = $tenantId;

            if (! $resolvedId && $user && method_exists($user, 'isSuperAdmin') && ! $user->isSuperAdmin()) {
                $resolvedId = static::resolveTenantIdFromUser($user);
            }

            if ($resolvedId) {
                $model->{$column} = $resolvedId;
            }
        });
    }

    public static function getTenantColumn(): string
    {
        return defined('static::TENANT_COLUMN') ? static::TENANT_COLUMN : 'company_id';
    }

    protected static function resolveTenantIdFromUser($user): ?int
    {
        if (property_exists($user, 'company_id') && $user->company_id) {
            return (int) $user->company_id;
        }

        if ($user->relationLoaded('branch') && $user->branch && $user->branch->company_id) {
            return (int) $user->branch->company_id;
        }

        if ($user->branch && $user->branch->company_id) {
            return (int) $user->branch->company_id;
        }

        return null;
    }

    public function tenant()
    {
        return $this->belongsTo(Company::class, static::getTenantColumn());
    }

    public function scopeForTenant(Builder $query, int|Company $tenant): Builder
    {
        $column = static::getTenantColumn();
        $tenantId = $tenant instanceof Company ? $tenant->getKey() : $tenant;

        return $query->withoutGlobalScope(TenantScope::class)
            ->where($this->qualifyColumn($column), $tenantId);
    }
}

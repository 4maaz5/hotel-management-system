<?php

namespace App\Models\Scopes;

use App\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = $this->loadedUser();

        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return;
        }

        $tenantId = app(TenantContext::class)->id();

        if ($tenantId) {
            $column = method_exists($model, 'getTenantColumn')
                ? $model->getTenantColumn()
                : 'company_id';

            $builder->where($model->qualifyColumn($column), $tenantId);

            return;
        }

        if ($user) {
            $builder->whereRaw('1 = 0');
        }
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

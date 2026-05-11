<?php

namespace App\Models\Scopes;

use App\Support\PropertyContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class PropertyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = $this->loadedUser();

        if (! $user) {
            return;
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return;
        }

        $property = app(PropertyContext::class)->property();

        if ($property) {
            $column = method_exists($model, 'getPropertyColumn')
                ? $model->getPropertyColumn()
                : 'branch_id';

            $builder->where($model->qualifyColumn($column), $property->branch_id);

            return;
        }

        if (method_exists($user, 'isTenantOwner') && $user->isTenantOwner()) {
            return;
        }

        $builder->whereRaw('1 = 0');
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

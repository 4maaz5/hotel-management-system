<?php

namespace App\Models\Scopes;

use App\Models\Property;
use App\Support\PropertyContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CurrentPropertyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = $this->loadedUser();

        if (! $user || (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())) {
            return;
        }

        $property = app(PropertyContext::class)->property();

        if (! $property) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $column = method_exists($model, 'getPropertyColumn')
            ? $model->getPropertyColumn()
            : 'branch_id';

        $builder->where($model->qualifyColumn($column), $property->branch_id);
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

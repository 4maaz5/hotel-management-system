<?php

namespace App\Models;

use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

class Role extends SpatieRole
{
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeVisibleToUser($query, ?User $user)
    {
        if (! $user || $user->isSuperAdmin()) {
            return $query;
        }

        return $query->where(function ($roleQuery) use ($user) {
            $roleQuery->whereNull('company_id')
                ->orWhere('company_id', $user->company_id);
        });
    }

    public function scopeTenantOwnedBy($query, ?User $user)
    {
        if (! $user || $user->isSuperAdmin()) {
            return $query;
        }

        return $query->where('company_id', $user->company_id);
    }

    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] ??= Guard::getDefaultName(static::class);
        $params = [
            'company_id' => $attributes['company_id'] ?? null,
            'name' => $attributes['name'],
            'guard_name' => $attributes['guard_name'],
        ];

        if (static::findByParam($params)) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        return static::query()->create($attributes);
    }

    public static function findByName(string $name, ?string $guardName = null): RoleContract
    {
        $guardName ??= Guard::getDefaultName(static::class);
        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (! $role) {
            throw RoleDoesNotExist::named($name, $guardName);
        }

        return $role;
    }

    public static function findOrCreate(string $name, ?string $guardName = null): RoleContract
    {
        $guardName ??= Guard::getDefaultName(static::class);
        $attributes = ['name' => $name, 'guard_name' => $guardName];

        $role = static::findByParam($attributes);

        if (! $role) {
            return static::query()->create($attributes);
        }

        return $role;
    }

    protected static function findByParam(array $params = []): ?RoleContract
    {
        $query = static::query();
        $registrar = app(PermissionRegistrar::class);

        if ($registrar->teams) {
            $teamsKey = $registrar->teamsKey;

            $query->where(fn ($q) => $q->whereNull($teamsKey)
                ->orWhere($teamsKey, $params[$teamsKey] ?? getPermissionsTeamId())
            );
            unset($params[$teamsKey]);
        }

        $companyId = array_key_exists('company_id', $params)
            ? $params['company_id']
            : auth()->user()?->company_id;
        unset($params['company_id']);

        if ($companyId) {
            $query->where(fn ($roleQuery) => $roleQuery->where('company_id', $companyId)->orWhereNull('company_id'))
                ->orderByRaw('CASE WHEN company_id IS NULL THEN 1 ELSE 0 END');
        } else {
            $query->whereNull('company_id');
        }

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }
}

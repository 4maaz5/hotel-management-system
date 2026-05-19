<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use BelongsToTenant, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'company_id',
        'branch_id',
        'name',
        'email',
        'password',
        'role',
        'default_language',
        'expiry_date',
        'status',
        'user_type',
        'profile_data',
        'employment_data',
        'contact_info',
        'outlet_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'profile_data' => 'array',
            'employment_data' => 'array',
            'contact_info' => 'array',
            'expiry_date' => 'date',
        ];
    }

    // ===== HR System Relationships =====

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    // ===== Reservation System Relationships =====

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'company_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'branch_id', 'branch_id');
    }

    public function assignedProperties()
    {
        return $this->belongsToMany(Property::class, 'property_user', 'user_id', 'branch_id', 'id', 'branch_id')
            ->withTimestamps();
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function smsSettings()
    {
        return $this->hasMany(SmsUserSetting::class);
    }

    // ===== HR System Methods =====

    public function getRoleNames()
    {
        return collect([$this->role]);
    }

    public function getAvatarAttribute()
    {
        return $this->avatar_path ? asset('storage/'.$this->avatar_path) : asset('images/default-avatar.png');
    }

    // ===== Reservation System Methods =====

    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['Administrator', 'super_admin'], true)
            || $this->hasAnyRole(['Administrator', 'super_admin']);
    }

    public function isTenantOwner(): bool
    {
        return $this->role === 'owner'
            || $this->user_type === 'owner'
            || $this->hasRole('owner');
    }

    public function canBeDeletedFromTenantDashboard(?self $actor = null): bool
    {
        if ($actor && (int) $actor->id === (int) $this->id) {
            return false;
        }

        return ! $this->isTenantOwner();
    }

    public function accessiblePropertiesQuery(): Builder
    {
        $query = Property::query();

        if ($this->isSuperAdmin()) {
            return $query->withoutGlobalScope(TenantScope::class);
        }

        if ($this->isTenantOwner()) {
            return $query;
        }

        $propertyIds = $this->assignedProperties()
            ->pluck('properties.id')
            ->all();

        if ($this->branch_id) {
            $propertyFromBranch = Property::where('branch_id', $this->branch_id)->first();
            if ($propertyFromBranch) {
                $propertyIds[] = (int) $propertyFromBranch->id;
            }
        }

        $propertyIds = array_values(array_unique(array_filter($propertyIds)));

        if (empty($propertyIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('properties.id', $propertyIds);
    }

    public function canAccessProperty(int $propertyId): bool
    {
        return $this->accessiblePropertiesQuery()
            ->whereKey($propertyId)
            ->exists();
    }
}

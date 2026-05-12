<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'subdomain',
        'email',
        'phone',
        'start_date',
        'end_date',
        'subscription_status',
        'subscription_plan_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $appends = ['status'];

    public function getStatusAttribute(): ?string
    {
        return $this->subscription_status;
    }

    public function setStatusAttribute(?string $value): void
    {
        $this->subscription_status = $value;
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'company_id');
    }

    public function hasFeature(string $feature): bool
    {
        return $this->plan?->hasFeature($feature) ?? false;
    }

    public function maxLimit(string $key, int $default = 0): int
    {
        return $this->plan?->maxLimit($key, $default) ?? 0;
    }

    public function usageCount(string $key): int
    {
        return match ($key) {
            'max_users' => $this->users()->count(),
            'max_properties' => $this->properties()->count(),
            default => 0,
        };
    }

    public function canExceedLimit(string $key): bool
    {
        $limit = $this->maxLimit($key);
        return $limit === 0 || $this->usageCount($key) < $limit;
    }

    public function isActiveSubscription(?CarbonInterface $date = null): bool
    {
        $date ??= now()->startOfDay();

        if ($this->subscription_status !== 'active') {
            return false;
        }

        if (! $this->start_date || ! $this->end_date) {
            return false;
        }

        return $date->betweenIncluded(
            $this->start_date->copy()->startOfDay(),
            $this->end_date->copy()->endOfDay()
        );
    }
}

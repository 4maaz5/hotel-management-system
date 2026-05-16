<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'billing_period',
        'features',
        'limits',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'subscription_plan_id');
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    public function maxLimit(string $key, int $default = 0): int
    {
        return (int) ($this->limits[$key] ?? $default);
    }

    public function formattedPrice(): string
    {
        return number_format($this->price, 2);
    }

    public function featureList(): array
    {
        return [
            'custom_branding' => 'Custom Branding (White-label)',
        ];
    }
}

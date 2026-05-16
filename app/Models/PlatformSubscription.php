<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformSubscription extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'third_party_platform_id',
        'company_id',
        'branch_id',
        'subscription_start_date',
        'subscription_end_date',
        'contract_amount',
        'commission_percentage',
        'status',
        'notes',
    ];

    protected $casts = [
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'contract_amount' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
    ];

    /* ================= Relationships ================= */

    public function platform()
    {
        return $this->belongsTo(ThirdPartyPlatform::class, 'third_party_platform_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /* ================= Helpers ================= */

    public function getCommissionAmountAttribute()
    {
        return ($this->contract_amount * $this->commission_percentage) / 100;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}

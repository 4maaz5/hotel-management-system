<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingAgent extends Model
{
    use BelongsToTenant, HasFactory;

    protected $table = 'marketing_agents';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'company_id',
        'brand_id',
        'branch_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'type',
        'commission_percent',
    ];

    /* =========================
     | Relationships
     ========================= */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function quotations()
    {
        return $this->hasMany(MarketingQuotation::class);
    }

    public function commissions()
    {
        return $this->hasMany(MarketingCommission::class);
    }

    /* =========================
     | Query Scopes
     ========================= */

    public function scopeAgentType($query)
    {
        return $query->where('type', 'agent');
    }

    public function scopeCompanyType($query)
    {
        return $query->where('type', 'company');
    }

    /* =========================
     | Helpers
     ========================= */

    public function isAgent(): bool
    {
        return $this->type === 'agent';
    }

    public function isCompany(): bool
    {
        return $this->type === 'company';
    }
}

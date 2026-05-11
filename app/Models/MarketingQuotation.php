<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingQuotation extends Model
{
    use HasFactory;

    protected $table = 'marketing_quotations';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'marketing_agent_id',
        'manual_agent_name',
        'branch_id',
        'quotation_number',
        'client_name',
        'client_contact',
        'description',
        'quotation_amount',
        'approved_at',
        'status',
        'account_number',
        'logo',
        'vat_no',
        'email',
        'cr_no',
        'bank_name',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'quotation_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /* =========================
       Relationships
    ========================= */

    public function agent()
    {
        return $this->belongsTo(MarketingAgent::class, 'marketing_agent_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function commission()
    {
        return $this->hasOne(MarketingCommission::class, 'quotation_id');
    }

    /* =========================
       Query Scopes
    ========================= */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /* =========================
       Helpers
    ========================= */

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}

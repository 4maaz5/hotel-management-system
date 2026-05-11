<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingCommission extends Model
{
    protected $fillable = [
        'marketing_agent_id',
        'branch_id',
        'quotation_id',
        'commission_percentage',
        'commission_amount',
        'paid_status',
    ];

    public function agent()
    {
        return $this->belongsTo(MarketingAgent::class, 'marketing_agent_id');
    }

    public function quotation()
    {
        return $this->belongsTo(MarketingQuotation::class, 'quotation_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

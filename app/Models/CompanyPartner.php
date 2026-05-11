<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPartner extends Model
{
    protected $fillable = [
        'company_id',
        'partner_type',
        'full_name',
        'email',
        'phone',
        'nationality',
        'id_type',
        'id_number',
        'investment_amount',
        'share_percentage',
        'share_quantity',
        'notes',
    ];

    public function documents()
    {
        return $this->hasMany(PartnerDocument::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

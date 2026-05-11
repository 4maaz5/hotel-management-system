<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    protected $fillable = [
        'employee_id',
        'provider_name',
        'policy_number',
        'policy_type',
        'start_date',
        'expiry_date',
        'premium_amount',
        'document',
    ];

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}

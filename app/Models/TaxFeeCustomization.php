<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TaxFeeCustomization extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'is_expenses',
        'type',
        'custom_name',
        'method',
        'amount',
        'applied_on',
        'has_max_length',
        'max_length',
        'start_date',
        'end_date',
        'charged_on_fees',
    ];

    protected $casts = [
        'is_expenses' => 'boolean',
        'charged_on_fees' => 'boolean',
        'has_max_length' => 'boolean',
        'applied_on' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}

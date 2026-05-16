<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class DiscountType extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'type',
        'is_active',
        'report_name',
        'description',
    ];

    protected $casts = [
        'report_name' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
    ];
}

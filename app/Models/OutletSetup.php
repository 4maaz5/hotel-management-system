<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class OutletSetup extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'status',
        'operating_status',
        'outlet_code',
        'name',
        'description',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}

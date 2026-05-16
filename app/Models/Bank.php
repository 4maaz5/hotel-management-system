<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'name',
        'account_number',
        'currency',
        'iban',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

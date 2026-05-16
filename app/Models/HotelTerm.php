<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class HotelTerm extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'order_no',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class LoyaltyAutoSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'auto_loyalty_upgrade',
    ];
}

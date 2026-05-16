<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityDeposit extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'company_id',
        'unit_type_id',
        'deposit_amount',
    ];

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }
}

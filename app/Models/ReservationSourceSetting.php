<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ReservationSourceSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'master_source_id',
        'status',
        'report_name',
        'url',
        'commission_rate',
        'tax_mode',
        'tax_calculation_type',
        'description',
    ];

    public function masterSource()
    {
        return $this->belongsTo(
            ReservationSourceMaster::class,
            'master_source_id'
        );
    }
}

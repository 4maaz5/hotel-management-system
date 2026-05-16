<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PropertyCommercialDetail extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'registration_number',
        'activity_license_number',
        'vat_registration_number',
        'registration_file_path',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'branch_id', 'branch_id');
    }
}

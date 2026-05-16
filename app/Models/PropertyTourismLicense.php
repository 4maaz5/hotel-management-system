<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PropertyTourismLicense extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'tourism_activity_type',
        'license_number',
        'license_expiry_date',
        'number_of_rooms',
        'number_of_beds',
        'license_file_path',
    ];

    protected $casts = [
        'license_expiry_date' => 'date',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'branch_id', 'branch_id');
    }
}

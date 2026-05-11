<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleDocuments extends Model
{
    protected $fillable = [
        'vehicle_id',
        'file_path',
        'start_date',
        'end_date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}

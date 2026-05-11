<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'name',
        'phone',
        'id_number',
    ];

    // Relationship to vehicle
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Relationship to documents
    public function documents()
    {
        return $this->hasMany(DriverDocument::class);
    }

    public function accidents()
    {
        return $this->hasMany(Accident::class);
    }
}

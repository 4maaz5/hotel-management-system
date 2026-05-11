<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $guarded = []; // allows all

    protected $fillable = [
        'branch_id',
        'name',
        'model',
        'plate_number',
        'owner_name',
        'owner_contact',
        'owner_iqama',
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function documents()
    {
        return $this->hasMany(VehicleDocuments::class);
    }

    public function accidents()
    {
        return $this->hasMany(Accident::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityCategory extends Model
{
    /** @use HasFactory<\Database\Factories\FacilityCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }

    public function propertyFacilities()
    {
        return $this->hasMany(PropertyFacility::class);
    }
}

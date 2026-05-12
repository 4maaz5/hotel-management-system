<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    /** @use HasFactory<\Database\Factories\FacilityFactory> */
    use HasFactory;

    protected $fillable = [
        'facility_category_id',
        'name',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(
            FacilityCategory::class,
            'facility_category_id'
        );
    }
}

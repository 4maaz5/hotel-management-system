<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    /** @use HasFactory<\Database\Factories\FacilityFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
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

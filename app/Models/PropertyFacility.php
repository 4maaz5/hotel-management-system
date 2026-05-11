<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyFacility extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'facility_category_id',
        'facility_id',
        'description',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(
            FacilityCategory::class,
            'facility_category_id'
        );
    }

    public function facility()
    {
        return $this->belongsTo(
            Facility::class,
            'facility_id'
        );
    }
}

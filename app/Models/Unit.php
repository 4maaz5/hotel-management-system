<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant, HasFactory;

    protected $fillable = [
        'property_id',
        'unit_number',
        'unit_class_id',
        'unit_type_id',
        'can_be_merged',
        'block_id',
        'floor_id',
        'phone_extension',
        'number_of_toilets',
        'kitchen_type',
        'hall_type_id',
        'unit_area',
        'number_of_single_beds',
        'number_of_double_beds',
        'base_occupancy',
        'description',
        'is_active',
        'housekeeping_status',
    ];

    protected $casts = [
        'can_be_merged' => 'boolean',
        'is_active' => 'boolean',
        'unit_area' => 'decimal:2',
    ];

    public function unitClass()
    {
        return $this->belongsTo(UnitClass::class);
    }

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }

    public function unitTypeCustomization()
    {
        return $this->belongsTo(UnitTypeCustomization::class, 'unit_type_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function hallType()
    {
        return $this->belongsTo(HallType::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'amenity_unit')
            ->withTimestamps();
    }

    public function mergeAsA()
    {
        return $this->hasMany(UnitMerge::class, 'unit_a_id');
    }

    public function mergeAsB()
    {
        return $this->hasMany(UnitMerge::class, 'unit_b_id');
    }
    public function reservations()
{
    return $this->hasMany(Reservation::class);
}
}

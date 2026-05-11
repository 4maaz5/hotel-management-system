<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class HousekeepingTask extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant;

    protected $table = 'housekeeping_tasks';

    protected $fillable = [
        'tenant_id',
        'property_id',
        'task_type',
        'unit_id',
        'property_facility_id',
        'task_type_id',
        'housekeeper_id',
        'created_by',
        'priority',
        'status',
        'description',
        'start_date',
        'completed_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'completed_date' => 'date',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function propertyFacility()
    {
        return $this->belongsTo(PropertyFacility::class, 'property_facility_id');
    }

    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'task_type_id');
    }

    public function housekeeper()
    {
        return $this->belongsTo(Housekeeper::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function media()
    {
        return $this->hasMany(TaskMedia::class, 'task_id')->orderBy('sort_order');
    }
}

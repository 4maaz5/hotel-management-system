<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffAttendance extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'user_id',
        'housekeeper_id',
        'attendance_date',
        'check_in_at',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_accuracy_meters',
        'check_in_distance_meters',
        'check_in_within_geofence',
        'check_out_at',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_accuracy_meters',
        'check_out_distance_meters',
        'check_out_within_geofence',
        'status',
        'notes',
        'edited_by',
        'deleted_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'check_in_latitude' => 'decimal:8',
        'check_in_longitude' => 'decimal:8',
        'check_out_latitude' => 'decimal:8',
        'check_out_longitude' => 'decimal:8',
        'check_in_within_geofence' => 'boolean',
        'check_out_within_geofence' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function housekeeper()
    {
        return $this->belongsTo(Housekeeper::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'branch_id', 'branch_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use BelongsToTenant;

    protected $guarded = []; // allows all

    protected $fillable = [
        'company_id',
        'branch_id',
        'name',
        'model',
        'plate_number',
        'owner_name',
        'owner_contact',
        'owner_iqama',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

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

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    use BelongsToTenant, HasFactory;

    protected $table = 'unit_types';

    protected $fillable = [
        'tenant_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function customizations()
    {
        return $this->hasMany(UnitTypeCustomization::class);
    }

    public function rate()
    {
        return $this->hasOne(UnitTypeRate::class);
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use BelongsToTenant, HasFactory;

    protected $table = 'amenities';

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'amenity_unit')
            ->withTimestamps();
    }
}

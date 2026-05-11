<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AmenityUnit extends Model
{
    use BelongsToTenant;

    protected $table = 'amenity_unit';

    protected $fillable = [
        'tenant_id',
        'amenity_id',
        'unit_id',
    ];
}

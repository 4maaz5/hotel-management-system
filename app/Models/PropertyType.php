<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    protected $fillable = [
        'code',
        'name_en',
        'name_ar',
        'is_active',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'iso_code',
        'name_en',
        'name_ar',
        'phone_code',
        'currency_code',
        'time_zone',
    ];

    public function regions()
    {
        return $this->hasMany(Region::class);
    }
}

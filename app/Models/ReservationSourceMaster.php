<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationSourceMaster extends Model
{
    protected $fillable = [
        'name',
        'channel_type',
        'icon',
        'is_active',
    ];
}

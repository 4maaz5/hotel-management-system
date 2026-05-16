<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallType extends Model
{
    use HasFactory;

    protected $table = 'hall_types';

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}

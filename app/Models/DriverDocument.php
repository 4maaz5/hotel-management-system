<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'file_path',
        'start_date',
        'end_date',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}

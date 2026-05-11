<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveDocument extends Model
{
    protected $fillable = ['leave_id', 'file_path', 'file_type'];

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }
}

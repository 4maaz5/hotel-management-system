<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'title',
        'room_name',
        'start_time',
        'duration',
        'created_by',
        'link',
    ];

    public function participants()
    {
        return $this->hasMany(MeetingParticipant::class);
    }
}

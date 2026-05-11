<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingParticipant extends Model
{
    protected $fillable = ['meeting_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

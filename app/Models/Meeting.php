<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
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

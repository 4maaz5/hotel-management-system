<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemNotificationRead extends Model
{
    protected $guarded = [];

    protected $table = 'system_notification_reads';

    public function notification()
    {
        return $this->belongsTo(SystemNotification::class, 'notification_id');
    }
}

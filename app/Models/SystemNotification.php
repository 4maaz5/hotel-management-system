<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    use BelongsToTenant;

    protected $table = 'system_notifications';

    protected $fillable = [
        'company_id', 'type', 'message', 'recipient_type', 'recipient_id', 'status',
        'scheduled_at', 'sent_at', 'created_by', 'parent_id', 'department_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function scopeSystem($query)
    {
        return $query->where('type', 'system');
    }

    public function scopeReadyToSend($query)
    {
        return $query->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', Carbon::now());
            });
    }

    public function reads()
    {
        return $this->hasMany(SystemNotificationRead::class, 'notification_id');
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'requested_by',
        'guest_id',
        'phone',
        'recipient_name',
        'source',
        'sms_type',
        'template_type',
        'delivery_mode',
        'status',
        'message_preview',
        'provider_response',
        'error_message',
    ];

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}

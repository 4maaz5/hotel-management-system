<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupportTicket extends Model
{
    use BelongsToTenant;

    public const STATUSES = ['open', 'pending', 'closed'];

    public const PRIORITIES = ['low', 'normal', 'high', 'urgent'];

    public const AREAS = ['reservation', 'hr'];

    protected $fillable = [
        'company_id',
        'created_by',
        'subject',
        'category',
        'support_area',
        'priority',
        'status',
        'last_message_at',
        'last_sender_role',
        'tenant_last_read_at',
        'super_admin_last_read_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'tenant_last_read_at' => 'datetime',
        'super_admin_last_read_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(SupportTicketMessage::class)->latestOfMany();
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'closed' => 'secondary',
            'pending' => 'warning text-dark',
            default => 'success',
        };
    }

    public function priorityBadgeClass(): string
    {
        return match ($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning text-dark',
            'low' => 'secondary',
            default => 'primary',
        };
    }

    public function areaLabel(): string
    {
        return match ($this->support_area) {
            'hr' => 'HRM',
            default => 'Reservation',
        };
    }
}

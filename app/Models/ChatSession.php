<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'property_id',
        'language',
        'status',
        'context',
        'last_message_at',
    ];

    protected $casts = [
        'context' => 'array',
        'last_message_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'session_id');
    }

    public function logs()
    {
        return $this->hasMany(AiChatLog::class, 'session_id');
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AiChatLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'session_id',
        'message_id',
        'user_id',
        'intent',
        'language',
        'tool_name',
        'status',
        'request_payload',
        'plan_payload',
        'tool_payload',
        'tool_result',
        'response_payload',
        'error_message',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'plan_payload' => 'array',
        'tool_payload' => 'array',
        'tool_result' => 'array',
        'response_payload' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

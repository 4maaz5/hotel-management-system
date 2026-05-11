<?php

namespace App\Jobs;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\Chatbot\AiChatService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAiChatMessage implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $sessionId,
        public readonly int $messageId,
    ) {
        $this->onQueue('chatbot');
    }

    public function handle(AiChatService $chatService): void
    {
        $session = ChatSession::query()->find($this->sessionId);
        $message = ChatMessage::query()->find($this->messageId);

        if (! $session || ! $message) {
            return;
        }

        $chatService->process($session, $message);
    }
}

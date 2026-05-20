<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatMessageRequest;
use App\Jobs\ProcessAiChatMessage;
use App\Models\ChatSession;
use App\Services\Chatbot\ChatSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatSessionService $sessionService,
    ) {
    }

    public function current(Request $request): JsonResponse
    {
        $session = $this->sessionService->current($request->user(), $request);

        return response()->json([
            'session_id' => $session->id,
            'status' => $session->status,
            'language' => $session->language,
            'messages' => $this->formatMessages($session),
        ]);
    }

    public function store(StoreChatMessageRequest $request): JsonResponse
    {
        $session = $this->sessionService->resolve(
            $request->user(),
            $request->integer('session_id') ?: null,
            $request->filled('user_id') ? (int) $request->input('user_id') : null,
            $request,
        );

        $this->sessionService->touch($session);

        $message = $session->messages()->create([
            'role' => 'user',
            'message' => trim((string) $request->string('message')),
        ]);

        ProcessAiChatMessage::dispatch($session->id, $message->id);

        return response()->json([
            'status' => 'queued',
            'session_id' => $session->id,
            'message' => [
                'id' => $message->id,
                'role' => $message->role,
                'message' => $message->message,
                'created_at' => $message->created_at?->toIso8601String(),
            ],
        ], 202);
    }

    public function messages(Request $request, int $session): JsonResponse
    {
        $chatSession = ChatSession::withoutGlobalScopes()->findOrFail($session);

        abort_unless($chatSession->user_id === $request->user()->id, 404);

        $query = $chatSession->messages()->orderBy('id');

        if ($request->filled('after')) {
            $query->where('id', '>', (int) $request->input('after'));
        }

        return response()->json([
            'session_id' => $chatSession->id,
            'status' => $chatSession->status,
            'language' => $chatSession->language,
            'messages' => $query->get()->map(function ($message) {
                return [
                    'id' => $message->id,
                    'role' => $message->role,
                    'message' => $message->message,
                    'created_at' => $message->created_at?->toIso8601String(),
                    'metadata' => $message->metadata ?? [],
                ];
            })->all(),
        ]);
    }

    private function formatMessages(ChatSession $session): array
    {
        return $session->messages()
            ->latest('id')
            ->take(20)
            ->get()
            ->sortBy('id')
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'role' => $message->role,
                    'message' => $message->message,
                    'created_at' => $message->created_at?->toIso8601String(),
                    'metadata' => $message->metadata ?? [],
                ];
            })
            ->all();
    }
}

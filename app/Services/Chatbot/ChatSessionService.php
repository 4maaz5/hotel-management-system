<?php

namespace App\Services\Chatbot;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ChatSessionService
{
    private const PUBLIC_WEBSITE_SESSION_KEY = 'booking_website_chat_session_id';

    public function current(User $user, ?Request $request = null): ChatSession
    {
        return ChatSession::query()
            ->where('user_id', $user->id)
            ->latest('last_message_at')
            ->latest('id')
            ->first()
            ?? $this->create($user, $request);
    }

    public function resolve(User $user, ?int $sessionId = null, ?int $userId = null, ?Request $request = null): ChatSession
    {
        if ($userId !== null && $userId !== $user->id) {
            throw ValidationException::withMessages([
                'user_id' => 'The selected user_id does not match the authenticated user.',
            ]);
        }

        if ($sessionId) {
            $session = ChatSession::query()
                ->where('id', $sessionId)
                ->where('user_id', $user->id)
                ->first();

            if (! $session) {
                throw ValidationException::withMessages([
                    'session_id' => 'The selected session was not found.',
                ]);
            }

            return $session;
        }

        return $this->current($user, $request);
    }

    public function touch(ChatSession $session): void
    {
        $session->forceFill([
            'last_message_at' => now(),
        ])->save();
    }

    public function currentGuest(Request $request, ?int $propertyId = null): ChatSession
    {
        $sessionId = (int) $request->session()->get(self::PUBLIC_WEBSITE_SESSION_KEY);

        if ($sessionId > 0) {
            $session = ChatSession::query()
                ->whereKey($sessionId)
                ->whereNull('user_id')
                ->first();

            if ($session && ($propertyId === null || $session->property_id === null || (int) $session->property_id === $propertyId)) {
                return $session;
            }
        }

        $session = $this->createGuest($request, $propertyId);
        $request->session()->put(self::PUBLIC_WEBSITE_SESSION_KEY, $session->id);

        return $session;
    }

    public function resolveGuest(Request $request, ?int $sessionId = null, ?int $propertyId = null): ChatSession
    {
        $currentSessionId = (int) $request->session()->get(self::PUBLIC_WEBSITE_SESSION_KEY);

        if ($sessionId !== null && $sessionId > 0) {
            if ($currentSessionId !== $sessionId) {
                throw ValidationException::withMessages([
                    'session_id' => 'The selected session was not found.',
                ]);
            }

            $session = ChatSession::query()
                ->whereKey($sessionId)
                ->whereNull('user_id')
                ->first();

            if (! $session) {
                throw ValidationException::withMessages([
                    'session_id' => 'The selected session was not found.',
                ]);
            }

            return $session;
        }

        return $this->currentGuest($request, $propertyId);
    }

    public function ownsGuestSession(ChatSession $session, Request $request): bool
    {
        return $session->user_id === null
            && (int) $request->session()->get(self::PUBLIC_WEBSITE_SESSION_KEY) === (int) $session->id;
    }

    private function create(User $user, ?Request $request = null): ChatSession
    {
        return ChatSession::create([
            'user_id' => $user->id,
            'property_id' => $request?->session()->get('property_id') ?: $user->property_id,
            'language' => app()->getLocale() === 'ar' ? 'ar' : 'en',
            'status' => 'open',
            'context' => [],
            'last_message_at' => now(),
        ]);
    }

    private function createGuest(Request $request, ?int $propertyId = null): ChatSession
    {
        return ChatSession::create([
            'user_id' => null,
            'property_id' => $propertyId ?: $request->session()->get('property_id'),
            'language' => app()->getLocale() === 'ar' ? 'ar' : 'en',
            'status' => 'open',
            'context' => [
                'channel' => 'website',
            ],
            'last_message_at' => now(),
        ]);
    }
}

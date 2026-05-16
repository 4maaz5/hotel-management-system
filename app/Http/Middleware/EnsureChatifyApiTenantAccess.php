<?php

namespace App\Http\Middleware;

use App\Models\ChMessage;
use App\Models\User;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureChatifyApiTenantAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            app(TenantContext::class)->forget();

            return response()->json(['message' => 'Chat API is available for tenant users only.'], 403);
        }

        $tenant = $user->tenant;

        if (! $tenant || ! $tenant->isActiveSubscription()) {
            app(TenantContext::class)->forget();

            return response()->json([
                'message' => 'Your tenant subscription is inactive, suspended, or expired.',
            ], 403);
        }

        app(TenantContext::class)->setTenant($tenant);

        if (! $this->targetUserIsAllowed($request, (int) $user->id, (int) $tenant->getKey())) {
            return response()->json(['message' => 'Target user is not available for this tenant.'], 403);
        }

        if (! $this->channelIsAllowed($request, (int) $tenant->getKey())) {
            return response()->json(['message' => 'Channel is not available for this tenant.'], 403);
        }

        if (! $this->attachmentIsAllowed($request)) {
            return response()->json(['message' => 'Attachment was not found for this tenant.'], 404);
        }

        return $next($request);
    }

    protected function targetUserIsAllowed(Request $request, int $currentUserId, int $tenantId): bool
    {
        $targetUserId = $request->input('id', $request->input('user_id'));

        if ($targetUserId === null || ! is_numeric($targetUserId)) {
            return true;
        }

        $targetUserId = (int) $targetUserId;

        if ($targetUserId === $currentUserId) {
            return true;
        }

        return User::whereKey($targetUserId)
            ->where('company_id', $tenantId)
            ->exists();
    }

    protected function channelIsAllowed(Request $request, int $tenantId): bool
    {
        $channelName = (string) $request->input('channel_name', '');

        if ($channelName === '') {
            return true;
        }

        if (! preg_match('/^private-chatify\.(\d+)$/', $channelName, $matches)) {
            return true;
        }

        return User::whereKey((int) $matches[1])
            ->where('company_id', $tenantId)
            ->exists();
    }

    protected function attachmentIsAllowed(Request $request): bool
    {
        $fileName = $request->route('fileName');

        if (! is_string($fileName) || $fileName === '') {
            return true;
        }

        return ChMessage::where('attachment', 'like', '%"new_name":"'.$fileName.'"%')
            ->exists();
    }
}

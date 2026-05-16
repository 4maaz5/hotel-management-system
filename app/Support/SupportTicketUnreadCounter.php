<?php

namespace App\Support;

use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class SupportTicketUnreadCounter
{
    public function forSuperAdmin(): int
    {
        if (! $this->hasReadTrackingColumns()) {
            return 0;
        }

        return SupportTicket::query()
            ->where('last_sender_role', 'tenant')
            ->where($this->unreadSince('super_admin_last_read_at'))
            ->count();
    }

    public function forTenantArea(string $supportArea): int
    {
        if (! $this->hasReadTrackingColumns()) {
            return 0;
        }

        return SupportTicket::query()
            ->where('support_area', $supportArea)
            ->where('last_sender_role', 'super_admin')
            ->where($this->unreadSince('tenant_last_read_at'))
            ->count();
    }

    private function unreadSince(string $readColumn): callable
    {
        return function (Builder $query) use ($readColumn): void {
            $query->whereNull($readColumn)
                ->orWhereColumn('last_message_at', '>', $readColumn);
        };
    }

    private function hasReadTrackingColumns(): bool
    {
        return Schema::hasColumn('support_tickets', 'last_sender_role')
            && Schema::hasColumn('support_tickets', 'tenant_last_read_at')
            && Schema::hasColumn('support_tickets', 'super_admin_last_read_at');
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\CompanyDocument;
use App\Models\EmployeeDocument;
use App\Models\SystemNotification;
use App\Models\User;
use Carbon\Carbon;

class CheckExpiringDocuments
{
    public function checkDocuments(iterable $documents, Carbon $today, string $typeLabel): void
    {
        foreach ($documents as $document) {
            if (! $document->expiration_date) {
                continue;
            }

            $daysLeft = $today->diffInDays(Carbon::parse($document->expiration_date), false);

            if ($daysLeft > 30) {
                continue;
            }

            $status = $daysLeft < 0 ? 'Expired' : 'Expiring Soon';
            $message = sprintf(
                "%s '%s' is %s.",
                $typeLabel,
                $this->documentName($document),
                $status
            );

            foreach ($this->recipientsFor($document) as $recipient) {
                SystemNotification::updateOrCreate(
                    [
                        'recipient_type' => $recipient['type'],
                        'recipient_id' => $recipient['id'],
                        'message' => $message,
                    ],
                    [
                        'type' => 'system',
                        'status' => 'sent',
                        'scheduled_at' => now(),
                        'sent_at' => now(),
                        'created_by' => auth()->id(),
                    ]
                );
            }
        }
    }

    private function documentName(EmployeeDocument|CompanyDocument $document): string
    {
        return $document instanceof EmployeeDocument
            ? ($document->type ?: 'Employee Document')
            : ($document->name ?: $document->type ?: 'Company Document');
    }

    private function recipientsFor(EmployeeDocument|CompanyDocument $document): array
    {
        $recipients = User::role('super_admin')
            ->get()
            ->map(fn (User $user) => ['type' => 'super_admin', 'id' => $user->id])
            ->all();

        $branchId = $document instanceof EmployeeDocument
            ? $document->employee?->branch_id
            : $document->branch_id;

        if ($branchId) {
            $branchManagers = User::role('manager')
                ->where('branch_id', $branchId)
                ->get()
                ->map(fn (User $user) => ['type' => 'manager', 'id' => $user->id])
                ->all();

            $recipients = array_merge($recipients, $branchManagers);
        }

        return $recipients;
    }
}

<?php

namespace App\Support;

use App\Models\UserActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class UserActivityLogger
{
    public function log(
        string $module,
        string $action,
        ?Model $subject,
        string $description,
        array $beforeData = [],
        array $afterData = [],
        array $metadata = [],
        ?int $subjectId = null,
        ?string $subjectReference = null
    ): ?UserActivityLog {
        if (! Schema::hasTable('user_activity_logs')) {
            return null;
        }

        $tenantId = app(TenantContext::class)->id();
        $branchId = app(PropertyContext::class)->branchId();

        return UserActivityLog::create([
            'company_id' => $tenantId,
            'branch_id' => $branchId,
            'user_id' => Auth::id(),
            'module' => $module,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subjectId ?? $subject?->getKey(),
            'subject_reference' => $subjectReference ?? $this->referenceFor($subject),
            'description' => $description,
            'before_data' => $beforeData === [] ? null : $beforeData,
            'after_data' => $afterData === [] ? null : $afterData,
            'metadata' => $metadata === [] ? null : $metadata,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    private function referenceFor(?Model $subject): ?string
    {
        if (! $subject) {
            return null;
        }

        foreach (['reservation_number', 'invoice_number', 'voucher_number', 'property_code', 'name'] as $attribute) {
            $value = $subject->getAttribute($attribute);

            if ($value) {
                return (string) $value;
            }
        }

        return (string) $subject->getKey();
    }
}

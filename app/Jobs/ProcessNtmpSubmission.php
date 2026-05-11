<?php

namespace App\Jobs;

use App\Models\NtmpSubmission;
use App\Models\Scopes\CurrentPropertyScope;
use App\Models\Scopes\TenantScope;
use App\Services\Ntmp\NtmpService;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessNtmpSubmission implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int $submissionId,
    ) {
    }

    public function handle(NtmpService $ntmpService): void
    {
        $submission = NtmpSubmission::withoutGlobalScope(TenantScope::class)
            ->withoutGlobalScope(CurrentPropertyScope::class)
            ->find($this->submissionId);

        if (! $submission) {
            return;
        }

        app(TenantContext::class)->setTenantId($submission->tenant_id);
        app(PropertyContext::class)->setPropertyId($submission->property_id);

        $ntmpService->processSubmission($submission);
    }
}

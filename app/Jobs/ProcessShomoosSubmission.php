<?php

namespace App\Jobs;

use App\Models\Scopes\CurrentPropertyScope;
use App\Models\Scopes\TenantScope;
use App\Models\ShomoosSubmission;
use App\Models\Property;
use App\Services\Shomoos\ShomoosService;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessShomoosSubmission implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int $submissionId,
    ) {
    }

    public function handle(ShomoosService $shomoosService): void
    {
        $submission = ShomoosSubmission::withoutGlobalScope(TenantScope::class)
            ->withoutGlobalScope(CurrentPropertyScope::class)
            ->find($this->submissionId);

        if (! $submission) {
            return;
        }

        app(TenantContext::class)->setTenantId($submission->company_id);
        app(PropertyContext::class)->setProperty(
            Property::where('branch_id', $submission->branch_id)->first()
        );

        $shomoosService->processSubmission($submission);
    }
}

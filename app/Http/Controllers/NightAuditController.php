<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Models\NightAudit;
use App\Models\NightAuditSetting;
use App\Models\User;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NightAuditController extends Controller
{
    use ScopesTenantAccess;

    public function index(Request $request)
    {
        $settings = NightAuditSetting::getSettings();

        if ($request->filled('user_id')) {
            abort_unless($this->scopeUsersForUser(User::whereKey($request->user_id), $request->user())->exists(), 403);
        }

        $filters = [
            'status' => $request->get('status'),
            'user_id' => $request->get('user_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $audits = $this->scopeNightAuditsForUser(NightAudit::with('user'), $request->user())
            ->filter($filters)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $users = $this->scopeUsersForUser(User::query(), $request->user())->get();

        return view('admin.night_audits.index', compact('audits', 'filters', 'users', 'settings'));
    }

    public function start(Request $request)
    {
        $settings = NightAuditSetting::getSettings();
        [$companyId, $branchId] = $this->currentTenantAndBranch($request);

        if (! $settings->is_active) {
            return redirect()->back()
                ->with('error', __('dashboard.night_audit_not_enabled'));
        }

        if ($settings->allowance_period > 0) {
            $currentHour = Carbon::now()->hour;
            if ($currentHour < $settings->allowance_period) {
                return redirect()->back()
                    ->with('error', __('dashboard.night_audit_not_in_window', ['hour' => $settings->allowance_period]));
            }
        }

        $pendingAudit = $this->scopeNightAuditsForUser(NightAudit::where('status', 'pending'), $request->user())->first();
        if ($pendingAudit) {
            return redirect()->back()
                ->with('error', __('dashboard.night_audit_already_pending'));
        }

        $lastAudit = $this->scopeNightAuditsForUser(NightAudit::where('status', 'completed'), $request->user())
            ->orderBy('period_date_to', 'desc')
            ->first();

        $periodFrom = $lastAudit
            ? Carbon::parse($lastAudit->period_date_to)->addDay()
            : Carbon::now()->subDays(7)->startOfDay();

        $audit = NightAudit::create([
            'start_date_time' => now(),
            'period_date_from' => $periodFrom,
            'period_date_to' => Carbon::today(),
            'status' => 'pending',
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'user_id' => auth()->id(),
            'night_count' => Carbon::parse($periodFrom)->diffInDays(Carbon::today()) + 1,
        ]);

        return redirect()->back()
            ->with('success', __('dashboard.night_audit_started'));
    }

    public function complete(Request $request, $id)
    {
        $audit = $this->scopeNightAuditsForUser(NightAudit::query(), $request->user())->findOrFail($id);

        if ($audit->status !== 'pending') {
            return redirect()->back()
                ->with('error', __('dashboard.night_audit_cannot_complete'));
        }

        $financialSummary = NightAudit::calculateFinancialSummary(
            $audit->period_date_from,
            $audit->period_date_to,
            $audit->company_id,
            $audit->branch_id
        );

        $audit->update([
            'end_date_time' => now(),
            'status' => 'completed',
            'notes' => $request->notes,
            'financial_summary' => $financialSummary,
        ]);

        return redirect()->back()
            ->with('success', __('dashboard.night_audit_completed'));
    }

    public function fail(Request $request, $id)
    {
        $audit = $this->scopeNightAuditsForUser(NightAudit::query(), $request->user())->findOrFail($id);

        if ($audit->status !== 'pending') {
            return redirect()->back()
                ->with('error', __('dashboard.night_audit_cannot_fail'));
        }

        $audit->update([
            'end_date_time' => now(),
            'status' => 'failed',
            'notes' => $request->notes,
        ]);

        return redirect()->back()
            ->with('success', __('dashboard.night_audit_failed'));
    }

    public function download($id)
    {
        $audit = $this->scopeNightAuditsForUser(NightAudit::with('user'), auth()->user())->findOrFail($id);

        if ($audit->status !== 'completed' || ! $audit->financial_summary) {
            return redirect()->back()
                ->with('error', __('dashboard.night_audit_no_report'));
        }

        $summary = $audit->financial_summary;
        $property = \App\Models\Property::current();

        return view('admin.night_audits.report', compact('audit', 'summary', 'property'));
    }

    public function destroy($id)
    {
        $audit = $this->scopeNightAuditsForUser(NightAudit::query(), auth()->user())->findOrFail($id);
        $audit->delete();

        return redirect()->back()
            ->with('success', __('dashboard.night_audit_deleted'));
    }

    private function scopeNightAuditsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        [$companyId, $branchId] = $this->currentTenantAndBranch(request());

        return $query
            ->where('company_id', $companyId)
            ->when($branchId, fn ($scopedQuery) => $scopedQuery->where('branch_id', $branchId));
    }

    private function scopeUsersForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        [$companyId, $branchId] = $this->currentTenantAndBranch(request());

        return $query
            ->where('company_id', $companyId)
            ->when($branchId, fn ($scopedQuery) => $scopedQuery->where('branch_id', $branchId));
    }

    private function currentTenantAndBranch(Request $request): array
    {
        $companyId = app(TenantContext::class)->id() ?: $request->user()?->company_id;
        $branchId = app(PropertyContext::class)->branchId() ?: $request->user()?->branch_id;

        abort_unless($companyId, 403);

        return [(int) $companyId, $branchId ? (int) $branchId : null];
    }
}

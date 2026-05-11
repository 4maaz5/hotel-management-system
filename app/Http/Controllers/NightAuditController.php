<?php

namespace App\Http\Controllers;

use App\Models\NightAudit;
use App\Models\NightAuditSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NightAuditController extends Controller
{
    public function index(Request $request)
    {
        $settings = NightAuditSetting::getSettings();

        $filters = [
            'status' => $request->get('status'),
            'user_id' => $request->get('user_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $audits = NightAudit::with('user')
            ->filter($filters)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $users = \App\Models\User::all();

        return view('admin.night_audits.index', compact('audits', 'filters', 'users', 'settings'));
    }

    public function start(Request $request)
    {
        $settings = NightAuditSetting::getSettings();

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

        $pendingAudit = NightAudit::where('status', 'pending')->first();
        if ($pendingAudit) {
            return redirect()->back()
                ->with('error', __('dashboard.night_audit_already_pending'));
        }

        $lastAudit = NightAudit::where('status', 'completed')
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
            'user_id' => auth()->id(),
            'night_count' => Carbon::parse($periodFrom)->diffInDays(Carbon::today()) + 1,
        ]);

        return redirect()->back()
            ->with('success', __('dashboard.night_audit_started'));
    }

    public function complete(Request $request, $id)
    {
        $audit = NightAudit::findOrFail($id);

        if ($audit->status !== 'pending') {
            return redirect()->back()
                ->with('error', __('dashboard.night_audit_cannot_complete'));
        }

        $financialSummary = NightAudit::calculateFinancialSummary(
            $audit->period_date_from,
            $audit->period_date_to
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
        $audit = NightAudit::findOrFail($id);

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
        $audit = NightAudit::with('user')->findOrFail($id);

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
        $audit = NightAudit::findOrFail($id);
        $audit->delete();

        return redirect()->back()
            ->with('success', __('dashboard.night_audit_deleted'));
    }
}

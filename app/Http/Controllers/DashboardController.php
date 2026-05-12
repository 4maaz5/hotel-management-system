<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Income;
use App\Models\PaymentVoucher;
use App\Models\Property;
use App\Models\ReceiptVoucher;
use App\Models\Reservation;
use App\Models\Unit;
use App\Support\PropertyContext;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $currentProperty = app(PropertyContext::class)->property();

        // Quick Stats
        $todaysArrivals = Reservation::whereDate('check_in_date', $today)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->count();

        $todaysDepartures = Reservation::whereDate('check_out_date', $today)
            ->where('status', 'checked_in')
            ->count();

        $inHouse = Reservation::where('status', 'checked_in')->count();
        $checkedIn = Reservation::where('status', 'checked_in')->count();

        // Reservation Status
        $onArrival = Reservation::whereDate('check_in_date', $today)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->count();

        $checkedInCount = Reservation::where('status', 'checked_in')->count();

        $onDeparture = Reservation::whereDate('check_out_date', $today)
            ->where('status', 'checked_in')
            ->count();

        $checkedOut = Reservation::whereDate('check_out_date', $today)
            ->where('status', 'checked_out')
            ->count();

        // Financial Summary
        $totalRevenue = ReceiptVoucher::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereNotIn('status', ['cancelled'])
            ->sum('amount');

        $totalExpense = PaymentVoucher::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereNotIn('status', ['cancelled'])
            ->where('voucher_type', '!=', 'refund')
            ->sum('amount');

        // Occupancy
        $totalUnits = Unit::count();
        $occupiedUnits = Reservation::where('status', 'checked_in')->count();
        $vacantUnits = $totalUnits - $occupiedUnits;
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;

        // Housekeeping Status - compute based on reservations
        $occupiedUnitIds = Reservation::where('status', 'checked_in')->pluck('unit_id')->toArray();

        $hkVacantClean = Unit::whereNotIn('id', $occupiedUnitIds)
            ->where('housekeeping_status', 'clean')
            ->count();

        $hkVacantDirty = Unit::whereNotIn('id', $occupiedUnitIds)
            ->where('housekeeping_status', 'dirty')
            ->count();

        $hkOccupiedClean = Unit::whereIn('id', $occupiedUnitIds)
            ->where('housekeeping_status', 'clean')
            ->count();

        $hkOccupiedDirty = Unit::whereIn('id', $occupiedUnitIds)
            ->where('housekeeping_status', 'dirty')
            ->count();

        $hkMaintenance = Unit::where('housekeeping_status', 'maintenance')
            ->count();

        $hkBlocked = Unit::where('housekeeping_status', 'blocked')
            ->count();

        // Bottom Stats
        $totalProperties = Property::count();
        $totalGuests = Guest::count();

        return view('dashboard', compact(
            'todaysArrivals',
            'todaysDepartures',
            'inHouse',
            'checkedIn',
            'onArrival',
            'checkedInCount',
            'onDeparture',
            'checkedOut',
            'totalRevenue',
            'totalExpense',
            'totalUnits',
            'occupiedUnits',
            'vacantUnits',
            'occupancyRate',
            'hkVacantClean',
            'hkVacantDirty',
            'hkOccupiedClean',
            'hkOccupiedDirty',
            'hkMaintenance',
            'hkBlocked',
            'totalProperties',
            'totalGuests',
            'currentProperty'
        ));
    }

    public function program()
    {
        return view('hr-program');
    }

    public function incomeChartData(Request $request)
    {
        $user = auth()->user();
        $type = $request->query('type', 'monthly');

        $query = Income::query();

        if (!$user->hasRole('super_admin')) {
            if ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            } else {
                $query->whereHas('branch', fn ($q) => $q->where('company_id', $user->company_id));
            }
        }

        switch ($type) {
            case 'daily':
                $query->whereMonth('income_date', now()->month)
                    ->whereYear('income_date', now()->year);
                $results = $query->get()->groupBy(fn ($i) => \Carbon\Carbon::parse($i->income_date)->format('Y-m-d'));
                $labels = collect();
                $start = now()->startOfMonth();
                $end = now()->endOfMonth();
                for ($d = $start; $d->lte($end); $d->addDay()) {
                    $labels->push($d->format('Y-m-d'));
                }
                break;

            case 'weekly':
                $query->whereYear('income_date', now()->year);
                $results = $query->get()->groupBy(fn ($i) => \Carbon\Carbon::parse($i->income_date)->format('W'));
                $labels = collect();
                for ($w = 1; $w <= 53; $w++) {
                    $labels->push('Week ' . $w);
                }
                break;

            case 'yearly':
                $query->where('income_date', '>=', now()->subYears(5)->startOfYear());
                $results = $query->get()->groupBy(fn ($i) => \Carbon\Carbon::parse($i->income_date)->format('Y'));
                $labels = collect(range(now()->subYears(4)->year, now()->year))->map(fn ($y) => (string) $y);
                break;

            case 'monthly':
            default:
                $query->whereYear('income_date', now()->year);
                $results = $query->get()->groupBy(fn ($i) => \Carbon\Carbon::parse($i->income_date)->format('Y-m'));
                $labels = collect();
                for ($m = 1; $m <= 12; $m++) {
                    $labels->push(now()->month($m)->format('Y-m'));
                }
                break;
        }

        $totals = $labels->map(fn ($label) => [
            'label' => $label,
            'total' => (float) ($results[$label] ?? collect())->sum('amount'),
        ]);

        return response()->json($totals);
    }
}

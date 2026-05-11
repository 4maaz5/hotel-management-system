<?php

namespace App\Http\Controllers\Housekeeping;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Reservation;
use App\Models\Floor;
use App\Models\Block;
use App\Models\UnitTypeCustomization;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $floors = Floor::all();
        $blocks = Block::all();
        $unitTypes = UnitTypeCustomization::all();

        $query = Unit::with(['unitType', 'floor', 'block', 'unitClass'])
            ->where('is_active', true);

        // Filters
        if ($request->floor_id) {
            $query->where('floor_id', $request->floor_id);
        }
        if ($request->block_id) {
            $query->where('block_id', $request->block_id);
        }
        if ($request->unit_type_id) {
            $query->where('unit_type_id', $request->unit_type_id);
        }
        if ($request->unit_number) {
            $query->where('unit_number', 'like', '%' . $request->unit_number . '%');
        }
        if ($request->housekeeping_status) {
            $query->where('housekeeping_status', $request->housekeeping_status);
        }

        // Build a base query for counts (excluding housekeeping_status filter to get all status counts)
        $baseQuery = Unit::with(['unitType', 'floor', 'block', 'unitClass'])->where('is_active', true);
        if ($request->floor_id) {
            $baseQuery->where('floor_id', $request->floor_id);
        }
        if ($request->block_id) {
            $baseQuery->where('block_id', $request->block_id);
        }
        if ($request->unit_type_id) {
            $baseQuery->where('unit_type_id', $request->unit_type_id);
        }
        if ($request->unit_number) {
            $baseQuery->where('unit_number', 'like', '%' . $request->unit_number . '%');
        }
        $dirtyCount = (clone $baseQuery)->where('housekeeping_status', 'dirty')->count();
        $cleanCount = (clone $baseQuery)->where('housekeeping_status', 'clean')->count();
        $inspectedCount = (clone $baseQuery)->where('housekeeping_status', 'inspected')->count();
        $outOfServiceCount = (clone $baseQuery)->where('housekeeping_status', 'out_of_service')->count();

        $units = $query->orderBy('floor_id')->orderBy('unit_number')->paginate(20);

        // Get active reservations (pending, confirmed, or checked_in for today)
        $activeReservations = Reservation::whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->whereDate('check_in_date', '<=', $today)
            ->whereDate('check_out_date', '>', $today)
            ->with(['guest'])
            ->get()
            ->keyBy('unit_id');

        // Get check-outs today
        $checkOutToday = Reservation::where('status', 'checked_in')
            ->whereDate('check_out_date', $today)
            ->get()
            ->keyBy('unit_id');

        // Process units with status (preserve pagination using through)
        $unitsWithStatus = $units->through(function ($unit) use ($activeReservations, $checkOutToday, $today) {
            $reservation = $activeReservations->get($unit->id);
            $checkout = $checkOutToday->get($unit->id);

            $unit->is_occupied = $reservation ? true : false;
            $unit->current_guest = $reservation ? ($reservation->guest ? $reservation->guest->first_name . ' ' . $reservation->guest->last_name : 'Walk-in') : null;
            $unit->check_in_date = $reservation ? \Carbon\Carbon::parse($reservation->check_in_date)->format('Y-m-d') : null;
            $unit->check_out_date = $checkout ? \Carbon\Carbon::parse($checkout->check_out_date)->format('Y-m-d') : ($reservation ? \Carbon\Carbon::parse($reservation->check_out_date)->format('Y-m-d') : null);
            $unit->notes = $reservation ? ($reservation->notes ?? '') : '';

            // Determine occupancy status
            if ($checkout) {
                $unit->occupancy_status = 'check_out_today';
            } elseif ($reservation) {
                $unit->occupancy_status = 'occupied';
            } else {
                $unit->occupancy_status = 'vacant';
            }

            return $unit;
        });

        $printingOption = \App\Models\PrintingOption::where('report_key', 'housekeeping_status')->first();

        return view('admin.housekeeping_status.index', compact(
            'unitsWithStatus',
            'floors',
            'blocks',
            'unitTypes',
            'dirtyCount',
            'cleanCount',
            'inspectedCount',
            'outOfServiceCount',
            'printingOption'
        ));
    }

    public function updateStatus(Request $request, Unit $unit)
    {
        $request->validate([
            'status' => 'required|in:clean,dirty,inspected,out_of_service'
        ]);

        $unit->update(['housekeeping_status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Housekeeping status updated successfully'
        ]);
    }

    public function print(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $floors = Floor::all();
        $blocks = Block::all();
        $unitTypes = UnitTypeCustomization::all();
        $property = Property::current(['commercialDetail']);

        $query = Unit::with(['unitType', 'floor', 'block', 'unitClass'])
            ->where('is_active', true);

        // Apply same filters as index
        if ($request->floor_id) {
            $query->where('floor_id', $request->floor_id);
        }
        if ($request->block_id) {
            $query->where('block_id', $request->block_id);
        }
        if ($request->unit_type_id) {
            $query->where('unit_type_id', $request->unit_type_id);
        }
        if ($request->unit_number) {
            $query->where('unit_number', 'like', '%' . $request->unit_number . '%');
        }
        if ($request->housekeeping_status) {
            $query->where('housekeeping_status', $request->housekeeping_status);
        }

        $units = $query->orderBy('floor_id')->orderBy('unit_number')->get();

        $activeReservations = Reservation::whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->whereDate('check_in_date', '<=', $today)
            ->whereDate('check_out_date', '>', $today)
            ->with(['guest'])
            ->get()
            ->keyBy('unit_id');

        $checkOutToday = Reservation::where('status', 'checked_in')
            ->whereDate('check_out_date', $today)
            ->get()
            ->keyBy('unit_id');

        $unitsWithStatus = $units->map(function ($unit) use ($activeReservations, $checkOutToday) {
            $reservation = $activeReservations->get($unit->id);
            $checkout = $checkOutToday->get($unit->id);

            $unit->is_occupied = $reservation ? true : false;
            $unit->current_guest = $reservation ? ($reservation->guest ? $reservation->guest->first_name . ' ' . $reservation->guest->last_name : 'Walk-in') : null;
            $unit->check_in_date = $reservation ? \Carbon\Carbon::parse($reservation->check_in_date)->format('Y-m-d') : null;
            $unit->check_out_date = $checkout ? \Carbon\Carbon::parse($checkout->check_out_date)->format('Y-m-d') : ($reservation ? \Carbon\Carbon::parse($reservation->check_out_date)->format('Y-m-d') : null);
            $unit->notes = $reservation ? ($reservation->notes ?? '') : '';

            if ($checkout) {
                $unit->occupancy_status = 'check_out_today';
            } elseif ($reservation) {
                $unit->occupancy_status = 'occupied';
            } else {
                $unit->occupancy_status = 'vacant';
            }

            return $unit;
        });

        $dirtyCount = $unitsWithStatus->where('housekeeping_status', 'dirty')->count();
        $cleanCount = $unitsWithStatus->where('housekeeping_status', 'clean')->count();
        $inspectedCount = $unitsWithStatus->where('housekeeping_status', 'inspected')->count();
        $outOfServiceCount = $unitsWithStatus->where('housekeeping_status', 'out_of_service')->count();

        // Get printing options
        $printingOption = \App\Models\PrintingOption::where('report_key', 'housekeeping_status')->first();
        $globalSetting = \App\Models\PrintingOption::first();

        $filtersApplied = $request->hasAny(['floor_id', 'block_id', 'unit_type_id', 'unit_number', 'housekeeping_status']);

        return view('admin.housekeeping_status.print', compact(
            'unitsWithStatus',
            'floors',
            'blocks',
            'unitTypes',
            'property',
            'dirtyCount',
            'cleanCount',
            'inspectedCount',
            'outOfServiceCount',
            'filtersApplied',
            'printingOption',
            'globalSetting'
        ));
    }
}

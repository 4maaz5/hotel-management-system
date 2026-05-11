<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Reservation;
use App\Models\Floor;
use App\Models\Block;
use App\Models\UnitTypeCustomization;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UnitStatusController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

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

        $units = $query->orderBy('floor_id')->orderBy('unit_number')->get();

        // Get active reservations for today
        $activeReservations = Reservation::whereNotIn('status', ['cancelled', 'checked_out'])
            ->where('check_in_date', '<=', $today)
            ->where('check_out_date', '>', $today)
            ->with(['guest'])
            ->get()
            ->keyBy('unit_id');

        // Get upcoming check-ins and check-outs
        $checkInToday = Reservation::whereNotIn('status', ['cancelled', 'checked_in', 'checked_out'])
            ->where('check_in_date', $today)
            ->count();

        $checkOutToday = Reservation::where('status', 'checked_in')
            ->where('check_out_date', $today)
            ->count();

        // Calculate counts
        $occupiedUnitIds = $activeReservations->keys()->toArray();
        $vacantCount = $units->whereNotIn('id', $occupiedUnitIds)->count();
        $occupiedCount = count($occupiedUnitIds);

        // Process units with status
        $unitsWithStatus = $units->map(function ($unit) use ($activeReservations, $today) {
            $reservation = $activeReservations->get($unit->id);

            $unit->is_occupied = $reservation ? true : false;
            $unit->current_guest = $reservation ? ($reservation->guest ? $reservation->guest->first_name . ' ' . $reservation->guest->last_name : 'Walk-in') : null;
            $unit->balance = $reservation ? ($reservation->balance ?? 0) : 0;
            $unit->check_out_date = $reservation ? $reservation->check_out_date : null;

            return $unit;
        });

        return view('admin.unit_status.index', compact(
            'unitsWithStatus',
            'floors',
            'blocks',
            'unitTypes',
            'vacantCount',
            'occupiedCount',
            'checkInToday',
            'checkOutToday'
        ));
    }

    /**
     * Update unit housekeeping status
     */
    public function updateStatus(Request $request, $unitId)
    {
        try {
            $unit = Unit::findOrFail($unitId);

            $validated = $request->validate([
                'housekeeping_status' => 'required|in:clean,dirty,inspected,out_of_service'
            ]);

            $unit->update([
                'housekeeping_status' => $validated['housekeeping_status']
            ]);

            return response()->json([
                'success' => true,
                'message' => __('messages.unit_status_updated_successfully'),
                'status' => $unit->housekeeping_status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_updating_unit_status')
            ], 400);
        }
    }
}

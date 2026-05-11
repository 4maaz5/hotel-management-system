<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        $shifts = Shift::all();

        return view('Admin.Backend.Shift.index', compact('branches', 'shifts'));
    }

    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'error' => $validator->errors(),
            ]);
        }

        // Store shift
        $shift = Shift::create([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return response()->json([
            'status' => 1,
            'message' => __('messages.shift_created_successfully'),
            'data' => $shift,
        ]);
    }

    public function update(Request $request)
    {
        // Validate request (accept any time string)
        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|exists:shifts,id',
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'error' => $validator->errors(),
            ]);
        }

        // Normalize times to 24-hour H:i format
        $start = date('H:i', strtotime($request->start_time));
        $end = date('H:i', strtotime($request->end_time));

        // Fetch shift
        $shift = Shift::find($request->shift_id);

        // Update shift
        $shift->update([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
            'start_time' => $start,
            'end_time' => $end,
        ]);

        return response()->json([
            'status' => 1,
            'message' => __('messages.shift_updated_successfully'),
            'data' => $shift,
        ]);
    }

    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return response()->json([
            'status' => 1,
            'message' => __('messages.shift_deleted_successfully'),
        ]);
    }
}

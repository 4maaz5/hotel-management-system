<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShiftController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $branches = $this->scopeBranchesForUser(Branch::query(), $user)->get();
        $shifts = Shift::whereIn('branch_id', $branches->pluck('id'))->get();

        return view('Admin.Backend.Shift.index', compact('branches', 'shifts'));
    }

    public function store(Request $request)
    {
        // Validate request
        $branchIds = $this->accessibleBranchIds(auth()->user());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->when(
                    $branchIds !== null,
                    fn ($rule) => $rule->where(fn ($query) => $query->whereIn('id', $branchIds))
                ),
            ],
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
        $branchIds = $this->accessibleBranchIds(auth()->user());
        $validator = Validator::make($request->all(), [
            'shift_id' => 'required|exists:shifts,id',
            'name' => 'required|string|max:255',
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->when(
                    $branchIds !== null,
                    fn ($rule) => $rule->where(fn ($query) => $query->whereIn('id', $branchIds))
                ),
            ],
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
        $shift = Shift::whereIn('branch_id', $this->scopeBranchesForUser(Branch::query(), auth()->user())->pluck('id'))
            ->find($request->shift_id);

        if (! $shift) {
            return response()->json(['status' => 0, 'error' => ['shift_id' => ['Invalid shift.']]], 403);
        }

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
        $branchIds = $this->scopeBranchesForUser(Branch::query(), auth()->user())->pluck('id');
        $shift = Shift::whereIn('branch_id', $branchIds)->findOrFail($id);
        $shift->delete();

        return response()->json([
            'status' => 1,
            'message' => __('messages.shift_deleted_successfully'),
        ]);
    }
}

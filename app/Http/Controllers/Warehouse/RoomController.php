<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            $warehouses = Warehouse::all();
            $rooms = Room::all();
        } elseif ($user->branch_id) {
            $warehouses = Warehouse::where('branch_id', $user->branch_id)->get();
            $rooms = Room::whereHas('warehouse', fn ($q) => $q->where('branch_id', $user->branch_id))->get();
        } else {
            $warehouses = Warehouse::whereHas('branch.company', fn ($q) => $q->where('id', $user->company_id))->get();
            $rooms = Room::whereHas('warehouse.branch.company', fn ($q) => $q->where('id', $user->company_id))->get();
        }

        return view('Admin.Backend.Room.index', compact('rooms', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        $room = Room::create([
            'name' => $validated['room_name'],
            'warehouse_id' => $validated['warehouse_id'],
        ]);
        $warehouseName = $room->warehouse->name;

        return response()->json([
            'success' => true,
            'message' => __('messages.section_created_successfully'),
            'data' => [
                'id' => $room->id,
                'name' => $room->name,
                'branch' => $warehouseName,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        $room = Room::findOrFail($request->id);
        $room->name = $request->name;
        $room->warehouse_id = $request->warehouse_id;
        $room->save();

        return response()->json([
            'success' => true,
            'message' => __('messages.section_updated_successfully'),
            'data' => [
                'id' => $room->id,
                'name' => $room->name,
                'branch_id' => $room->warehouse_id,
                'branch' => $room->warehouse->name,
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $room = Room::findOrFail($request->room_id);
        $room->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.section_deleted_successfully'),
            'data' => [
                'id' => $room->id,
            ],
        ]);
    }
}

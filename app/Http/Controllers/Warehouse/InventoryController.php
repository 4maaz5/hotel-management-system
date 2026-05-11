<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Room;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with('warehouse', 'category', 'room')->get();
        $warehouses = Warehouse::all();
        $sections = Room::all();
        $products = Product::all();

        return view('Admin.Backend.Inventory.index', compact('inventories', 'warehouses', 'sections', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'room_id' => 'nullable|exists:rooms,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        Inventory::updateOrCreate(
            [
                'warehouse_id' => $request->warehouse_id,
                'room_id' => $request->section_id,
                'product_id' => $request->product_id,
            ],
            [
                'quantity' => DB::raw("quantity + {$request->quantity}"),
            ]
        );

        // return response()->json(['success' => true, 'message' => 'Stock added successfully']);
        return redirect()->back()->with(['success' => __('messages.stock_added_successfully')]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:inventories,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'section_id' => 'nullable|exists:rooms,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $inventory = \App\Models\Inventory::findOrFail($request->id);

        // Check unique constraint for warehouse + section + product
        $exists = \App\Models\Inventory::where('warehouse_id', $request->warehouse_id)
            ->where('room_id', $request->section_id)
            ->where('product_id', $request->product_id)
            ->where('id', '!=', $inventory->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => __('dashboard.inventory_already_exists'),
            ], 422);
        }

        // Update inventory
        $inventory->update([
            'warehouse_id' => $request->warehouse_id,
            'room_id' => $request->section_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => __('dashboard.inventory_updated_successfully'),
        //     'data' => $inventory->load('warehouse', 'section', 'product'),
        // ]);
        return redirect()->back()->with(['success' => __('messages.stock_updated_successfully')]);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $inventory = \App\Models\Inventory::find($id);

        if (! $inventory) {
            return response()->json([
                'success' => false,
                'message' => __('dashboard.inventory_not_found'),
            ], 404);
        }

        $inventory->delete();

        // return response()->json([
        //     'success' => true,
        //     'message' => __('dashboard.inventory_deleted_successfully'),
        // ]);
        return redirect()->back()->with(['delete' => __('messages.stock_deleted_successfully')]);
    }
}

<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\StockRequestItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            $branches = Branch::with('brand', 'company')->get();
            $warehouses = Warehouse::all();
        } elseif ($user->branch_id) {
            $branches = Branch::with('brand', 'company')
                ->where('id', $user->branch_id)->get();
            $warehouses = Warehouse::where('branch_id', $user->branch_id)->get();
        } else {
            $branches = Branch::with('brand', 'company')
                ->whereHas('company', fn ($q) => $q->where('id', $user->company_id))->get();
            $warehouses = Warehouse::whereHas('branch.company', fn ($q) => $q->where('id', $user->company_id))->get();
        }

        return view(
            'Admin.Backend.Warehouse.index',
            compact('branches', 'warehouses')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_name' => 'required|string|max:255',
            'type' => 'required|in:main,branch',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        //  MAIN warehouse logic
        if ($validated['type'] === 'main') {

            // Only ONE main warehouse allowed
            if (Warehouse::where('type', 'main')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Main warehouse already exists.',
                ], 422);
            }

            $validated['branch_id'] = null;
        }

        // BRANCH warehouse logic
        if ($validated['type'] === 'branch') {

            if (empty($validated['branch_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch is required for branch warehouse.',
                ], 422);
            }

            // Only ONE warehouse per branch
            if (Warehouse::where('branch_id', $validated['branch_id'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This branch already has a warehouse.',
                ], 422);
            }
        }

        $warehouse = Warehouse::create([
            'name' => $validated['warehouse_name'],
            'branch_id' => $validated['branch_id'],
            'type' => $validated['type'],
        ]);

        $branchName = $warehouse->branch?->name ?? 'Main Warehouse';

        return response()->json([
            'success' => true,
            'message' => __('messages.warehouse_created_successfully'),
            'data' => [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'branch' => $branchName,
                'type' => $warehouse->type,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:warehouses,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:main,branch',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $warehouse = Warehouse::findOrFail($request->id);

        // Main warehouse logic
        if ($request->type === 'main') {
            // Only one main warehouse allowed (ignore current warehouse)
            $exists = Warehouse::where('type', 'main')
                ->where('id', '!=', $warehouse->id)
                ->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Main warehouse already exists.',
                ], 422);
            }

            $warehouse->branch_id = null; // Main warehouse has no branch
        }

        // Branch warehouse logic
        if ($request->type === 'branch') {
            if (empty($request->branch_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch is required for branch warehouse.',
                ], 422);
            }

            // Only one warehouse per branch (ignore current warehouse)
            $exists = Warehouse::where('branch_id', $request->branch_id)
                ->where('id', '!=', $warehouse->id)
                ->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This branch already has a warehouse.',
                ], 422);
            }

            $warehouse->branch_id = $request->branch_id;
        }

        // Update name and type
        $warehouse->name = $request->name;
        $warehouse->type = $request->type;
        $warehouse->save();

        return response()->json([
            'success' => true,
            'message' => __('dashboard.warehouse_updated_successfully'),
            'data' => [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'branch_id' => $warehouse->branch_id,
                'branch' => $warehouse->branch?->name ?? 'Main Warehouse',
                'type' => $warehouse->type,
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        $warehouse = Warehouse::findOrFail($request->warehouse_id);
        $warehouse->delete();

        return response()->json([
            'success' => true,
            'message' => __('dashboard.warehouse_deleted_successfully'),
            'data' => [
                'id' => $warehouse->id,
            ],
        ]);
    }

    public function report($warehouseId)
    {
        $warehouse = Warehouse::findOrFail($warehouseId);

        // Current inventory
        // $inventories = Inventory::with('product')
        //     ->where('warehouse_id', $warehouseId)
        //     ->get();
        $inventories = Inventory::with(['product', 'room'])
            ->where('warehouse_id', $warehouseId)
            ->orderBy('room_id')
            ->get();

        $totalProducts = $inventories->count();
        $totalQuantity = $inventories->sum('quantity');

        // Stock dispatched to branches
        $dispatched = StockRequestItem::whereHas('stockRequest', function ($q) {
            $q->where('status', 'dispatched');
        })
            ->whereHas('product.inventories', function ($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->with('product', 'stockRequest.branch')
            ->get();

        return view('Admin.Backend.partials.report', compact(
            'warehouse',
            'inventories',
            'totalProducts',
            'totalQuantity',
            'dispatched'
        ));
    }
}

<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Room;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $inventories = Inventory::with('warehouse', 'product.category', 'room')
            ->whereHas('warehouse', fn ($query) => $this->scopeWarehousesForUser($query, $user))
            ->get();
        $products = $this->scopeVisibleProductsForUser(Product::query(), $user)->get();

        $warehouses = $this->scopeWarehousesForUser(Warehouse::query(), $user)->get();
        $sections = Room::whereHas('warehouse', fn ($query) => $this->scopeWarehousesForUser($query, $user))->get();

        return view('Admin.Backend.Inventory.index', compact('inventories', 'warehouses', 'sections', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => [
                'required',
                Rule::exists('warehouses', 'id')->where(fn ($query) => $this->scopeWarehousesForUser($query, $request->user())),
            ],
            'section_id' => [
                'nullable',
                Rule::exists('rooms', 'id')->where(
                    fn ($query) => $query->whereIn(
                        'warehouse_id',
                        $this->scopeWarehousesForUser(Warehouse::query(), $request->user())->select('id')
                    )
                ),
            ],
            'product_id' => [
                'required',
                Rule::exists('products', 'id')->where(fn ($query) => $this->scopeVisibleProductsForUser($query, $request->user())),
            ],
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        abort_unless($this->userCanAccessWarehouse((int) $request->warehouse_id, $user), 403);
        if ($request->filled('section_id')) {
            abort_unless(
                Room::whereKey($request->section_id)
                    ->where('warehouse_id', $request->warehouse_id)
                    ->whereHas('warehouse', fn ($query) => $this->scopeWarehousesForUser($query, $user))
                    ->exists(),
                403
            );
        }

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
            'warehouse_id' => [
                'required',
                Rule::exists('warehouses', 'id')->where(fn ($query) => $this->scopeWarehousesForUser($query, $request->user())),
            ],
            'section_id' => [
                'nullable',
                Rule::exists('rooms', 'id')->where(
                    fn ($query) => $query->whereIn(
                        'warehouse_id',
                        $this->scopeWarehousesForUser(Warehouse::query(), $request->user())->select('id')
                    )
                ),
            ],
            'product_id' => [
                'required',
                Rule::exists('products', 'id')->where(fn ($query) => $this->scopeVisibleProductsForUser($query, $request->user())),
            ],
            'quantity' => 'required|integer|min:0',
        ]);

        $inventory = Inventory::whereHas('warehouse', fn ($query) => $this->scopeWarehousesForUser($query, $request->user()))
            ->findOrFail($request->id);
        $user = auth()->user();
        abort_unless($this->userCanAccessWarehouse((int) $request->warehouse_id, $user), 403);
        abort_unless($this->userCanAccessWarehouse((int) $inventory->warehouse_id, $user), 403);
        if ($request->filled('section_id')) {
            abort_unless(
                Room::whereKey($request->section_id)
                    ->where('warehouse_id', $request->warehouse_id)
                    ->whereHas('warehouse', fn ($query) => $this->scopeWarehousesForUser($query, $user))
                    ->exists(),
                403
            );
        }

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
        $inventory = \App\Models\Inventory::whereHas(
            'warehouse',
            fn ($query) => $this->scopeWarehousesForUser($query, auth()->user())
        )->find($id);

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

    private function scopeVisibleProductsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->where(function ($productQuery) use ($user) {
            $productQuery->whereNull('company_id')
                ->orWhere('company_id', $user->company_id);
        });
    }
}

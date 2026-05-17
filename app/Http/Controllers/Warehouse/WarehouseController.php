<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Inventory;
use App\Models\StockRequestItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = Auth::user();

        $branches = $this->scopeBranchesForUser(Branch::with('brand', 'company'), $user)->get();
        $warehouses = $this->scopeWarehousesForUser(Warehouse::with('branch.company'), $user)->get();
        $companies = $this->isSuperAdmin($user)
            ? Company::orderBy('name')->get()
            : Company::whereKey($user->company_id)->get();

        return view(
            'Admin.Backend.Warehouse.index',
            compact('branches', 'warehouses', 'companies')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_name' => 'required|string|max:255',
            'type' => 'required|in:main,branch',
            'branch_id' => [
                'nullable',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $request->user())),
            ],
            'company_id' => [
                'nullable',
                $this->isSuperAdmin($request->user())
                    ? Rule::exists('companies', 'id')
                    : Rule::exists('companies', 'id')->where(fn ($query) => $query->where('id', $request->user()->company_id)),
            ],
        ]);

        $user = Auth::user();
        $companyId = $this->isSuperAdmin($user)
            ? ($validated['company_id'] ?? null)
            : $user->company_id;

        //  MAIN warehouse logic
        if ($validated['type'] === 'main') {
            if (! $companyId || $user->branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'A main warehouse must belong to a company account.',
                ], 422);
            }

            // Only ONE main warehouse per company
            if (Warehouse::where('company_id', $companyId)->where('type', 'main')->exists()) {
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

            if (! $this->userCanAccessBranch((int) $validated['branch_id'], $user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected branch is not available for this account.',
                ], 403);
            }

            $companyId = Branch::whereKey($validated['branch_id'])->value('company_id');

            // Only ONE warehouse per branch
            if (Warehouse::where('branch_id', $validated['branch_id'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This branch already has a warehouse.',
                ], 422);
            }
        }

        $warehouse = Warehouse::create([
            'company_id' => $companyId,
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
                'company_id' => $warehouse->company_id,
                'branch' => $branchName,
                'branch_id' => $warehouse->branch_id,
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
            'branch_id' => [
                'nullable',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $request->user())),
            ],
            'company_id' => [
                'nullable',
                $this->isSuperAdmin($request->user())
                    ? Rule::exists('companies', 'id')
                    : Rule::exists('companies', 'id')->where(fn ($query) => $query->where('id', $request->user()->company_id)),
            ],
        ]);

        $user = Auth::user();
        $warehouse = $this->scopeWarehousesForUser(Warehouse::query(), $user)->findOrFail($request->id);
        $companyId = $this->isSuperAdmin($user)
            ? ($request->company_id ?? $warehouse->company_id)
            : $user->company_id;

        // Main warehouse logic
        if ($request->type === 'main') {
            if (! $companyId || $user->branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'A main warehouse must belong to a company account.',
                ], 422);
            }

            // Only one main warehouse per company (ignore current warehouse)
            $exists = Warehouse::where('type', 'main')
                ->where('company_id', $companyId)
                ->where('id', '!=', $warehouse->id)
                ->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Main warehouse already exists.',
                ], 422);
            }

            $warehouse->branch_id = null; // Main warehouse has no branch
            $warehouse->company_id = $companyId;
        }

        // Branch warehouse logic
        if ($request->type === 'branch') {
            if (empty($request->branch_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch is required for branch warehouse.',
                ], 422);
            }

            if (! $this->userCanAccessBranch((int) $request->branch_id, $user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected branch is not available for this account.',
                ], 403);
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
            $warehouse->company_id = Branch::whereKey($request->branch_id)->value('company_id');
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
                'company_id' => $warehouse->company_id,
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

        $warehouse = $this->scopeWarehousesForUser(Warehouse::query(), Auth::user())->findOrFail($request->warehouse_id);
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
        $warehouse = $this->scopeWarehousesForUser(Warehouse::query(), Auth::user())->findOrFail($warehouseId);

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

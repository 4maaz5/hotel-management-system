<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\StockRequest;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = Auth::user();

        // Products (all users can see all products)
        $products = Product::all();

        // Stock requests
        $requestsQuery = StockRequest::with('items.product', 'branch')
            ->orderBy('created_at', 'desc');

        $this->scopeStockRequestsForUser($requestsQuery, $user);

        $requests = $requestsQuery->get();

        return view('Admin.Backend.Request.index', compact('products', 'requests'));
    }

    public function store(Request $request)
    {
        // 1. Check sender has a branch
        if (! auth()->user()->branch_id) {
            return redirect()->back()->with('delete', __('messages.no_branch_assigned'));
        }

        // 2. Validate input
        $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // 3. Create stock_request row
        $stockRequest = StockRequest::create([
            'branch_id' => auth()->user()->branch_id,
            'requested_by' => auth()->id(),
            'status' => 'pending', // default
        ]);

        // 3. Create stock_request_items rows
        foreach ($request->products as $item) {
            $stockRequest->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Stock request submitted successfully',
        //     'request_id' => $stockRequest->id,
        // ]);
        return redirect()->back()->with(['success' => __('messages.request_send_successfully')]);
    }

    public function approve($id)
    {
        $user = Auth::user();
        $request = $this->scopeStockRequestsForUser(StockRequest::query(), $user)->findOrFail($id);

        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be approved.');
        }

        $request->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', __('messages.stock_request_approved'));
    }

    // public function dispatch($id)
    // {
    //     $request = StockRequest::with(['items', 'branch'])->findOrFail($id);

    //     if ($request->status !== 'approved') {
    //         return back()->with('danger', 'Only approved requests can be dispatched.');
    //     }

    //     //  Get main warehouse dynamically
    //     $mainWarehouse = Warehouse::where('type', 'main')
    //         ->whereNull('branch_id')
    //         ->first();

    //     //  Get branch warehouse (each branch has ONE)
    //     $branchWarehouse = Warehouse::where('branch_id', $request->branch_id)
    //         ->where('type', 'branch')
    //         ->firstOrFail();
    //     foreach ($request->items as $item) {

    //         //  Deduct from main warehouse
    //         $mainInventory = Inventory::where('warehouse_id', $mainWarehouse->id)
    //             ->where('product_id', $item->product_id)
    //             ->lockForUpdate() // prevents race condition
    //             ->first();

    //         if (! $mainInventory || $mainInventory->quantity < $item->quantity) {
    //             return back()->with('danger', 'Not enough stock in main warehouse.');
    //         }

    //         $mainInventory->decrement('quantity', $item->quantity);

    //         //  Add to branch warehouse
    //         $branchInventory = Inventory::firstOrCreate(
    //             [
    //                 'warehouse_id' => $branchWarehouse->id,
    //                 'product_id' => $item->product_id,
    //             ],
    //             ['quantity' => 0]
    //         );

    //         $branchInventory->increment('quantity', $item->quantity);
    //     }

    //     //  Update request status
    //     $request->update([
    //         'status' => 'dispatched',
    //         'dispatched_by' => auth()->id(),
    //     ]);

    //     return back()->with('success', 'Stock dispatched successfully.');
    // }

    public function dispatch($id)
    {
        $user = Auth::user();
        $requestQuery = StockRequest::with(['items.product', 'branch']);
        $request = $this->scopeStockRequestsForUser($requestQuery, $user)->findOrFail($id);

        if ($request->status !== 'approved') {
            return back()->with('delete', __('messages.only_approve_request'));
        }

        // Check main warehouse existence
        $mainWarehouse = Warehouse::where('type', 'main')
            ->where('company_id', $request->branch->company_id)
            ->first();

        if (! $mainWarehouse) {
            return back()->with(
                'delete',
                __('messages.main_warehouse_is_not_configured')
            );
        }

        // Branch warehouse must exist
        $branchWarehouse = Warehouse::where('branch_id', $request->branch_id)
            ->where('type', 'branch')
            ->first();

        if (! $branchWarehouse) {
            return back()->with(
                'danger',
                __('messages.branch_warehouse_not_found')
            );
        }

        DB::beginTransaction();

        try {
            foreach ($request->items as $item) {

                $mainInventory = Inventory::where('warehouse_id', $mainWarehouse->id)
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if (! $mainInventory) {
                    throw new \Exception(
                        __('dashboard.product').' "'.$item->product->name.'" '.
                        __('messages.not_found_in_main_warehouse')
                    );

                }

                if ($mainInventory->quantity < $item->quantity) {
                    throw new \Exception(
                        __('messages.insufficient_stock_for').' "'.$item->product->name.'".'
                    );

                }

                // Deduct from main warehouse
                $mainInventory->decrement('quantity', $item->quantity);

                // Add to branch warehouse
                $branchInventory = Inventory::firstOrCreate(
                    [
                        'warehouse_id' => $branchWarehouse->id,
                        'product_id' => $item->product_id,
                    ],
                    ['quantity' => 0]
                );

                $branchInventory->increment('quantity', $item->quantity);
            }

            // Update request
            $request->update([
                'status' => 'dispatched',
                'dispatched_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', __('messages.stock_dispatched_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('danger', $e->getMessage());
        }
    }

    public function print($id)
    {
        $user = Auth::user();
        $requestQuery = StockRequest::with([
            'branch',
            'items.product',
        ]);
        $request = $this->scopeStockRequestsForUser($requestQuery, $user)->findOrFail($id);

        return view('Admin.Backend.partials.warehouse-print', compact('request'));
    }

    private function scopeStockRequestsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->whereHas('branch', fn ($branchQuery) => $branchQuery->where('company_id', $user->company_id));
    }
}

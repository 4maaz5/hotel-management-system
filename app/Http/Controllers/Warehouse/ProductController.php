<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Product;
use App\Models\Room;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $products = Product::all();
        $categories = Categories::all();

        if ($user->hasRole('super_admin')) {
            $warehouses = Warehouse::all();
            $rooms = Room::all();
        } elseif ($user->branch_id) {
            $warehouses = Warehouse::where('branch_id', $user->branch_id)->get();
            $rooms = Room::whereHas('warehouse', fn ($q) => $q->where('branch_id', $user->branch_id))->get();
        } else {
            $warehouses = Warehouse::whereHas('branch.company', fn ($q) => $q->where('id', $user->company_id))->get();
            $rooms = Room::whereHas('warehouse.branch.company', fn ($q) => $q->where('id', $user->company_id))->get();
        }

        return view('Admin.Backend.Products.index', compact('products', 'categories', 'warehouses', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
        ]);
        $product = Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'sku' => $request->sku,
            'unit' => $request->unit,
        ]);

        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => __('messages.product_added_successfully!'),
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category->name,
                'category_id' => $product->category_id,
                'sku' => $product->sku,
                'unit' => $product->unit,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
        ]);

        $product = Product::findOrFail($request->id);

        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'sku' => $request->sku,
            'unit' => $request->unit,
        ]);

        // Load category relation for response
        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => __('messages.product_updated_successfully'),
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'category' => $product->category->name,
                'sku' => $product->sku,
                'unit' => $product->unit,
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.classify_deleted_successfully!'),
            'id' => $request->id,
        ]);
    }
}

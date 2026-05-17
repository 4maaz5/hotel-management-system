<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Product;
use App\Models\Room;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $products = $this->scopeVisibleProductsForUser(Product::query(), $user)->get();
        $categories = $this->scopeVisibleCategoriesForUser(Categories::query(), $user)->get();

        $warehouses = $this->scopeWarehousesForUser(Warehouse::query(), $user)->get();
        $rooms = Room::whereHas('warehouse', fn ($query) => $this->scopeWarehousesForUser($query, $user))->get();

        return view('Admin.Backend.Products.index', compact('products', 'categories', 'warehouses', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($query) => $this->scopeVisibleCategoriesForUser($query, $request->user())),
            ],
            'sku' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
        ]);
        $product = Product::create([
            'company_id' => $this->isSuperAdmin($request->user()) ? null : $request->user()->company_id,
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
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($query) => $this->scopeVisibleCategoriesForUser($query, $request->user())),
            ],
            'sku' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
        ]);

        $product = $this->scopeMutableProductsForUser(Product::query(), $request->user())->findOrFail($request->id);

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

        $product = $this->scopeMutableProductsForUser(Product::query(), $request->user())->findOrFail($request->id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.classify_deleted_successfully!'),
            'id' => $request->id,
        ]);
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

    private function scopeMutableProductsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->where('company_id', $user->company_id);
    }

    private function scopeVisibleCategoriesForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->where(function ($categoryQuery) use ($user) {
            $categoryQuery->whereNull('company_id')
                ->orWhere('company_id', $user->company_id);
        });
    }
}

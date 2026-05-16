<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $categories = $this->scopeVisibleCategoriesForUser(Categories::query(), auth()->user())->get();

        return view('Admin.Backend.Categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        $category = Categories::create([
            'company_id' => $this->isSuperAdmin($request->user()) ? null : $request->user()->company_id,
            'name' => $validated['category_name'],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.type_created_successfully'),
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
        ]);
    }

    public function update(Request $request)
    {

        $request->validate([
            'id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        $category = $this->scopeMutableCategoriesForUser(Categories::query(), $request->user())->findOrFail($request->id);
        $category->name = $request->name;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => __('messages.type_updated_successfully'),
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:categories,id',
        ]);

        $category = $this->scopeMutableCategoriesForUser(Categories::query(), $request->user())->findOrFail($request->room_id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.type_deleted_successfully'),
            'data' => [
                'id' => $category->id,
            ],
        ]);
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

    private function scopeMutableCategoriesForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->where('company_id', $user->company_id);
    }
}

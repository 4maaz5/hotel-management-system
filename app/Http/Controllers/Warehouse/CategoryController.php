<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Categories::all();

        return view('Admin.Backend.Categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        $category = Categories::create([
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

        $category = Categories::findOrFail($request->id);
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

        $category = Categories::findOrFail($request->room_id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.type_deleted_successfully'),
            'data' => [
                'id' => $category->id,
            ],
        ]);
    }
}

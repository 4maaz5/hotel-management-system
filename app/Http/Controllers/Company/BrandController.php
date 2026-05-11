<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        $brandCards = Brand::paginate(10);
        $companies = Company::all();

        return view('Admin.Backend.Brand.index', compact('brands', 'companies', 'brandCards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'brand_name' => 'required|string|max:255',
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brand = new Brand;
        $brand->company_id = $request->company_id;
        $brand->name = $request->brand_name;

        if ($request->hasFile('brand_logo')) {
            $path = $request->file('brand_logo')->store('brands', 'public');
            $brand->logo = $path;
        }

        $brand->save();

        return response()->json([
            'success' => true,
            'message' => __('messages.brand_created_successfully'),
            'data' => [
                'id' => $brand->id,
                'name' => $brand->name,
                'company_name' => $brand->company->legal_name,
                'logo' => $brand->logo ? asset('storage/'.$brand->logo) : null,
            ],
        ]);

    }

    public function update(Request $request)
    {
        $request->validate([
            'brandId' => 'required|exists:brands,id',
            'company_id' => 'required|exists:companies,id',
            'brand_name' => 'required|string|max:255',
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brand = Brand::findOrFail($request->brandId);
        $brand->company_id = $request->company_id;
        $brand->name = $request->brand_name;

        if ($request->hasFile('brand_logo')) {
            $brand->logo = $request->file('brand_logo')->store('brands', 'public');
        }

        $brand->save();
        $brand = $brand->fresh('company');

        return response()->json([
            'success' => true,
            'message' => __('messages.brand_updated_successfully'),
            'data' => [
                'id' => $brand->id,
                'name' => $brand->name,
                'company_name' => $brand->company->legal_name ?? '',
                'logo' => $brand->logo ? asset('storage/'.$brand->logo) : null,
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'brandId' => 'required|exists:brands,id',
        ]);

        $brand = Brand::findOrFail($request->brandId);

        // Delete logo from public disk if exists
        if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
            Storage::disk('public')->delete($brand->logo);
        }

        // Delete brand record
        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.brand_deleted_successfully'),
        ]);
    }

    public function getBrands($company_id)
    {
        $brands = Brand::where('company_id', $company_id)->get();

        return response()->json($brands);
    }
}

<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $brands = $this->scopeBrandsForUser(Brand::query(), $user)->get();
        $brandCards = $this->scopeBrandsForUser(Brand::query(), $user)->paginate(10);
        $companies = $user->isSuperAdmin() ? Company::all() : Company::whereKey($user->company_id)->get();

        return view('Admin.Backend.Brand.index', compact('brands', 'companies', 'brandCards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => [
                'required',
                $this->isSuperAdmin($request->user())
                    ? Rule::exists('companies', 'id')
                    : Rule::exists('companies', 'id')->where(fn ($query) => $query->where('id', $request->user()->company_id)),
            ],
            'brand_name' => 'required|string|max:255',
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brand = new Brand;
        $brand->company_id = $this->isSuperAdmin($request->user()) ? $request->company_id : $request->user()->company_id;
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
            'company_id' => [
                'required',
                $this->isSuperAdmin($request->user())
                    ? Rule::exists('companies', 'id')
                    : Rule::exists('companies', 'id')->where(fn ($query) => $query->where('id', $request->user()->company_id)),
            ],
            'brand_name' => 'required|string|max:255',
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brand = $this->scopeBrandsForUser(Brand::query(), $request->user())->findOrFail($request->brandId);
        $brand->company_id = $this->isSuperAdmin($request->user()) ? $request->company_id : $request->user()->company_id;
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

        $brand = $this->scopeBrandsForUser(Brand::query(), $request->user())->findOrFail($request->brandId);

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
        abort_unless($this->isSuperAdmin(auth()->user()) || (int) $company_id === (int) auth()->user()->company_id, 403);

        $brands = $this->scopeBrandsForUser(Brand::where('company_id', $company_id), auth()->user())->get();

        return response()->json($brands);
    }

    private function scopeBrandsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->where('company_id', $user->company_id);
    }
}

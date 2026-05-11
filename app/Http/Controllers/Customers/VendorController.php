<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {

        $query = Vendor::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%'.$request->email.'%');
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%'.$request->phone.'%');
        }

        if ($request->filled('vat')) {
            $query->where('vat_registration_number', 'like', '%'.$request->vat.'%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $vendors = $query->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.vendors.index', compact('vendors'));

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dial_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'vat_registration_number' => 'nullable|string|max:50',
            'commercial_registration_number' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Vendor::create($validated);

        return redirect()->back()
            ->with('success', __('messages.vendor_created_successfully'));
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dial_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'vat_registration_number' => 'nullable|string|max:50',
            'commercial_registration_number' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $vendor->update($validated);

        return redirect()->back()
            ->with('success', __('messages.vendor_updated_successfully'));
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return redirect()->back()
            ->with('danger', __('messages.vendor_deleted_successfully'));
    }
}

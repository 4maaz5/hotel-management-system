<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\GuestClass;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class GuestClassController extends Controller
{
    public function index(Request $request)
    {
        $query = GuestClass::query();

        if ($request->filled('class_name')) {
            $query->where('class_name', 'like', '%'.$request->class_name.'%');
        }

        if ($request->filled('order_no')) {
            $query->where('order_no', $request->order_no);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $guestClasses = $query->orderBy('order_no')->get();

        return view('admin.guest_class.index', compact('guestClasses'));
    }

    public function create()
    {
        return view('admin.guest_class.create');
    }

    public function store(Request $request)
    {
        $companyId = app(TenantContext::class)->id() ?: $request->user()?->company_id;
        abort_unless($companyId, 422, 'Tenant context is required to create guest classes.');

        $request->validate([
            'class_name' => 'required|string|max:255',
            'order_no' => 'required|integer|min:1|max:20',
            'description' => 'nullable|string',
        ]);

        GuestClass::create([
            'company_id' => $companyId,
            'blacklist' => false,
            'class_name' => $request->class_name,
            'order_no' => $request->order_no,
            'icon' => null,
            'discount_method' => null,
            'discount_amount' => null,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('setup-sidebar.guest_class.index')
            ->with('success', __('messages.guest_class_created_successfully'));
    }

    public function edit($id)
    {
        $guestClass = GuestClass::findOrfail($id);

        return view('admin.guest_class.edit', compact('guestClass'));
    }

    public function update(Request $request, GuestClass $guestClass)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'order_no' => 'required|integer|min:1|max:20',
        ]);

        $guestClass->update([
            'is_active' => $request->has('is_active'),
            'class_name' => $request->class_name,
            'order_no' => $request->order_no,
            'blacklist' => false,
            'icon' => null,
            'discount_method' => null,
            'discount_amount' => null,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('setup-sidebar.guest_class.index')
            ->with('success', __('messages.guest_class_updated_successfully'));
    }

    public function delete(GuestClass $guestClass)
    {
        $guestClass->delete();

        return redirect()->route('setup-sidebar.guest_class.index')->with('danger', __('messages.guest_class_deleted_successfully'));
    }

    public function view($id)
    {
        $guestClass = GuestClass::findOrfail($id);

        return view('admin.guest_class.view', compact('guestClass'));
    }
}

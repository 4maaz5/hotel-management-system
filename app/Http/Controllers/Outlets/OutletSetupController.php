<?php

namespace App\Http\Controllers\Outlets;

use App\Http\Controllers\Controller;
use App\Models\OutletSetup;
use Illuminate\Http\Request;

class OutletSetupController extends Controller
{
    public function index(Request $request)
    {
        $query = OutletSetup::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->name.'%');
            });
        }
        if ($request->filled('outlet_code')) {
            $query->where('outlet_code', $request->outlet_code);
        }
        if ($request->filled('operating_status')) {
            $query->where('operating_status', $request->operating_status);
        }
        $outlets = $query->latest()->paginate(10);

        return view('admin.outlet_setup.index', compact('outlets'));
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'operating_status' => 'required|string|max:50',
        'outlet_code' => 'required|digits:3|unique:outlet_setups,outlet_code',
        'name' => 'required|string|max:200',
        'description' => 'nullable|string|max:600',
    ]);

    OutletSetup::create([
        'operating_status' => $validated['operating_status'],
        'outlet_code' => $validated['outlet_code'],
        'name' => $validated['name'],
        'description' => $validated['description'],
    ]);

    return redirect()->back()
        ->with('success', __('messages.outlet_created_successfully'));
}

    public function update(Request $request, $id)
    {
        $outlet = OutletSetup::findOrFail($id);

        $validated = $request->validate([
            'status' => 'nullable|boolean',
            'operating_status' => 'required|string|max:50',
            'outlet_code' => 'required|digits:3|unique:outlet_setups,outlet_code,'.$id,
            'name' => 'required|string|max:200',
            'description' => 'nullable|string|max:600',
        ]);

        $validated['status'] = $request->has('status') ? 1 : 0;

        $outlet->update($validated);

        return redirect()
            ->back()
            ->with('success', __('messages.outlet_updated_successfully'));
    }

    public function delete($id)
    {
        $outlet = OutletSetup::findOrfail($id);
        $outlet->delete();

        return redirect()->back()->with('danger', __('messages.outlet_deleted_successfully'));
    }
}

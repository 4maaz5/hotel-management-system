<?php

namespace App\Http\Controllers\Rules;

use App\Http\Controllers\Controller;
use App\Models\HotelTerm;
use Illuminate\Http\Request;

class ConditionController extends Controller
{
    public function index(Request $request)
    {
        $query = HotelTerm::query();
        if ($request->filled('order_no')) {
            $query->where('order_no', $request->order_no);
        }
        if ($request->filled('description')) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $conditions = $query
            ->orderBy('order_no')
            ->paginate(10)
            ->withQueryString();

        return view('admin.terms_condition.index', compact('conditions'));
    }

    public function create()
    {
        $maxOrder = HotelTerm::max('order_no') ?? 0;

        return view('admin.terms_condition.create', compact('maxOrder'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_no' => 'required|integer|min:1',
            'description' => 'required|string|max:300',
        ]);

        HotelTerm::create($request->only('order_no', 'description'));

        return redirect()
            ->route('setup-sidebar.condition.index')
            ->with('success', __('messages.condition_created_successfully'));
    }

    public function edit($id)
    {
        $condition = HotelTerm::findOrfail($id);

        return view('admin.terms_condition.edit', compact('condition'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'order_no' => 'required|integer|min:1',
            'description' => 'required|string|max:300',
        ]);

        $condition = HotelTerm::findOrFail($id);

        $condition->update([
            'order_no' => $request->order_no,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('setup-sidebar.condition.index')
            ->with('success', __('messages.condition_updated_successfully'));
    }

    public function delete($id)
    {
        $condition = HotelTerm::findOrfail($id);
        $condition->delete();

        return redirect()->back()->with('danger', __('messages.condition_deleted_successfully'));
    }
}

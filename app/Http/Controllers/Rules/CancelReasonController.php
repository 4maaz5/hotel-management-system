<?php

namespace App\Http\Controllers\Rules;

use App\Http\Controllers\Controller;
use App\Models\CancelReason;
use App\Models\Penalty;
use Illuminate\Http\Request;

class CancelReasonController extends Controller
{
    public function index(Request $request)
    {
        $query = CancelReason::query();

        if ($request->name) {
            $query->where(function ($cancelReasonQuery) use ($request) {
                $cancelReasonQuery->where('name', 'like', '%'.$request->name.'%')
                    ->orWhere('name_ar', 'like', '%'.$request->name.'%');
            });
        }

        if ($request->description) {
            $query->where(function ($cancelReasonQuery) use ($request) {
                $cancelReasonQuery->where('description', 'like', '%'.$request->description.'%')
                    ->orWhere('description_ar', 'like', '%'.$request->description.'%');
            });
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $cancelReasons = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.cancel_reason.index', compact('cancelReasons'));
    }

    public function create()
    {
        $penalties = Penalty::where('is_active', 1)->get();

        return view('admin.cancel_reason.create', compact('penalties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $cancelReason = CancelReason::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('penalties') && is_array($request->penalties)) {
            foreach ($request->penalties as $penaltyId) {
                $autoApply = $request->input('auto_apply_'.$penaltyId, false);
                $cancelReason->penalties()->attach($penaltyId, ['auto_apply' => $autoApply]);
            }
        }

        return redirect()->route('setup-sidebar.cancel_reason.index')
            ->with('success', __('messages.setting_added_successfully'));
    }

    public function edit($id)
    {
        $cancelReason = CancelReason::with('penalties')->findOrFail($id);
        $penalties = Penalty::where('is_active', 1)->get();

        return view('admin.cancel_reason.edit', compact('cancelReason', 'penalties'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $cancelReason = CancelReason::findOrFail($id);

        $cancelReason->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        $cancelReason->penalties()->detach();

        if ($request->has('penalties') && is_array($request->penalties)) {
            foreach ($request->penalties as $penaltyId) {
                $autoApply = $request->input('auto_apply_'.$penaltyId, false);
                $cancelReason->penalties()->attach($penaltyId, ['auto_apply' => $autoApply]);
            }
        }

        return redirect()->route('setup-sidebar.cancel_reason.index')
            ->with('success', __('messages.setting_updated_successfully'));
    }

    public function destroy($id)
    {
        $cancelReason = CancelReason::findOrFail($id);
        $cancelReason->penalties()->detach();
        $cancelReason->delete();

        return back()->with('success', __('messages.setting_updated_successfully'));
    }
}

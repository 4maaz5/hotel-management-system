<?php

namespace App\Http\Controllers\Financials;

use App\Http\Controllers\Controller;
use App\Models\TaxFeeCustomization;
use Illuminate\Http\Request;

class TaxesController extends Controller
{
    public function index(Request $request)
    {
        $query = TaxFeeCustomization::query();

        if ($request->filled('name')) {
            $query->where('custom_name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('status')) {

            if ($request->status == 'active') {
                $query->whereDate('start_date', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', now());
                    });
            }

            if ($request->status == 'inactive') {
                $query->where(function ($q) {
                    $q->whereDate('start_date', '>', now())
                        ->orWhereDate('end_date', '<', now());
                });
            }
        }

        if ($request->has('is_expenses')) {
            $query->where('is_expenses', true);
        }

        if ($request->filled('start_from')) {
            $query->whereDate('start_date', '>=', $request->start_from);
        }

        if ($request->filled('start_to')) {
            $query->whereDate('start_date', '<=', $request->start_to);
        }

        $customizations = $query->latest()->get();

        return view('admin.taxes.index', compact('customizations'));
    }

    public function create()
    {
        return view('admin.taxes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:tax,fee',
            'custom_name' => 'required|string|max:255',
            'method' => 'required|in:percentage,fixed_amount_reservation,fixed_amount_per_night',
            'amount' => 'required|numeric|min:0',
            'applied_on' => 'required|array|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        TaxFeeCustomization::create([
            'is_expenses' => $request->has('is_expenses'),
            'type' => $request->type,
            'custom_name' => $request->custom_name,
            'method' => $request->method,
            'amount' => $request->amount,
            'applied_on' => $request->applied_on,
            'has_max_length' => $request->has_max_length ?? false,
            'max_length' => $request->has_max_length ? $request->max_length : null,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'charged_on_fees' => false,
        ]);

        return redirect()->route('setup-sidebar.taxes.index')->with('success', __('messages.customization_created_successfully'));
    }

    public function edit($id)
    {
        $tax = TaxFeeCustomization::findOrFail($id);

        return view('admin.taxes.edit', compact('tax'));
    }

    public function update(Request $request, $id)
    {
        $tax = TaxFeeCustomization::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:tax,fee',
            'custom_name' => 'required|string',
            'method' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'applied_on' => 'required|array',
        ]);

        $isExpenses = $request->has('is_expenses');

        $appliedOn = $request->applied_on;

        if (in_array('all', $appliedOn)) {
            $appliedOn = ['rent', 'penalties', 'extras'];
        }

        $tax->update([
            'type' => $request->type,
            'is_expenses' => $isExpenses,
            'custom_name' => $request->custom_name,
            'method' => $request->method,
            'amount' => $request->amount,
            'applied_on' => $appliedOn,
            'has_max_length' => $request->has_max_length ?? 0,
            'max_length' => $request->max_length,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'charged_on_fees' => false,
        ]);

        return redirect()
            ->route('setup-sidebar.taxes.index')
            ->with('success', __('messages.tax_updated_successfully'));
    }

    public function view($id)
    {
        $tax = TaxFeeCustomization::findOrFail($id);

        return view('admin.taxes.view', compact('tax'));
    }

    public function delete($id)
    {
        $customization = TaxFeeCustomization::findOrfail($id);
        $customization->delete();

        return redirect()->back()->with('danger', __('messages.tax_fee_customization_deleted'));
    }
}

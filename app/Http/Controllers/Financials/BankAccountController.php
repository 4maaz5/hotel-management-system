<?php

namespace App\Http\Controllers\Financials;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = Bank::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('account_number')) {
            $query->where('account_number', 'like', '%'.$request->account_number.'%');
        }

        if ($request->filled('iban')) {
            $query->where('iban', 'like', '%'.$request->iban.'%');
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $banks = $query->latest()->paginate(10);

        return view('admin.bank_accounts.index', compact('banks'));
    }

    public function create()
    {
        return view('admin.bank_accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:150|unique:banks,account_number',
            'currency' => 'required|string|max:10',
            'iban' => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        Bank::create([
            'name' => $validated['name'],
            'account_number' => $validated['account_number'],
            'currency' => $validated['currency'],
            'iban' => $validated['iban'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('setup-sidebar.bank_account.index')
            ->with('success', __('messages.new_bank_added_successfully'));
    }

    public function edit(Bank $bank)
    {

        return view('admin.bank_accounts.edit', compact('bank'));
    }

    public function view(Bank $bank)
    {

        return view('admin.bank_accounts.view', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:150|unique:banks,account_number,'.$bank->id,
            'currency' => 'required|string|max:10',
            'iban' => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $bank->update([
            'name' => $validated['name'],
            'account_number' => $validated['account_number'],
            'currency' => $validated['currency'],
            'iban' => $validated['iban'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('setup-sidebar.bank_account.index')
            ->with('success', __('messages.bank_updated_successfully'));
    }

    public function delete(Bank $bank)
    {
        $bank->delete();

        return redirect()->route('setup-sidebar.bank_account.index')->with('danger', __('messages.bank_deleted_successfully'));
    }
}

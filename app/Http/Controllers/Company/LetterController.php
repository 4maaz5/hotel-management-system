<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Letter;
use App\Models\LetterSetting;
use App\Support\TenantContext;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LetterController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $generatedLetterNumber = $this->nextLetterNumber();
        $user = auth()->user();
        $companies = $this->isGlobalSuperAdmin($user) ? Company::all() : Company::whereKey($this->tenantIdForUser($user))->get();
        $branches = $this->scopeBranchesForUser(Branch::query(), $user)->get();
        $employees = $this->scopeEmployeesForUser(Employee::query(), $user)->get();
        $letters = $this->scopeLettersForUser(Letter::with('letterSetting'), $user)->get();
        $letterSettings = $this->scopeLetterSettingsForUser(LetterSetting::query(), $user)->latest()->get();

        return view('Admin.Backend.Letters.company', compact('companies', 'branches', 'employees', 'generatedLetterNumber', 'letters', 'letterSettings'));
    }

    public function store(Request $request)
    {
        //  Validate request
        $request->validate([
            'company_id' => [
                'required',
                $this->isGlobalSuperAdmin($request->user())
                    ? Rule::exists('companies', 'id')
                    : Rule::exists('companies', 'id')->where(fn ($query) => $query->where('id', $this->tenantIdForUser($request->user()))),
            ],
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $request->user())),
            ],
            'employee_id' => [
                'nullable',
                Rule::exists('employees', 'id')->where(fn ($query) => $this->scopeEmployeesForUser($query, $request->user())),
            ],
            'letter_setting_id' => [
                'nullable',
                Rule::exists('letter_settings', 'id')->where(fn ($query) => $this->scopeLetterSettingsForUser($query, $request->user())),
            ],
            'letter_type' => 'required|in:open,warning',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'hijri_date' => 'nullable|date',
            'receiver_name' => 'nullable|string',
        ]);

        $letterNumber = $this->nextLetterNumber();

        //  Prepare data
        $data = $request->only([
            'company_id',
            'branch_id',
            'employee_id',
            'letter_type',
            'subject',
            'body',
            'receiver_name',
            'letter_setting_id',
        ]);
        if (! $this->isGlobalSuperAdmin($request->user())) {
            $data['company_id'] = $this->tenantIdForUser($request->user());
        }
        $data['letter_number'] = $letterNumber;
        $data['gregorian_date'] = now();
        $data['hijri_date'] = $request->hijri_date ?? now()->format('Y-m-d');
        $data['created_by'] = auth()->id();

        //  Create Letter
        $letter = Letter::create($data);

        // Load relationships
        $letter->load(['company', 'employee', 'branch']);

        // Get letter settings
        // $letterSetting = \App\Models\LetterSetting::first();

        //  Return response
        return redirect()->back()
            ->with('success', __('messages.letter_created_successfully'));
    }

    public function update(Request $request, Letter $letter)
    {
        $letter = $this->scopeLettersForUser(Letter::query(), $request->user())->findOrFail($letter->id);

        $request->validate([
            'company_id' => [
                'required',
                $this->isGlobalSuperAdmin($request->user())
                    ? Rule::exists('companies', 'id')
                    : Rule::exists('companies', 'id')->where(fn ($query) => $query->where('id', $this->tenantIdForUser($request->user()))),
            ],
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $request->user())),
            ],
            'employee_id' => [
                'nullable',
                Rule::exists('employees', 'id')->where(fn ($query) => $this->scopeEmployeesForUser($query, $request->user())),
            ],
            'letter_setting_id' => [
                'nullable',
                Rule::exists('letter_settings', 'id')->where(fn ($query) => $this->scopeLetterSettingsForUser($query, $request->user())),
            ],
            'letter_type' => 'required|in:open,warning',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'hijri_date' => 'nullable|date',
            'receiver_name' => 'nullable|string',
        ]);

        $data = $request->only([
            'company_id',
            'branch_id',
            'employee_id',
            'letter_type',
            'subject',
            'body',
            'letter_number',
            'gregorian_date',
            'hijri_date',
            'receiver_name',
            'letter_setting_id',
        ]);
        if (! $this->isGlobalSuperAdmin($request->user())) {
            $data['company_id'] = $this->tenantIdForUser($request->user());
        }

        $letter->update($data);

        return redirect()->back()
            ->with('success', __('messages.letter_updated_successfully'));
    }

    public function destroy($id)
    {
        $letter = $this->scopeLettersForUser(Letter::query(), auth()->user())->findOrfail($id);
        //  Delete PDF file if exists
        if ($letter->pdf_path && Storage::disk('public')->exists($letter->pdf_path)) {
            Storage::disk('public')->delete($letter->pdf_path);
        }

        //  Delete letter record
        $letter->delete();

        //  Return response
        return redirect()->back()->with('delete', __('messages.letter_deleted_successfully'));
    }

    private function scopeLettersForUser($query, $user)
    {
        if ($this->isGlobalSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->where('company_id', $this->tenantIdForUser($user));
    }

    private function scopeEmployeesForUser($query, $user)
    {
        if ($this->isGlobalSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->where('company_id', $this->tenantIdForUser($user));
    }

    private function scopeLetterSettingsForUser($query, $user)
    {
        if ($this->isGlobalSuperAdmin($user)) {
            return $query;
        }

        return $query->where('company_id', $this->tenantIdForUser($user));
    }

    private function tenantIdForUser($user): ?int
    {
        return app(TenantContext::class)->id() ?: $user?->company_id;
    }

    private function isGlobalSuperAdmin($user): bool
    {
        return ! $this->tenantIdForUser($user)
            && ($user?->hasRole('super_admin') || $user?->role === 'super_admin');
    }

    private function nextLetterNumber(): string
    {
        $year = now()->year;
        $lastNumber = Letter::whereYear('created_at', $year)
            ->pluck('letter_number')
            ->map(function ($letterNumber) {
                $parts = explode('-', (string) $letterNumber);

                return (int) end($parts);
            })
            ->max() ?? 0;

        return sprintf('LTR-%s-%05d', $year, $lastNumber + 1);
    }
}

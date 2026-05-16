<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Letter;
use App\Models\LetterSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LetterController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        // Generate sequential letter number
        $year = now()->year;

        // Get last number used this year
        $lastNumber = Letter::whereYear('created_at', $year)
            ->max(DB::raw("CAST(SUBSTRING_INDEX(letter_number, '-', -1) AS UNSIGNED)"));

        $nextNumber = str_pad(($lastNumber + 1), 5, '0', STR_PAD_LEFT);

        $generatedLetterNumber = "LTR-{$year}-{$nextNumber}";
        $user = auth()->user();
        $companies = $user->isSuperAdmin() ? Company::all() : Company::whereKey($user->company_id)->get();
        $branches = $this->scopeBranchesForUser(Branch::query(), $user)->get();
        $employees = $this->scopeEmployeesForUser(Employee::query(), $user)->get();
        $letters = $this->scopeLettersForUser(Letter::with('letterSetting'), $user)->get();
        $letterSettings = LetterSetting::all();

        return view('Admin.Backend.Letters.company', compact('companies', 'branches', 'employees', 'generatedLetterNumber', 'letters', 'letterSettings'));
    }

    public function store(Request $request)
    {
        //  Validate request
        $request->validate([
            'company_id' => [
                'required',
                $this->isSuperAdmin($request->user())
                    ? Rule::exists('companies', 'id')
                    : Rule::exists('companies', 'id')->where(fn ($query) => $query->where('id', $request->user()->company_id)),
            ],
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $request->user())),
            ],
            'employee_id' => [
                'nullable',
                Rule::exists('employees', 'id')->where(fn ($query) => $this->scopeEmployeesForUser($query, $request->user())),
            ],
            'letter_setting_id' => 'nullable|exists:letter_settings,id',
            'letter_type' => 'required|in:open,warning',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'hijri_date' => 'nullable|date',
            'receiver_name' => 'nullable|string',
        ]);

        //  Generate Letter Number
        $year = now()->year;
        $lastNumber = Letter::whereYear('created_at', $year)
            ->max(DB::raw("CAST(SUBSTRING_INDEX(letter_number, '-', -1) AS UNSIGNED)"));
        $nextNumber = str_pad(($lastNumber + 1), 5, '0', STR_PAD_LEFT);
        $letterNumber = "LTR-{$year}-{$nextNumber}";

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
        if (! $this->isSuperAdmin($request->user())) {
            $data['company_id'] = $request->user()->company_id;
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
                $this->isSuperAdmin($request->user())
                    ? Rule::exists('companies', 'id')
                    : Rule::exists('companies', 'id')->where(fn ($query) => $query->where('id', $request->user()->company_id)),
            ],
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $request->user())),
            ],
            'employee_id' => [
                'nullable',
                Rule::exists('employees', 'id')->where(fn ($query) => $this->scopeEmployeesForUser($query, $request->user())),
            ],
            'letter_setting_id' => 'nullable|exists:letter_settings,id',
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
        if (! $this->isSuperAdmin($request->user())) {
            $data['company_id'] = $request->user()->company_id;
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
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->where('company_id', $user->company_id);
    }

    private function scopeEmployeesForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->where('company_id', $user->company_id);
    }
}

<?php

namespace App\Http\Controllers\Company;

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

class LetterController extends Controller
{
    public function index()
    {
        // Generate sequential letter number
        $year = now()->year;

        // Get last number used this year
        $lastNumber = Letter::whereYear('created_at', $year)
            ->max(DB::raw("CAST(SUBSTRING_INDEX(letter_number, '-', -1) AS UNSIGNED)"));

        $nextNumber = str_pad(($lastNumber + 1), 5, '0', STR_PAD_LEFT);

        $generatedLetterNumber = "LTR-{$year}-{$nextNumber}";
        $companies = Company::all();
        $branches = Branch::all();
        $employees = Employee::all();
        $letters = Letter::with('letterSetting')->get();
        $letterSettings = LetterSetting::all();

        return view('Admin.Backend.Letters.company', compact('companies', 'branches', 'employees', 'generatedLetterNumber', 'letters', 'letterSettings'));
    }

    public function store(Request $request)
    {
        //  Validate request
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'nullable|exists:employees,id',
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
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'nullable|exists:employees,id',
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

        $letter->update($data);

        return redirect()->back()
            ->with('success', __('messages.letter_updated_successfully'));
    }

    public function destroy($id)
    {
        $letter = Letter::findOrfail($id);
        //  Delete PDF file if exists
        if ($letter->pdf_path && Storage::disk('public')->exists($letter->pdf_path)) {
            Storage::disk('public')->delete($letter->pdf_path);
        }

        //  Delete letter record
        $letter->delete();

        //  Return response
        return redirect()->back()->with('delete', __('messages.letter_deleted_successfully'));
    }
}

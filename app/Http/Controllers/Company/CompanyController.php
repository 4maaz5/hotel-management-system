<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Models\Department;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class CompanyController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $companies = Company::all();
            $companyCards = Company::paginate(10);
        } else {
            $companies = Company::whereKey($user->company_id)->get();
            $companyCards = Company::whereKey($user->company_id)->paginate(10);
        }

        return view('Admin.Backend.Company.index', compact('companies', 'companyCards'));
    }

    public function store(Request $request)
    {
        // STEP 1: VALIDATION (Company + Document)
        $rules = [
            // Company fields
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cr_number' => 'nullable|string|max:50',
            'cr_expiry' => 'nullable|date',
            'vat_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
            'industry_type' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',

            // Document fields
            'type' => 'nullable|string|max:100',
            'issued_by' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiration_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf|max:5120',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // STEP 2: Upload logo
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('companies', 'public');
        }

        // STEP 3: Create Company
        $company = Company::create([
            'name' => $request->name,
            'legal_name' => $request->legal_name,
            'logo' => $logoPath,
            'cr_number' => $request->cr_number,
            'cr_expiry' => $request->cr_expiry,
            'vat_number' => $request->vat_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'street' => $request->street,
            'district' => $request->district,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'website' => $request->website,
            'industry_type' => $request->industry_type,
            'is_active' => $request->is_active ?? 1,
        ]);

        // STEP 4: Create Document (if document fields exist)
        if ($request->type || $request->hasFile('file')) {

            $docData = [
                'company_id' => $company->id,
                'name' => $request->name,
                'type' => $request->type,
                'issued_by' => $request->issued_by,
                'issue_date' => $request->issue_date,
                'expiration_date' => $request->expiration_date,
            ];

            // Upload document file
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = Str::slug($request->name).'-'.time().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('company_documents', $filename, 'public');
                $docData['file_path'] = $path;
            }

            CompanyDocument::create($docData);
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.company_and_documents_created_successfully'),
            'data' => $company,
        ]);
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cr_number' => 'nullable|string|max:50',
            'cr_expiry' => 'nullable|date',
            'vat_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
            'industry_type' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('Validation failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $company->logo = $request->file('logo')->store('companies', 'public');
        }

        // Update Company
        $company->update([
            'name' => $request->name,
            'legal_name' => $request->legal_name,
            'cr_number' => $request->cr_number,
            'cr_expiry' => $request->cr_expiry,
            'vat_number' => $request->vat_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'street' => $request->street,
            'district' => $request->district,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'website' => $request->website,
            'industry_type' => $request->industry_type,
            'is_active' => $request->is_active ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.company_updated_successfully.'),
            'data' => $company,
        ]);
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        if ($company->logo && Storage::disk('public')->exists($company->logo)) {
            Storage::disk('public')->delete($company->logo);
        }
        $company->delete();

        return response()->json(['success' => true]);
    }

    public function filter(Request $request)
    {
        $query = Company::query();

        // Text filters
        if ($request->filled('name')) {
            $query->where('legal_name', 'like', '%'.$request->name.'%');
        }

        // if ($request->filled('legal_name')) {
        //     $query->where('legal_name', 'like', '%'.$request->legal_name.'%');
        // }

        if ($request->filled('cr_number')) {
            $query->where('cr_number', 'like', '%'.$request->cr_number.'%');
        }

        if ($request->filled('vat_number')) {
            $query->where('vat_number', 'like', '%'.$request->vat_number.'%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%'.$request->email.'%');
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%'.$request->phone.'%');
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->city.'%');
        }

        // Date range filter for CR expiry
        if ($request->filled('cr_expiry_from')) {
            $query->whereDate('cr_expiry', '>=', $request->cr_expiry_from);
        }

        if ($request->filled('cr_expiry_to')) {
            $query->whereDate('cr_expiry', '<=', $request->cr_expiry_to);
        }

        $companies = $query->get();

        return response()->json([
            'success' => true,
            'data' => $companies,
        ]);
    }

    public function reportView()
    {
        $companyCards = Company::paginate(5);

        return view('Admin.Backend.Company.report', compact('companyCards'));
    }

    public function reports($companyId)
    {
        $company = \App\Models\Company::findOrFail($companyId);
        // Simple queries
        $brands = \App\Models\Brand::where('company_id', $companyId)->get();

        return view('Admin.Backend.partials.reports', compact(
            'company', 'brands'
        ));
    }

    public function getBrandBranches($brandId)
    {
        $brand = Brand::findOrFail($brandId);
        $branches = Branch::where('brand_id', $brandId)->get();

        // Return partial view
        return view('Admin.Backend.partials.branches', compact('brand', 'branches'));
    }

    public function getBranchDepartments($branchId)
    {
        $branch = Branch::findOrFail($branchId);
        $departments = Department::where('branch_id', $branchId)->get();

        return view('Admin.Backend.partials.departments', compact('branch', 'departments'));
    }

    public function getDepartmentEmployees($departmentId)
    {
        $department = Department::findOrFail($departmentId);
        $employees = Employee::where('department_id', $departmentId)->get();

        return view('Admin.Backend.partials.employee-list', compact('department', 'employees'));
    }

    public function getEmployeeDetails($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        return view('Admin.Backend.partials.employee-detail', compact('employee'));
    }

    public function downloadPdf(Request $request)
    {
        $html = $request->html;

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');

        return $pdf->download('company-report.pdf');
    }
}

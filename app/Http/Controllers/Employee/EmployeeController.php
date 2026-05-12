<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            $employees = Employee::all();
            $companies = Company::all();
            $brands = Brand::all();
            $employeeCards = Employee::paginate(8);
            $branches = Branch::all();
            $departments = Department::all();
            $shifts = Shift::all();
        } elseif ($user->branch_id) {
            $employees = Employee::where('branch_id', $user->branch_id)->get();
            $employeeCards = Employee::where('branch_id', $user->branch_id)->paginate(8);

            $branches = Branch::where('id', $user->branch_id)->get();
            $shifts = Shift::where('branch_id', $user->branch_id)->get();

            $branch = Branch::with('brand.company')->find($user->branch_id);
            $companies = $branch && $branch->brand ? collect([$branch->brand->company]) : collect();
            $departments = Department::where('branch_id', $user->branch_id)->get();
            $brands = $branch && $branch->brand ? collect([$branch->brand]) : collect();
        } else {
            $employees = Employee::all();
            $employeeCards = Employee::paginate(8);
            $branches = Branch::all();
            $shifts = Shift::all();
            $companies = $user->company_id
                ? Company::whereKey($user->company_id)->get()
                : collect();
            $departments = Department::all();
            $brands = Brand::all();
        }

        return view('Admin.Backend.Employee.index', compact('branches', 'shifts', 'departments', 'employees', 'employeeCards', 'companies', 'brands'));
    }

    public function create()
    {
        return view('Admin.Backend.Employee.create');
    }

    public function store(Request $request)
    {

        try {
            // Validate the main employee data
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:employees,email',
                'phone' => 'nullable|string|max:20',
                'designation' => 'nullable|string|max:255',
                'company_id' => 'nullable|exists:companies,id',
                'brand_id' => 'nullable|exists:brands,id',
                'department_id' => 'nullable|exists:departments,id',
                'branch_id' => 'nullable|exists:branches,id',
                'shift_id' => 'nullable|exists:shifts,id',
                'join_date' => 'nullable|date',
                'residence_expiry_date' => 'nullable|date',
                'bank_name' => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:50',
                'base_salary' => 'nullable|numeric|min:0',
                'salary_type' => 'nullable|string|in:monthly,weekly,daily,hourly',
                'commission_percentage' => 'nullable|numeric|min:0|max:100',
                'commission_type' => 'nullable|string|in:sales,profit,revenue',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'overtime' => 'nullable|numeric|min:0',
            ]);

            // Handle is_commission checkbox
            $validated['is_commission'] = $request->has('is_commission');

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
                $imagePath = $image->storeAs('employees', $fileName, 'public');
                $validated['image'] = $imagePath;
            }

            $user = User::where('email', $validated['email'])->first();

            if ($user) {

                // Update only safe fields
                $user->update([
                    'name' => $validated['first_name'].' '.$validated['last_name'],
                    'branch_id' => $validated['branch_id'],
                ]);

            } else {

                $password = ucfirst($validated['first_name']).'@123';

                $user = User::create([
                    'name' => $validated['first_name'].' '.$validated['last_name'],
                    'email' => $validated['email'],
                    'branch_id' => $validated['branch_id'],
                    'password' => Hash::make($password),
                ]);

                $user->assignRole('employee');
            }

            // ALWAYS set user_id (for both cases)
            $validated['user_id'] = $user->id;

            // Create Employee

            $validated['qr_code'] = Str::uuid();
            // Generate unique employee_id
            $lastEmployee = Employee::orderBy('id', 'desc')->first();

            if ($lastEmployee) {
                $lastId = (int) substr($lastEmployee->employee_id, 3); // remove "EMP" prefix
                $validated['employee_id'] = 'EMP'.str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $validated['employee_id'] = 'EMP0001';
            }
            $employee = Employee::create($validated);

            // Handle Insurance Data
            if ($request->has('insurances')) {
                foreach ($request->insurances as $insuranceData) {
                    // Only create if provider_name is provided (required field)
                    if (! empty($insuranceData['provider_name'])) {

                        // Handle insurance document upload
                        $documentPath = null;
                        if (isset($insuranceData['document']) && $insuranceData['document'] instanceof \Illuminate\Http\UploadedFile) {
                            $document = $insuranceData['document'];
                            $documentFileName = time().'_insurance_'.uniqid().'.'.$document->getClientOriginalExtension();
                            $documentPath = $document->storeAs('employee_insurances', $documentFileName, 'public');
                        }

                        // Create insurance record
                        $employee->insurances()->create([
                            'provider_name' => $insuranceData['provider_name'],
                            'policy_number' => $insuranceData['policy_number'] ?? null,
                            'policy_type' => $insuranceData['policy_type'] ?? null,
                            'start_date' => $insuranceData['start_date'] ?? null,
                            'expiry_date' => $insuranceData['expiry_date'] ?? null,
                            'premium_amount' => $insuranceData['premium_amount'] ?? null,
                            'document' => $documentPath,
                        ]);
                    }
                }
            }

            // Handle Document Data
            if ($request->has('documents')) {
                foreach ($request->documents as $documentData) {

                    if (! empty($documentData['type'])) {

                        // Handle document file upload
                        $filePath = null;
                        if (isset($documentData['document_path']) && $documentData['document_path'] instanceof \Illuminate\Http\UploadedFile) {
                            $file = $documentData['document_path'];
                            $fileName = time().'_document_'.uniqid().'.'.$file->getClientOriginalExtension();
                            $filePath = $file->storeAs('employee_documents', $fileName, 'public');
                        }

                        // Save using "file_path" (not document_path)
                        $employee->documents()->create([
                            'type' => $documentData['type'],
                            'file_path' => $filePath,   //  IMPORTANT
                            'document_number' => $documentData['document_number'] ?? null,
                            'issue_date' => $documentData['issue_date'] ?? null,
                            'expiration_date' => $documentData['expiry_date'] ?? null,
                        ]);
                    }
                }
            }

            // Load relationships for response
            $employee = Employee::with(['branch', 'department', 'insurances', 'documents'])->find($employee->id);

            return response()->json([
                'success' => true,
                'message' => __('messages.employee_created_successfully'),
                'data' => [
                    'id' => $employee->id,
                    'full_name' => $employee->first_name.' '.$employee->last_name,
                    'employee_id' => $employee->employee_id,
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'designation' => $employee->designation,
                    'branch_id' => $employee->branch_id,
                    'branch_name' => $employee->branch->name ?? '-',
                    'department_id' => $employee->department_id,
                    'department_name' => $employee->department->name ?? '-',
                    'join_date' => $employee->join_date,
                    'residence_expiry_date' => $employee->residence_expiry_date,
                    'bank_name' => $employee->bank_name,
                    'account_number' => $employee->account_number,
                    'base_salary' => $employee->base_salary,
                    'salary_type' => $employee->salary_type,
                    'is_commission' => $employee->is_commission,
                    'commission_percentage' => $employee->commission_percentage,
                    'commission_type' => $employee->commission_type,
                    'user_id' => $employee->user_id,
                    'insurances_count' => $employee->insurances->count(),
                    'documents_count' => $employee->documents->count(),
                ],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating employee: '.$e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $employee = Employee::with('branch', 'department')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $employee,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'branch_id' => 'required|exists:branches,id',
            'company_id' => 'required|exists:companies,id',
            'brand_id' => 'required|exists:brands,id',
            'shift_id' => 'required|exists:shifts,id',
            'department_id' => 'required|exists:departments,id',
            'image' => 'nullable|image|max:2048',
            'join_date' => 'required|date',
            'residence_expiry_date' => 'nullable|date',
        ]);

        $employee = Employee::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($employee->image) {
                Storage::delete('public/'.$employee->image);
            }
            $validated['image'] = $request->file('image')->store('employees', 'public');
        }

        $employee->update($validated);
        $employee = Employee::with('branch')->find($employee->id);
        // dd($employee);

        return response()->json([
            'success' => true,
            'message' => __('messages.employee_updated_successfully'),
            'data' => $employee->load('branch'),
            'branch_name' => $employee->branch->name,
            'shift_name' => $employee->shift->name,
        ]);
    }

    public function destroy(Request $request)
    {
        $employee = Employee::findOrFail($request->id);

        // Delete employee image
        if (! empty($employee->image)) {
            \Storage::disk('public')->delete($employee->image);
        }

        //  delete associated user
        if ($employee->user_id) {
            $user = User::find($employee->user_id);
            if ($user) {
                $user->delete();
            }
        }

        // Delete the employee record
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.employee_deleted_successfully'),
        ]);
    }

    public function show($id)
    {
        $employee = Employee::with(['branch', 'department'])->find($id);

        if (! $employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $employee,
        ]);
    }

    public function filter(Request $request)
    {
        $branchId = $request->branch_id;
        $search = $request->search;
        $phone = $request->phone;

        $query = Employee::with('branch');

        // Branch filter
        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        // Name or email search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        // Phone search
        if ($phone) {
            $query->where('phone', 'like', "%$phone%");
        }

        $employees = $query->paginate(10);

        if ($request->ajax()) {
            $html = '';
            foreach ($employees as $employee) {
                $html .= view('Admin.Backend.partials.card_row', compact('employee'))->render();
            }
            $pagination = $employees->links('pagination::bootstrap-5')->render();
            return response()->json(['html' => $html, 'pagination' => $pagination]);
        }

        return view('Admin.Backend.EmployeeAbsent.index', compact('employees'));
    }

    public function multipleFilter(Request $request)
    {
        $query = Employee::query();

        // Branch filter
        if ($request->filled('branch_id') && $request->branch_id != 'all') {
            $query->where('branch_id', $request->branch_id);
        }

        // Employee ID filter
        if ($request->filled('employee_id')) {
            $query->where('employee_id', 'like', "%{$request->employee_id}%");
        }

        // Name OR Email filter (combined input)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Department filter
        if ($request->filled('department_id') && $request->department_id != 'all') {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', "%{$request->phone}%");
        }

        $employees = $query->paginate();

        if ($request->ajax()) {
            return view('Admin.Backend.partials.index', compact('employees'))->render();
        }

        return view('Admin.Backend.Employee.index', compact('employees'));
    }

    public function getDepartments($branchId)
    {
        $departments = Department::where('branch_id', $branchId)->get();

        return response()->json($departments);
    }

    public function checkEmail(Request $request)
    {
        $email = $request->email;

        $existsInEmployees = Employee::where('email', $email)->exists();
        $existsInUsers = User::where('email', $email)->exists();

        return response()->json([
            'exists' => $existsInEmployees || $existsInUsers,
            'employee_exists' => $existsInEmployees,
        ]);
    }

    public function checkImage(Request $request)
    {
        if (! $request->hasFile('image')) {
            return response()->json(['invalid' => true]);
        }

        $image = $request->file('image');

        $valid = in_array($image->extension(), ['jpg', 'jpeg', 'png', 'webp'])
            && $image->getSize() <= 2048 * 1024;

        return response()->json([
            'invalid' => ! $valid,
        ]);
    }

    public function checkPhone(Request $request)
    {
        $phone = $request->phone;

        $existsInEmployees = Employee::where('phone', $phone)->exists();

        return response()->json([
            'phone_exists' => $existsInEmployees,
        ]);
    }
}

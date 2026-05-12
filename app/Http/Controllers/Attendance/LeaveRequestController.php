<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaveRequestController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::user();

    //     // Fetch all employees (for super admin view)
    //     $employees = Employee::all();

    //     if ($user->hasRole('super_admin')) {
    //         // Super Admin can see all leave requests
    //         $leaves = Leave::with('employee')->latest()->get();
    //         $leaveCards = Leave::with('employee')->latest()->paginate(10);

    //     } elseif ($user->hasRole('manager')) {
    //         // Manager can see leave requests for employees in their branch
    //         $leaves = Leave::whereHas('employee', function ($query) use ($user) {
    //             $query->where('branch_id', $user->branch_id);
    //         })->with('employee')->latest()->get();
    //         $leaveCards = Leave::whereHas('employee', function ($query) use ($user) {
    //             $query->where('branch_id', $user->branch_id);
    //         })->with('employee')->latest()->paginate(10);

    //     } elseif ($user->hasRole('employee')) {
    //         // Employee can see only their own leave requests
    //         $leaves = Leave::whereHas('employee', function ($query) use ($user) {
    //             $query->where('user_id', $user->id);
    //         })->with('employee')->latest()->get();
    //     } else {
    //         $leaves = collect();
    //     }

    //     return view('Admin.Backend.Leaves.index', compact('leaves', 'employees', 'leaveCards'));
    // }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            $employees = Employee::all();
            $leaves = Leave::with('employee')->latest()->paginate(10);
            $leaveCards = Leave::with('employee')->latest()->paginate(10);
        } elseif ($user->branch_id) {
            $employees = Employee::where('branch_id', $user->branch_id)->get();
            $leaves = Leave::whereHas('employee', function ($query) use ($user) {
                $query->where('branch_id', $user->branch_id);
            })->with('employee')->latest()->paginate(10);
            $leaveCards = Leave::whereHas('employee', function ($query) use ($user) {
                $query->where('branch_id', $user->branch_id);
            })->with('employee')->latest()->paginate(10);
        } else {
            $employees = Employee::where('company_id', $user->company_id)->get();
            $leaves = Leave::whereHas('employee', function ($query) use ($user) {
                $query->where('company_id', $user->company_id);
            })->with('employee')->latest()->paginate(10);
            $leaveCards = Leave::whereHas('employee', function ($query) use ($user) {
                $query->where('company_id', $user->company_id);
            })->with('employee')->latest()->paginate(10);
        }

        return view('Admin.Backend.Leaves.index', compact(
            'leaves',
            'employees',
            'leaveCards'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_days' => 'required|integer|min:1',
            'payment_type' => 'required|in:paid,unpaid',
            'travel_responsibility' => 'nullable|in:company,employee',
            'ticket_amount' => 'nullable|numeric|min:0',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee = Employee::find($request->employee_id);
        if (!$employee || (!$user->isSuperAdmin() && $employee->company_id !== $user->company_id)) {
            return response()->json(['errors' => ['employee_id' => ['Invalid employee.']]], 403);
        }

        $leave = Leave::create($request->only([
            'employee_id',
            'leave_type',
            'start_date',
            'end_date',
            'total_days',
            'payment_type',
            'travel_responsibility',
            'ticket_amount',
            'status',
            'reason',
        ]));

        // Store documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('leave_documents', 'public');

                $leave->documents()->create([
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => __('messages.leave_added_successfully'),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'leave_id' => 'required|exists:leaves,id',
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_days' => 'required|integer|min:1',
            'payment_type' => 'required|in:paid,unpaid',
            'travel_responsibility' => 'nullable|in:company,employee',
            'ticket_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee = Employee::find($request->employee_id);
        if (!$employee || (!$user->isSuperAdmin() && $employee->company_id !== $user->company_id)) {
            return response()->json(['errors' => ['employee_id' => ['Invalid employee.']]], 403);
        }

        $leave = Leave::find($request->leave_id);

        if (! $leave) {
            return response()->json(['error' => 'Leave record not found.'], 404);
        }

        $leave->update($request->only([
            'employee_id',
            'leave_type',
            'start_date',
            'end_date',
            'total_days',
            'payment_type',
            'travel_responsibility',
            'ticket_amount',
            'status',
            'reason',
        ]));

        return response()->json([
            'status' => 'success',
            'message' => __('messages.leave_updated_successfully'),
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $leave = Leave::find($id);

        if (! $leave) {
            return response()->json(['status' => 'error', 'message' => 'Leave not found.'], 404);
        }

        if (!$user->isSuperAdmin()) {
            $employee = Employee::find($leave->employee_id);
            if (!$employee || $employee->company_id !== $user->company_id) {
                return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
            }
        }

        $leave->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.leave_deleted_successfully'),
        ]);
    }

    // public function filterAjax(Request $request)
    // {
    //     $query = Leave::query();

    //     if ($request->filled('employee_id')) {
    //         $query->where('employee_id', $request->employee_id);
    //     }

    //     if ($request->filled('leave_type')) {
    //         $query->where('leave_type', $request->leave_type);
    //     }

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     if ($request->filled('start_date')) {
    //         $query->whereDate('start_date', '>=', $request->start_date);
    //     }

    //     if ($request->filled('end_date')) {
    //         $query->whereDate('end_date', '<=', $request->end_date);
    //     }

    //     $leaves = $query->orderBy('start_date', 'desc')->get();

    //     return view('Admin.Backend.partials.leaves', compact('leaves'));
    // }
    public function filterAjax(Request $request)
    {
        $user = Auth::user();
        $query = Leave::with('employee');

        // Branch-based access
        if ($user->hasRole('super_admin')) {
            // Optional branch filter for super admin
            if ($request->filled('branch_id')) {
                $query->whereHas('employee', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            }
        } else {
            // Other roles → only their branch
            if ($user->branch_id) {
                $query->whereHas('employee', function ($q) use ($user) {
                    $q->where('branch_id', $user->branch_id);
                });
            } else {
                // No branch assigned → return empty view
                return view('Admin.Backend.partials.leaves', ['leaves' => collect()]);
            }
        }

        // Existing filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $leaves = $query->orderBy('start_date', 'desc')->paginate(10);

        if ($request->ajax()) {
            $html = view('Admin.Backend.partials.leaves', compact('leaves'))->render();
            $pagination = $leaves->links('pagination::bootstrap-5')->render();
            return response()->json(['html' => $html, 'pagination' => $pagination]);
        }

        return view('Admin.Backend.partials.leaves', compact('leaves'));
    }
}

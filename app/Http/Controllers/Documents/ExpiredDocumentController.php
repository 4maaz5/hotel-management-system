<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\CompanyDocument;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;

class ExpiredDocumentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now();
        $soonDate = now()->addDays(60);

        if ($user->hasRole('super_admin')) {

            // Full access
            $employeeDocs = EmployeeDocument::with('employee.branch')->get();
            $companyDocs = CompanyDocument::all();

            // Expiring soon (today → 60 days)
            $expiringSoonEmployee = EmployeeDocument::whereBetween('expiration_date', [$today, $soonDate])->count();
            $expiringSoonCompany = CompanyDocument::whereBetween('expiration_date', [$today, $soonDate])->count();

            // Already expired (expiration_date < today)
            $expiredEmployee = EmployeeDocument::where('expiration_date', '<', $today)->count();
            $expiredCompany = CompanyDocument::where('expiration_date', '<', $today)->count();

        } else {
            // Non-super-admin → only their branch
            $employeeDocs = EmployeeDocument::with('employee.branch')
                ->whereHas('employee', function ($query) use ($user) {
                    $query->where('branch_id', $user->branch_id);
                })
                ->get();

            $companyDocs = collect(); // company documents hidden for non-super-admins

            // Expiring soon for their branch only
            $expiringSoonEmployee = EmployeeDocument::whereBetween('expiration_date', [$today, $soonDate])
                ->whereHas('employee', function ($q) use ($user) {
                    $q->where('branch_id', $user->branch_id);
                })
                ->count();

            $expiringSoonCompany = 0;

            // Expired for their branch only
            $expiredEmployee = EmployeeDocument::where('expiration_date', '<', $today)
                ->whereHas('employee', function ($q) use ($user) {
                    $q->where('branch_id', $user->branch_id);
                })
                ->count();

            $expiredCompany = 0;
        }

        return view('Admin.Backend.ExpirationAlert.dashboard', compact(
            'employeeDocs',
            'companyDocs',
            'expiringSoonEmployee',
            'expiringSoonCompany',
            'expiredEmployee',
            'expiredCompany'
        ));
    }

    public function filteredDocuments(Request $request)
    {
        $user = auth()->user();
        $today = now();
        $soonDate = now()->addDays(60);

        // Fetch employee documents
        $employeeDocs = EmployeeDocument::with('employee.branch');
        if ($user->hasRole('manager')) {
            $employeeDocs->whereHas('employee', fn ($q) => $q->where('branch_id', $user->branch_id));
        }
        $employeeDocs = $employeeDocs->get();

        // Fetch company documents
        $companyDocs = $user->hasRole('super_admin') ? CompanyDocument::all() : collect();

        // Merge documents
        $allDocs = $employeeDocs->merge($companyDocs)->map(function ($doc) use ($today) {
            $expiry = \Carbon\Carbon::parse($doc->expiration_date);
            $daysLeft = intval($today->diffInDays($expiry, false));

            $status = $daysLeft < 0 ? 'expired' : ($daysLeft <= 70 ? 'expiring_soon' : 'active');

            return [
                'id' => $doc->id,
                'name' => $doc->name ?? $doc->document_number,
                'owner' => $doc->employee->first_name ?? 'Company',
                'branch' => $doc->employee->branch->name ?? '—',
                'type' => $doc->type,
                'issue_date' => $doc->issue_date,
                'expiration_date' => $doc->expiration_date,
                'status' => $status,
                'days_left' => $daysLeft >= 0 ? $daysLeft : 0,
                'doc_type' => $doc instanceof EmployeeDocument ? 'employee' : 'company',
            ];
        });

        return response()->json($allDocs);
    }
}

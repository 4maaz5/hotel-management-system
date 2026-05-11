<?php

namespace App\Http\Middleware;

use App\Models\CompanyDocument;
use App\Models\EmployeeDocument;
use App\\Models\\SystemNotification;
use App\Models\User;
use Carbon\Carbon;
use Closure;

class CheckExpiringDocuments
{
    // public function handle($request, Closure $next)
    // {
    //     // Run only once per session/day to reduce overhead
    //     if (! session()->has('checked_expiring_docs_today')) {

    //         $today = Carbon::today();

    //         // Check Employee Documents
    //         $this->checkDocuments(EmployeeDocument::all(), $today, 'Employee Document');

    //         // Check Company Documents
    //         $this->checkDocuments(CompanyDocument::all(), $today, 'Company Document');

    //         // Set session flag to avoid multiple runs per day
    //         session()->put('checked_expiring_docs_today', true);
    //     }

    //     return $next($request);
    // }

    // private function checkDocuments($documents, $today, $typeLabel)
    // {
    //     foreach ($documents as $doc) {

    //         if (! $doc->expiration_date) {
    //             continue;
    //         }

    //         $employee = \App\Models\Employee::find($doc->employee_id);
    //         if (! $employee) {
    //             continue;
    //         }

    //         $daysLeft = $today->diffInDays(Carbon::parse($doc->expiration_date), false);

    //         if ($daysLeft <= 60) {
    //             $status = $daysLeft < 0 ? 'Expired' : 'Expiring Soon';

    //             $docName = $doc->type ?? $doc->name;
    //             $employeeId = $employee->id ?? '';
    //             $message = "{$typeLabel} '{$docName}' (Employee ID: {$employeeId}) is {$status}.";

    //             // -------------------------------
    //             // 1. Notify Super Admins
    //             // -------------------------------
    //             $superAdmins = User::role('super_admin')->get();
    //             foreach ($superAdmins as $admin) {

    //                 // Delete previous notifications with the same message for this super_admin
    //                 \\DB::table('system_notifications')
    //                     ->where('recipient_type', 'super_admin')
    //                     ->where('recipient_id', $admin->id)
    //                     ->where('message', $message)
    //                     ->delete();

    //                 // Save new notification
    //                 \\DB::table('system_notifications')->insert([
    //                     'type' => 'email',
    //                     'message' => $message,
    //                     'recipient_type' => 'super_admin',
    //                     'recipient_id' => $admin->id,
    //                     'status' => 'sent',
    //                     'scheduled_at' => now(),
    //                     'created_by' => auth()->id() ?? null,
    //                     'created_at' => now(),
    //                     'updated_at' => now(),
    //                 ]);

    //                 // Send email
    //                 \Mail::to($admin->email)
    //                     ->send(new \App\Mail\DocumentExpiryMail($message, $doc, $employee));
    //             }

    //             // -------------------------------
    //             // 2. Notify Branch Managers
    //             // -------------------------------
    //             $branchId = $employee->branch_id ?? null;
    //             if ($branchId) {
    //                 $managers = User::where('branch_id', $branchId)
    //                     ->whereHas('roles', function ($q) {
    //                         $q->where('name', 'manager');
    //                     })
    //                     ->get();

    //                 foreach ($managers as $manager) {

    //                     // Delete previous notifications with the same message for this manager
    //                     \\DB::table('system_notifications')
    //                         ->where('recipient_type', 'manager')
    //                         ->where('recipient_id', $manager->id)
    //                         ->where('message', $message)
    //                         ->delete();

    //                     // Save new notification
    //                     \\DB::table('system_notifications')->insert([
    //                         'type' => 'email',
    //                         'message' => $message,
    //                         'recipient_type' => 'manager',
    //                         'recipient_id' => $manager->id,
    //                         'status' => 'sent',
    //                         'scheduled_at' => now(),
    //                         'created_by' => auth()->id() ?? null,
    //                         'created_at' => now(),
    //                         'updated_at' => now(),
    //                     ]);

    //                     // Send email
    //                     \Mail::to($manager->email)
    //                         ->send(new \App\Mail\DocumentExpiryMail($message, $doc, $employee));
    //                 }
    //             }
    //         }
    //     }
    // }

    // private function checkDocuments($documents, $today, $typeLabel)
    // {
    //     foreach ($documents as $doc) {

    //         // Skip if no expiration date
    //         if (! $doc->expiration_date) {
    //             continue;
    //         }

    //         // Check if employee exists
    //         $employeeExists = \App\Models\Employee::where('id', $doc->employee_id)->exists();
    //         if (! $employeeExists) {
    //             continue; // skip this document
    //         }

    //         $daysLeft = $today->diffInDays(Carbon::parse($doc->expiration_date), false);

    //         if ($daysLeft <= 30) {
    //             $status = $daysLeft < 0 ? 'Expired' : 'Expiring Soon';

    //             $docName = $doc->type ?? $doc->name;
    //             $employeeId = $doc->employee_id ?? '';
    //             $message = "{$typeLabel} '{$docName}' {$employeeId} is {$status}.";

    //             // Send notification to all super admins
    //             $superAdminIds = User::role('super_admin')->pluck('id');
    //             foreach ($superAdminIds as $adminId) {

    //                 // Delete previous notifications with the same message for this super_admin
    //                 SystemNotification::where('recipient_type', 'super_admin')
    //                     ->where('recipient_id', $adminId)
    //                     ->where('message', $message)
    //                     ->delete();

    //                 // Create the new notification
    //                 SystemNotification::create([
    //                     'type' => 'system',
    //                     'message' => $message,
    //                     'recipient_type' => 'super_admin',
    //                     'recipient_id' => $adminId,
    //                     'status' => 'sent',
    //                     'created_by' => auth()->id() ?? null,
    //                     'scheduled_at' => now(),
    //                 ]);
    //             }
    //         }
    //     }
    // }
}

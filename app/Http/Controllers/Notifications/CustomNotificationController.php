<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomNotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = DB::table('system_notifications');
        $departments = Department::with('branch')
            ->orderBy('branch_id')   // first group by branch
            ->orderBy('name')        // then sort by department name
            ->get();

        // Role-based filtering
        if (! $user->hasRole('super_admin')) {
            $query->where(function ($q) use ($user) {
                // Notifications meant for everyone
                $q->where('recipient_type', 'all');

                // Branch-specific notifications (for managers)
                if ($user->branch_id) {
                    $q->orWhere(function ($q2) use ($user) {
                        $q2->where('recipient_type', 'branch')
                            ->where('recipient_id', $user->branch_id);
                    });
                }

                // Employee-specific notifications
                $q->orWhere(function ($q3) use ($user) {
                    $q3->where('recipient_type', 'employee')
                        ->where('recipient_id', $user->id);
                });

                // Manager-specific notifications
                if ($user->hasRole('manager')) {
                    $q->orWhere(function ($q4) use ($user) {
                        $q4->where('recipient_type', 'manager')
                            ->where(function ($q5) use ($user) {
                                $q5->whereNull('recipient_id') // all managers
                                    ->orWhere('recipient_id', $user->id); // this manager
                            });
                    });
                }
            });
        }

        // Fetch notifications for table
        $notifications = $query->orderBy('created_at', 'desc')->get();

        // Clone the query for cards and paginate
        $notificationCards = (clone $query)->orderBy('created_at', 'desc')->paginate(10);

        return view('Admin.Backend.CustomNotification.index', compact('notifications', 'notificationCards', 'departments'));
    }
}

<?php

namespace App\Http\Controllers\Meetings;

use App\Http\Controllers\Controller;
use App\Mail\MeetingCreated;
use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MeetingController extends Controller
{
    // public function index()
    // {
    //     $meetings = Meeting::all();
    //     $users = User::all();

    //     return view('Admin.Backend.Meetings.index', compact('meetings', 'users'));
    // }

    public function index()
    {
        $user = auth()->user();
        $users = User::all();

        if ($user->hasRole('super_admin')) {
            $meetings = Meeting::latest()->get();
        } else {
            $meetings = Meeting::where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhereHas('participants', fn ($p) => $p->where('user_id', $user->id));
                })
                ->latest()
                ->get();
        }

        return view('Admin.Backend.Meetings.index', compact('meetings', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'nullable|date',
            'duration' => 'nullable|integer',
            'participants' => 'nullable|array',
        ]);

        $user = auth()->user();
        $roomName = 'HRMS_'.\Str::uuid();

        $meeting = Meeting::create([
            'company_id' => $user->company_id,
            'title' => $request->title,
            'room_name' => $roomName,
            'link' => 'https://meet.jit.si/'.$roomName,
            'start_time' => $request->start_time,
            'duration' => $request->duration,
            'created_by' => $user->id,
        ]);

        $users = collect();

        if ($request->participants) {
            foreach ($request->participants as $userId) {
                MeetingParticipant::create([
                    'meeting_id' => $meeting->id,
                    'user_id' => $userId,
                ]);

                $users->push(User::find($userId));
            }
        }

        // Send email notifications
        foreach ($users as $user) {
            if ($user && $user->email) {
                Mail::to($user->email)->send(new MeetingCreated($meeting));
            }
        }

        return redirect()->route('meetings.join', $meeting->id)
            ->with('success', __('messages.meeting_created_successfully'));

    }

    public function join(Meeting $meeting)
    {
        return view('Admin.Backend.Meetings.join', compact('meeting'));
    }

    public function destroy(Request $request, $meeting)
    {
        $meetings = Meeting::findOrfail($meeting);
        $meetings->delete();

        return redirect()->back()->with('delete', __('messages.meeting_deleted_successfully'));
    }
}

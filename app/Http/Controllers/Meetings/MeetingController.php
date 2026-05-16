<?php

namespace App\Http\Controllers\Meetings;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Mail\MeetingCreated;
use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class MeetingController extends Controller
{
    use ScopesTenantAccess;

    // public function index()
    // {
    //     $meetings = Meeting::all();
    //     $users = User::all();

    //     return view('Admin.Backend.Meetings.index', compact('meetings', 'users'));
    // }

    public function index()
    {
        $user = auth()->user();
        $users = $this->scopeUsersForUser(User::query(), $user)->get();

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
            'participants.*' => [
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $this->scopeUsersForUser($query, $request->user())),
            ],
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

                $users->push($this->scopeUsersForUser(User::query(), $user)->find($userId));
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
        $meeting = $this->scopeMeetingsForUser(Meeting::query(), auth()->user())->findOrFail($meeting->id);

        return view('Admin.Backend.Meetings.join', compact('meeting'));
    }

    public function destroy(Request $request, $meeting)
    {
        $meetings = $this->scopeMeetingsForUser(Meeting::query(), $request->user())->findOrfail($meeting);
        $meetings->delete();

        return redirect()->back()->with('delete', __('messages.meeting_deleted_successfully'));
    }

    private function scopeUsersForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->where('company_id', $user->company_id);
    }

    private function scopeMeetingsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->where('company_id', $user->company_id)
            ->where(function ($meetingQuery) use ($user) {
                $meetingQuery->where('created_by', $user->id)
                    ->orWhereHas('participants', fn ($participantQuery) => $participantQuery->where('user_id', $user->id));
            });
    }
}

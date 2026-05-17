<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TrackerController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = Auth::user();
        $projects = $this->scopeProjectsForUser(Project::query(), $user)->get();
        $trackers = $this->scopeTrackersForUser(ProjectTracker::with('project'), $user)->get();

        return view('Admin.Backend.Projects.tracker', compact('projects', 'trackers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => ['required', $this->projectExistsRuleForUser($request->user())],
            'level' => 'required|integer|between:1,4',
            'status' => 'required|in:pending,in_progress,completed',
            'description' => 'nullable|string',
        ]);

        ProjectTracker::updateOrCreate(
            [
                'project_id' => $request->project_id,
                'level' => $request->level,
            ],
            [
                'status' => $request->status,
                'description' => $request->description,
            ]
        );

        return back()->with('success', __('messages.tracker_updated_successfully'));
    }

    private function scopeProjectsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        $query->where('company_id', $this->companyIdForUser($user));

        if ($user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }

        return $query;
    }

    private function scopeTrackersForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->whereHas('project', fn ($projectQuery) => $this->scopeProjectsForUser($projectQuery, $user));
    }

    private function projectExistsRuleForUser($user)
    {
        return Rule::exists('projects', 'id')->where(function ($query) use ($user) {
            if ($this->isSuperAdmin($user)) {
                return;
            }

            $query->where('company_id', $this->companyIdForUser($user));

            if ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            }
        });
    }

    private function companyIdForUser($user): ?int
    {
        return $user?->company_id ?: $user?->branch?->company_id;
    }
}

<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTracker;
use Illuminate\Http\Request;

class TrackerController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        $trackers = ProjectTracker::all();

        return view('Admin.Backend.Projects.tracker', compact('projects', 'trackers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
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
}

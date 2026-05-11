<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();

        return view('Admin.Backend.Projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'project_manager' => 'nullable|string|max:255',
            'value' => 'nullable|numeric',
            'timeline_type' => 'required|in:fixed,milestone',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'documents.*' => 'nullable|mimes:pdf|max:2048',
        ]);

        //  Handle multiple file uploads
        $documentPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('projects', 'public');
                $documentPaths[] = $path;
            }
        }

        //  Save project data
        $project = new \App\Models\Project;
        $project->name = $request->name;
        $project->location = $request->location;
        $project->project_manager = $request->project_manager;
        $project->value = $request->value;
        $project->timeline_type = $request->timeline_type;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->documents = json_encode($documentPaths);
        $project->save();

        //  Redirect with success message
        return redirect()->back()->with('success', __('messages.project_added_successfully'));
    }

    public function update(Request $request, $project)
    {
        // Find the project
        $project = \App\Models\Project::findOrFail($project);

        // 1. Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'project_manager' => 'nullable|string|max:255',
            'value' => 'nullable|numeric',
            'timeline_type' => 'required|in:fixed,milestone',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'documents.*' => 'nullable|mimes:pdf|max:2048', // each file max 2MB
        ]);

        // 2. Handle multiple file uploads
        $existingDocs = $project->documents ? json_decode($project->documents, true) : [];
        $newDocs = [];

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('projects', 'public');
                $newDocs[] = $path;
            }
        }

        // Merge old and new documents
        $allDocs = array_merge($existingDocs, $newDocs);

        // 3. Update project data
        $project->update([
            'name' => $request->name,
            'location' => $request->location,
            'project_manager' => $request->project_manager,
            'value' => $request->value,
            'timeline_type' => $request->timeline_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'documents' => json_encode($allDocs),
        ]);

        // 4. Redirect back with success message
        return redirect()->back()->with('success', __('messages.project_updated_successfully'));
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        // Delete files from storage
        if ($project->documents) {
            foreach (json_decode($project->documents) as $doc) {
                if (\Storage::disk('public')->exists($doc)) {
                    \Storage::disk('public')->delete($doc);
                }
            }
        }

        // Delete project record
        $project->delete();

        return redirect()->back()->with('delete', __('messages.project_deleted_successfully'));
    }
}

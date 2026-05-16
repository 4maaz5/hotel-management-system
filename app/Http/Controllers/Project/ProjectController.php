<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $projects = $this->scopeProjectsForUser(Project::query(), Auth::user())->get();

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

        $user = Auth::user();

        Project::create([
            'company_id' => $this->companyIdForUser($user),
            'branch_id' => $this->branchIdForUser($user),
            'name' => $request->name,
            'location' => $request->location,
            'project_manager' => $request->project_manager,
            'value' => $request->value,
            'timeline_type' => $request->timeline_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'documents' => $documentPaths,
        ]);

        //  Redirect with success message
        return redirect()->back()->with('success', __('messages.project_added_successfully'));
    }

    public function update(Request $request, $project)
    {
        $project = $this->scopeProjectsForUser(Project::query(), Auth::user())->findOrFail($project);

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
        $existingDocs = $project->documents ?: [];
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
            'documents' => $allDocs,
        ]);

        // 4. Redirect back with success message
        return redirect()->back()->with('success', __('messages.project_updated_successfully'));
    }

    public function destroy($id)
    {
        $project = $this->scopeProjectsForUser(Project::query(), Auth::user())->findOrFail($id);

        // Delete files from storage
        if ($project->documents) {
            foreach ((array) $project->documents as $doc) {
                if (\Storage::disk('public')->exists($doc)) {
                    \Storage::disk('public')->delete($doc);
                }
            }
        }

        // Delete project record
        $project->delete();

        return redirect()->back()->with('delete', __('messages.project_deleted_successfully'));
    }

    protected function scopeProjectsForUser($query, $user)
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

    protected function companyIdForUser($user): ?int
    {
        return $user?->company_id ?: $user?->branch?->company_id;
    }

    protected function branchIdForUser($user): ?int
    {
        return $user?->branch_id;
    }
}

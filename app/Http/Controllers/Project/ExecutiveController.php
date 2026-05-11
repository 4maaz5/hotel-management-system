<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectExecutive;
use Illuminate\Http\Request;

class ExecutiveController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        $executives = ProjectExecutive::with('project')->latest()->get();

        return view('Admin.Backend.Projects.executive', compact('projects', 'executives'));
    }

    public function store(Request $request)
    {
        //  Validate request
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'responsible_person_name' => 'required|string|max:255',
            'contract_reference' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
        ]);

        //  Store data
        ProjectExecutive::create($validated);

        //  Redirect with success
        return redirect()
            ->back()
            ->with('success', __('messages.project_executive_added_successfully'));
    }

    public function update(Request $request, $id)
    {
        //  Find executive
        $executive = ProjectExecutive::findOrFail($id);

        //  Validate request
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'responsible_person_name' => 'required|string|max:255',
            'contract_reference' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
        ]);

        //  Update record
        $executive->update($validated);

        //  Redirect with success
        return redirect()
            ->back()
            ->with('success', __('messages.project_executive_updated_successfully'));
    }

    public function destroy($id)
    {
        $executive = ProjectExecutive::findOrFail($id);

        $executive->delete();

        return redirect()
            ->back()
            ->with('delete', __('messages.project_executive_deleted_successfully'));
    }
}

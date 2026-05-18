<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectExecutive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ExecutiveController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = Auth::user();
        $projects = $this->scopeProjectsForUser(Project::withoutGlobalScopes(), $user)->get();
        $executives = $this->scopeExecutivesForUser(ProjectExecutive::with('project'), $user)->latest()->get();

        return view('Admin.Backend.Projects.executive', compact('projects', 'executives'));
    }

    public function store(Request $request)
    {
        //  Validate request
        $validated = $request->validate([
            'project_id' => ['required', $this->projectExistsRuleForUser($request->user())],
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
        $executive = $this->scopeExecutivesForUser(ProjectExecutive::query(), $request->user())->findOrFail($id);

        //  Validate request
        $validated = $request->validate([
            'project_id' => ['required', $this->projectExistsRuleForUser($request->user())],
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
        $executive = $this->scopeExecutivesForUser(ProjectExecutive::query(), Auth::user())->findOrFail($id);

        $executive->delete();

        return redirect()
            ->back()
            ->with('delete', __('messages.project_executive_deleted_successfully'));
    }

    private function scopeProjectsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        $query->where('company_id', $this->companyIdForUser($user));

        if ($user->branch_id) {
            $query->where(function ($query) use ($user) {
                $query->whereNull('branch_id')
                    ->orWhere('branch_id', $user->branch_id);
            });
        }

        return $query;
    }

    private function scopeExecutivesForUser($query, $user)
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
                $query->where(function ($query) use ($user) {
                    $query->whereNull('branch_id')
                        ->orWhere('branch_id', $user->branch_id);
                });
            }
        });
    }

    private function companyIdForUser($user): ?int
    {
        return $user?->company_id ?: $user?->branch?->company_id;
    }
}

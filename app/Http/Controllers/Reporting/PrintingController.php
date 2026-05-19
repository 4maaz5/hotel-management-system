<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use App\Models\PrintingOption;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PrintingController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $this->currentCompanyId($request);
        abort_unless($companyId, 422, 'Tenant context is required to manage printing options.');

        PrintingOption::ensureDefaultsForTenant($companyId);

        $options = PrintingOption::query()
            ->orderBy('id')
            ->get();
        $reportSetting = PrintingOption::query()->first();

        return view('admin.print_option.index', compact('options', 'reportSetting'));
    }

    public function update(Request $request)
    {
        $companyId = $this->currentCompanyId($request);
        abort_unless($companyId, 422, 'Tenant context is required to manage printing options.');

        PrintingOption::ensureDefaultsForTenant($companyId);

        $request->validate([
            'contract_template_type' => ['required', Rule::in(['double', 'single'])],
            'reports' => ['nullable', 'array'],
            'reports.*.enabled' => ['nullable'],
            'reports.*.letter_head' => ['nullable'],
            'reports.*.blank_paper' => ['nullable'],
            'reports.*.cashier_paper' => ['nullable'],
        ]);

        $reports = $request->input('reports', []);

        $options = PrintingOption::query()->get()->keyBy('id');

        foreach ($options as $option) {
            $data = $reports[$option->id] ?? [];

            $option->update([
                'enabled' => isset($data['enabled']),
                'letter_head' => isset($data['letter_head']),
                'blank_paper' => isset($data['blank_paper']),
                'cashier_paper' => isset($data['cashier_paper']),
                'contract_template_type' => $request->contract_template_type,
            ]);
        }

        return back()->with('success', __('messages.printing_options_updated_successfully'));
    }

    private function currentCompanyId(Request $request): ?int
    {
        return app(TenantContext::class)->id() ?: $request->user()?->company_id;
    }
}

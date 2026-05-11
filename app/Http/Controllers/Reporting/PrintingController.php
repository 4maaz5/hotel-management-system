<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use App\Models\PrintingOption;
use Illuminate\Http\Request;

class PrintingController extends Controller
{
    public function index()
    {
        $options = PrintingOption::all();
        $reportSetting = PrintingOption::first();

        return view('admin.print_option.index', compact('options', 'reportSetting'));
    }

    public function update(Request $request)
    {
        $reports = $request->input('reports', []);

        foreach ($reports as $id => $data) {

            PrintingOption::where('id', $id)->update([
                'letter_head' => isset($data['letter_head']),
                'blank_paper' => isset($data['blank_paper']),
                'cashier_paper' => isset($data['cashier_paper']),
                'contract_template_type' => $request->contract_template_type,
            ]);
        }

        return back()->with('success', __('messages.printing_options_updated_successfully'));
    }
}

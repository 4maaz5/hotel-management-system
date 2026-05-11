<?php

namespace App\Http\Controllers\Rules;

use App\Http\Controllers\Controller;
use App\Models\FeedbackMetric;
use Illuminate\Http\Request;

class GuestFeedbackController extends Controller
{
    public function index()
    {
        $metrics = FeedbackMetric::orderBy('id', 'desc')->paginate(10);
        $metricOptions = FeedbackMetric::getMetricOptions();

        return view('admin.guest_feedback.index', compact('metrics', 'metricOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        FeedbackMetric::create([
            'name' => $request->name,
            'is_active' => true,
        ]);

        return back()->with('success', __('messages.guest_feedback_metric_added_successfully'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $metric = FeedbackMetric::findOrFail($id);

        $metric->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', __('messages.guest_feedback_metric_updated_successfully'));
    }

    public function destroy($id)
    {
        $metric = FeedbackMetric::findOrFail($id);
        $metric->delete();

        return back()->with('danger', __('messages.guest_feedback_metric_deleted_successfully'));
    }
}

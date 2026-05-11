<?php

namespace App\Http\Controllers\ChannelManager;

use App\Http\Controllers\Controller;
use App\Models\WebsiteFaqItem;
use Illuminate\Http\Request;

class WebsiteFaqController extends Controller
{
    public function index()
    {
        WebsiteFaqItem::ensureDefaults();

        return view('admin.website_faq.index', [
            'items' => WebsiteFaqItem::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateItem($request);

        WebsiteFaqItem::query()->create([
            ...$validated,
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()
            ->route('setup-sidebar.website_faq.index')
            ->with('success', 'FAQ item created successfully.');
    }

    public function edit(WebsiteFaqItem $websiteFaqItem)
    {
        return view('admin.website_faq.edit', [
            'item' => $websiteFaqItem,
        ]);
    }

    public function update(Request $request, WebsiteFaqItem $websiteFaqItem)
    {
        $validated = $this->validateItem($request);

        $websiteFaqItem->update([
            ...$validated,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('setup-sidebar.website_faq.index')
            ->with('success', 'FAQ item updated successfully.');
    }

    public function destroy(WebsiteFaqItem $websiteFaqItem)
    {
        $websiteFaqItem->delete();

        return redirect()
            ->route('setup-sidebar.website_faq.index')
            ->with('success', 'FAQ item deleted successfully.');
    }

    private function validateItem(Request $request): array
    {
        return $request->validate([
            'question_en' => 'required|string|max:255',
            'question_ar' => 'nullable|string|max:255',
            'answer_en' => 'required|string',
            'answer_ar' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ]);
    }
}

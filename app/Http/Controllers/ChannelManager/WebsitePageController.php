<?php

namespace App\Http\Controllers\ChannelManager;

use App\Http\Controllers\Controller;
use App\Models\WebsitePage;
use Illuminate\Http\Request;

class WebsitePageController extends Controller
{
    public function index()
    {
        WebsitePage::ensureDefaults();

        return view('admin.website_pages.index', [
            'pages' => WebsitePage::query()
                ->whereIn('page_key', WebsitePage::supportedPageKeys())
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function edit(WebsitePage $websitePage)
    {
        abort_unless(in_array($websitePage->page_key, WebsitePage::supportedPageKeys(), true), 404);

        return view('admin.website_pages.edit', [
            'page' => $websitePage,
        ]);
    }

    public function update(Request $request, WebsitePage $websitePage)
    {
        abort_unless(in_array($websitePage->page_key, WebsitePage::supportedPageKeys(), true), 404);

        $validated = $request->validate([
            'nav_label_en' => 'nullable|string|max:255',
            'nav_label_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'hero_title_en' => 'nullable|string|max:255',
            'hero_title_ar' => 'nullable|string|max:255',
            'hero_intro_en' => 'nullable|string',
            'hero_intro_ar' => 'nullable|string',
            'body_en' => 'nullable|string',
            'body_ar' => 'nullable|string',
            'seo_title_en' => 'nullable|string|max:255',
            'seo_title_ar' => 'nullable|string|max:255',
            'seo_description_en' => 'nullable|string|max:500',
            'seo_description_ar' => 'nullable|string|max:500',
            'seo_keywords_en' => 'nullable|string|max:500',
            'seo_keywords_ar' => 'nullable|string|max:500',
            'is_published' => 'nullable|boolean',
            'show_in_navigation' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $websitePage->update([
            ...$validated,
            'is_published' => $request->boolean('is_published'),
            'show_in_navigation' => $request->boolean('show_in_navigation'),
        ]);

        return redirect()
            ->route('setup-sidebar.website_pages.index')
            ->with('success', 'Website page updated successfully.');
    }
}

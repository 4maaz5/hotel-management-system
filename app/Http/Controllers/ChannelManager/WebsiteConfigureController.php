<?php

namespace App\Http\Controllers\ChannelManager;

use App\Http\Controllers\Controller;
use App\Models\WebsiteFaqItem;
use App\Models\WebsitePage;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

class WebsiteConfigureController extends Controller
{
    public function index()
    {
        WebsitePage::ensureDefaults();
        WebsiteFaqItem::ensureDefaults();

        return view('admin.website_configuration.index', [
            'settings' => WebsiteSetting::getSettings(),
            'pages' => WebsitePage::query()
                ->whereIn('page_key', WebsitePage::supportedPageKeys())
                ->orderBy('sort_order')
                ->get(),
            'faqCount' => WebsiteFaqItem::query()->count(),
        ]);
    }

    public function update(Request $request)
    {
        $settings = WebsiteSetting::getSettings();

        $validated = $request->validate([
            'site_tagline_en' => 'nullable|string|max:255',
            'site_tagline_ar' => 'nullable|string|max:255',
            'home_hero_kicker_en' => 'nullable|string|max:255',
            'home_hero_kicker_ar' => 'nullable|string|max:255',
            'home_hero_title_en' => 'nullable|string|max:255',
            'home_hero_title_ar' => 'nullable|string|max:255',
            'home_hero_text_en' => 'nullable|string',
            'home_hero_text_ar' => 'nullable|string',
            'featured_rooms_title_en' => 'nullable|string|max:255',
            'featured_rooms_title_ar' => 'nullable|string|max:255',
            'featured_rooms_intro_en' => 'nullable|string',
            'featured_rooms_intro_ar' => 'nullable|string',
            'facilities_section_title_en' => 'nullable|string|max:255',
            'facilities_section_title_ar' => 'nullable|string|max:255',
            'facilities_section_intro_en' => 'nullable|string',
            'facilities_section_intro_ar' => 'nullable|string',
            'rooms_page_title_en' => 'nullable|string|max:255',
            'rooms_page_title_ar' => 'nullable|string|max:255',
            'rooms_page_intro_en' => 'nullable|string',
            'rooms_page_intro_ar' => 'nullable|string',
            'footer_note_en' => 'nullable|string',
            'footer_note_ar' => 'nullable|string',
            'contact_phone_override' => 'nullable|string|max:30',
            'contact_email_override' => 'nullable|email|max:255',
            'default_seo_title_en' => 'nullable|string|max:255',
            'default_seo_title_ar' => 'nullable|string|max:255',
            'default_seo_description_en' => 'nullable|string|max:500',
            'default_seo_description_ar' => 'nullable|string|max:500',
        ]);

        $settings->update($validated);

        return redirect()
            ->route('setup-sidebar.website_configuration.index')
            ->with('success', 'Website configuration updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ThemeCustomization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ThemeCustomizationController extends Controller
{
    public function index()
    {
        $theme = ThemeCustomization::getTheme();
        return view('admin.theme_customization.index', compact('theme'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'sidebar_bg_color' => 'required|string',
            'sidebar_text_color' => 'required|string',
            'sidebar_active_color' => 'required|string',
            'sidebar_hover_color' => 'required|string',
            'topbar_bg_color' => 'required|string',
            'topbar_text_color' => 'required|string',
            // 'text_color' => 'required|string',
            'font_family' => 'required|string',
            'login_bg_color' => 'required|string',
            'login_text_color' => 'required|string',
            'login_card_bg' => 'required|string',
            // 'button_primary_color' => 'required|string',
            // 'button_secondary_color' => 'required|string',
            // 'card_bg_color' => 'required|string',
            // 'card_border_color' => 'required|string',
            'table_header_bg' => 'required|string',
            'table_header_text' => 'required|string',
            'table_row_even' => 'required|string',
            'table_row_odd' => 'required|string',
            // 'input_bg_color' => 'required|string',
            // 'input_border_color' => 'required|string',
            // 'input_text_color' => 'required|string',
            'dashboard_card_bg' => 'required|string',
            'dashboard_card_border' => 'required|string',
            'dashboard_icon_color' => 'required|string',
            'dashboard_card_title_color' => 'required|string',
            'dashboard_card_text_color' => 'required|string',
        ]);

        $theme = ThemeCustomization::getTheme();

        $data = [
            'sidebar_bg_color' => $request->sidebar_bg_color,
            'sidebar_text_color' => $request->sidebar_text_color,
            'sidebar_active_color' => $request->sidebar_active_color,
            'sidebar_hover_color' => $request->sidebar_hover_color,
            'topbar_bg_color' => $request->topbar_bg_color,
            'topbar_text_color' => $request->topbar_text_color,
            'text_color' => '#212529',
            'font_family' => $request->font_family,
            'login_bg_color' => $request->login_bg_color,
            'login_text_color' => $request->login_text_color,
            'login_card_bg' => $request->login_card_bg,
            // 'button_primary_color' => $request->button_primary_color,
            // 'button_secondary_color' => $request->button_secondary_color,
            // 'card_bg_color' => $request->card_bg_color,
            // 'card_border_color' => $request->card_border_color,
            'table_header_bg' => $request->table_header_bg,
            'table_header_text' => $request->table_header_text,
            'table_row_even' => $request->table_row_even,
            'table_row_odd' => $request->table_row_odd,
            // 'input_bg_color' => $request->input_bg_color,
            // 'input_border_color' => $request->input_border_color,
            // 'input_text_color' => $request->input_text_color,
            'dashboard_card_bg' => $request->dashboard_card_bg,
            'dashboard_card_border' => $request->dashboard_card_border,
            'dashboard_icon_color' => $request->dashboard_icon_color,
            'dashboard_card_title_color' => $request->dashboard_card_title_color,
            'dashboard_card_text_color' => $request->dashboard_card_text_color,
        ];

        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $filename = time() . '_bg.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/img'), $filename);
            $data['background_image'] = 'assets/img/' . $filename;
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_logo.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/img'), $filename);
            $data['logo'] = 'assets/img/' . $filename;
        }

        if ($theme) {
            $theme->update($data);
        } else {
            ThemeCustomization::create($data);
        }

        Cache::forget('theme_settings');

        return back()->with('success', __('messages.theme_settings_updated_successfully'));
    }
}

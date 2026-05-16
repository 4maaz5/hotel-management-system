<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ThemeCustomization extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'company_id',
        'sidebar_bg_color',
        'sidebar_text_color',
        'sidebar_active_color',
        'sidebar_hover_color',
        'topbar_bg_color',
        'topbar_text_color',
        'text_color',
        'font_family',
        'background_image',
        'logo',
        'login_bg_color',
        'login_text_color',
        'login_card_bg',
        'button_primary_color',
        'button_secondary_color',
        'card_bg_color',
        'card_border_color',
        'table_header_bg',
        'table_header_text',
        'table_row_even',
        'table_row_odd',
        'input_bg_color',
        'input_border_color',
        'input_text_color',
        'dashboard_card_bg',
        'dashboard_card_border',
        'dashboard_icon_color',
        'dashboard_card_title_color',
        'dashboard_card_text_color',
    ];

    public static function getTheme()
    {
        if (! Schema::hasTable((new self)->getTable())) {
            return new self(self::defaultAttributes());
        }

        return self::first() ?? self::createDefault();
    }

    public static function createDefault()
    {
        if (! Schema::hasTable((new self)->getTable())) {
            return new self(self::defaultAttributes());
        }

        return self::create(self::defaultAttributes());
    }

    public static function defaultAttributes(): array
    {
        return [
            'sidebar_bg_color' => '#1a237e',
            'sidebar_text_color' => '#e8eaf6',
            'sidebar_active_color' => '#3949ab',
            'sidebar_hover_color' => '#303f9f',
            'topbar_bg_color' => '#A67C37',
            'topbar_text_color' => '#ffffff',
            'text_color' => '#212529',
            'font_family' => 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif',
            'login_bg_color' => '#1a237e',
            'login_text_color' => '#ffffff',
            'login_card_bg' => '#ffffff',
            'button_primary_color' => '#1a237e',
            'button_secondary_color' => '#6c757d',
            'card_bg_color' => '#ffffff',
            'card_border_color' => '#dee2e6',
            'table_header_bg' => '#1a237e',
            'table_header_text' => '#ffffff',
            'table_row_even' => '#f8f9fa',
            'table_row_odd' => '#ffffff',
            'input_bg_color' => '#ffffff',
            'input_border_color' => '#ced4da',
            'input_text_color' => '#212529',
            'dashboard_card_bg' => '#ffffff',
            'dashboard_card_border' => '#dee2e6',
            'dashboard_icon_color' => '#1a237e',
            'dashboard_card_title_color' => '#212529',
            'dashboard_card_text_color' => '#6c757d',
        ];
    }
}

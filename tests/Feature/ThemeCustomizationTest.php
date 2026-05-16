<?php

namespace Tests\Feature;

use App\Models\ThemeCustomization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class ThemeCustomizationTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('theme_customization.edit', 'web');
        Permission::findOrCreate('dashboard.view', 'web');
    }

    public function test_theme_customization_topbar_colors_are_rendered_in_layout(): void
    {
        [$user, $property] = $this->createTenantUserWithProperty();
        $user->givePermissionTo('theme_customization.edit');

        ThemeCustomization::create([
            'company_id' => $user->company_id,
            'sidebar_bg_color' => '#111111',
            'sidebar_text_color' => '#eeeeee',
            'sidebar_active_color' => '#222222',
            'sidebar_hover_color' => '#333333',
            'topbar_bg_color' => '#123456',
            'topbar_text_color' => '#abcdef',
            'text_color' => '#212529',
            'font_family' => 'Arial, Helvetica, sans-serif',
            'login_bg_color' => '#444444',
            'login_text_color' => '#ffffff',
            'login_card_bg' => '#ffffff',
            'button_primary_color' => '#555555',
            'button_secondary_color' => '#666666',
            'card_bg_color' => '#ffffff',
            'card_border_color' => '#dddddd',
            'table_header_bg' => '#777777',
            'table_header_text' => '#ffffff',
            'table_row_even' => '#f8f9fa',
            'table_row_odd' => '#ffffff',
            'input_bg_color' => '#ffffff',
            'input_border_color' => '#ced4da',
            'input_text_color' => '#212529',
            'dashboard_card_bg' => '#ffffff',
            'dashboard_card_border' => '#dddddd',
            'dashboard_icon_color' => '#888888',
            'dashboard_card_title_color' => '#111111',
            'dashboard_card_text_color' => '#666666',
        ]);

        $response = $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->get(route('setup-sidebar.theme_customization.index'));

        $response
            ->assertOk()
            ->assertSee('--topbar-bg: #123456', false)
            ->assertSee('--topbar-text: #abcdef', false)
            ->assertSee('background-repeat: no-repeat;', false)
            ->assertSee('background-size: cover;', false)
            ->assertDontSee('style="background: #1a237e"', false);
    }

    public function test_setup_dashboard_layout_uses_theme_topbar_colors(): void
    {
        [$user, $property] = $this->createTenantUserWithProperty();
        $user->givePermissionTo('dashboard.view');

        ThemeCustomization::create([
            'company_id' => $user->company_id,
            'sidebar_bg_color' => '#111111',
            'sidebar_text_color' => '#eeeeee',
            'sidebar_active_color' => '#222222',
            'sidebar_hover_color' => '#333333',
            'topbar_bg_color' => '#654321',
            'topbar_text_color' => '#fedcba',
            'text_color' => '#212529',
            'font_family' => 'Arial, Helvetica, sans-serif',
            'login_bg_color' => '#444444',
            'login_text_color' => '#ffffff',
            'login_card_bg' => '#ffffff',
            'button_primary_color' => '#555555',
            'button_secondary_color' => '#666666',
            'card_bg_color' => '#ffffff',
            'card_border_color' => '#dddddd',
            'table_header_bg' => '#777777',
            'table_header_text' => '#ffffff',
            'table_row_even' => '#f8f9fa',
            'table_row_odd' => '#ffffff',
            'input_bg_color' => '#ffffff',
            'input_border_color' => '#ced4da',
            'input_text_color' => '#212529',
            'dashboard_card_bg' => '#ffffff',
            'dashboard_card_border' => '#dddddd',
            'dashboard_icon_color' => '#888888',
            'dashboard_card_title_color' => '#111111',
            'dashboard_card_text_color' => '#666666',
        ]);

        $response = $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->get(route('setup-sidebar'));

        $response
            ->assertOk()
            ->assertSee('--topbar-bg: #654321', false)
            ->assertSee('--topbar-text: #fedcba', false)
            ->assertSee('background-repeat: no-repeat;', false)
            ->assertSee('background-size: cover;', false)
            ->assertDontSee('style="background: #1a237e"', false);
    }
}

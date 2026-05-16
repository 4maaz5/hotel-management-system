<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SuperAdminSaasTabsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
        ]);
        $this->admin->assignRole($role);
    }

    public function test_saas_tabs_render_real_dashboard_pages(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Pro',
            'price' => 120,
            'billing_period' => 'monthly',
            'features' => ['custom_branding'],
            'limits' => ['max_users' => 2, 'max_properties' => 1],
            'is_active' => true,
        ]);

        $tenant = Tenant::create([
            'name' => 'Acme Hotels',
            'subdomain' => 'acme',
            'email' => 'owner@acme.test',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(7),
            'subscription_status' => 'active',
            'subscription_plan_id' => $plan->id,
        ]);

        UserActivityLog::create([
            'company_id' => $tenant->id,
            'user_id' => $this->admin->id,
            'module' => 'tenant',
            'action' => 'created',
            'description' => 'Tenant created',
        ]);

        $this->actingAs($this->admin)
            ->get(route('super-admin.analytics.index'))
            ->assertOk()
            ->assertSee('Monthly Recurring Revenue')
            ->assertSee('Acme Hotels')
            ->assertSee('SAR 120.00');

        $this->actingAs($this->admin)
            ->get(route('super-admin.support.index'))
            ->assertOk()
            ->assertSee('System Checks')
            ->assertSee('Acme Hotels');

        $this->actingAs($this->admin)
            ->get(route('super-admin.activity.index'))
            ->assertOk()
            ->assertSee('Tenant created')
            ->assertSee('tenant');
    }

    public function test_plans_only_accept_custom_branding_as_optional_feature(): void
    {
        $this
            ->actingAs($this->admin)
            ->post(route('super-admin.plans.store'), [
                'name' => 'Limit Only',
                'description' => 'Uses limits and one optional add-on.',
                'price' => 99,
                'billing_period' => 'monthly',
                'max_users' => 10,
                'max_properties' => 2,
                'features' => ['custom_branding'],
                'is_active' => '1',
            ])
            ->assertRedirect(route('super-admin.plans.index'));

        $this->assertDatabaseHas('subscription_plans', [
            'name' => 'Limit Only',
        ]);

        $this
            ->actingAs($this->admin)
            ->from(route('super-admin.plans.create'))
            ->post(route('super-admin.plans.store'), [
                'name' => 'Old Feature Plan',
                'description' => 'Should not accept static module features.',
                'price' => 99,
                'billing_period' => 'monthly',
                'max_users' => 10,
                'max_properties' => 2,
                'features' => ['ai_chatbot'],
                'is_active' => '1',
            ])
            ->assertRedirect(route('super-admin.plans.create'))
            ->assertSessionHasErrors('features.0');
    }
}

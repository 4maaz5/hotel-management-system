<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class ProjectTenantScopeTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_branch', 'web');
    }

    public function test_tenant_user_can_create_and_see_tenant_wide_project(): void
    {
        [$user, $property, $tenant] = $this->createTenantUserWithProperty(userOverrides: [
            'branch_id' => null,
        ]);
        $user->givePermissionTo('manage_branch');

        $this->setTenantAndPropertyContext($tenant, $property);

        $this
            ->actingAs($user)
            ->withSession(['current_property_id' => $property->id, 'branch_id' => $property->branch_id])
            ->post(route('dashboard.company.project.store'), [
                'name' => 'Tenant Wide Project',
                'location' => 'Riyadh',
                'project_manager' => 'Manager',
                'value' => 1000,
                'timeline_type' => 'fixed',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'company_id' => $tenant->id,
            'branch_id' => null,
            'name' => 'Tenant Wide Project',
        ]);

        $this
            ->actingAs($user)
            ->withSession(['current_property_id' => $property->id, 'branch_id' => $property->branch_id])
            ->get(route('dashboard.company.project.index'))
            ->assertOk()
            ->assertSee('Tenant Wide Project');
    }

    public function test_project_index_hides_other_tenant_projects(): void
    {
        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty(userOverrides: [
            'branch_id' => null,
        ]);
        [, , $tenantB] = $this->createTenantUserWithProperty();
        $userA->givePermissionTo('manage_branch');

        Project::withoutGlobalScopes()->create([
            'company_id' => $tenantA->id,
            'branch_id' => null,
            'name' => 'Tenant A Project',
            'timeline_type' => 'fixed',
        ]);
        Project::withoutGlobalScopes()->create([
            'company_id' => $tenantB->id,
            'branch_id' => null,
            'name' => 'Tenant B Project',
            'timeline_type' => 'fixed',
        ]);

        $this->setTenantAndPropertyContext($tenantA, $propertyA);

        $this
            ->actingAs($userA)
            ->withSession(['current_property_id' => $propertyA->id, 'branch_id' => $propertyA->branch_id])
            ->get(route('dashboard.company.project.index'))
            ->assertOk()
            ->assertSee('Tenant A Project')
            ->assertDontSee('Tenant B Project');
    }
}

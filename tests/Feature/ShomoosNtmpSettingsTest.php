<?php

namespace Tests\Feature;

use App\Models\NtmpSetting;
use App\Models\NtmpSubmission;
use App\Models\Reservation;
use App\Models\ShomoosSetting;
use App\Models\ShomoosSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class ShomoosNtmpSettingsTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_shomoos_and_ntmp_redirect_when_no_branch_is_selected(): void
    {
        $this->withoutMiddleware();

        $tenant = $this->createTenant();
        $user = \App\Models\User::factory()->create([
            'company_id' => $tenant->id,
            'role' => 'employee',
            'status' => 'active',
        ]);

        $this->setTenantAndPropertyContext($tenant);

        $this
            ->actingAs($user)
            ->get(route('setup-sidebar.shomoos.index'))
            ->assertRedirect(route('setup-sidebar.property.index'))
            ->assertSessionHas('warning', 'Please select or create a branch before configuring Shomoos.');

        $this
            ->actingAs($user)
            ->get(route('setup-sidebar.ntmp.index'))
            ->assertRedirect(route('setup-sidebar.property.index'))
            ->assertSessionHas('warning', 'Please select or create a branch before configuring Saudi NTMP.');
    }

    public function test_blank_secret_fields_keep_existing_encrypted_settings(): void
    {
        $this->withoutMiddleware();

        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $this->setTenantAndPropertyContext($tenant, $property);

        $shomoos = ShomoosSetting::create([
            'company_id' => $tenant->id,
            'branch_id' => $property->branch_id,
            'enabled' => true,
            'mode' => 'simulation',
            'driver' => 'fake',
            'password' => 'shomoos-secret',
        ]);

        $ntmp = NtmpSetting::create([
            'company_id' => $tenant->id,
            'branch_id' => $property->branch_id,
            'enabled' => true,
            'mode' => 'simulation',
            'driver' => 'fake',
            'api_key' => 'api-key-one',
            'password' => 'ntmp-secret',
        ]);

        $this
            ->withSession(['current_property_id' => $property->id, 'branch_id' => $property->branch_id])
            ->actingAs($user)
            ->post(route('setup-sidebar.shomoos.update'), [
                'enabled' => '1',
                'mode' => 'simulation',
                'driver' => 'fake',
                'password' => '',
            ])
            ->assertSessionHasNoErrors();

        $this
            ->withSession(['current_property_id' => $property->id, 'branch_id' => $property->branch_id])
            ->actingAs($user)
            ->post(route('setup-sidebar.ntmp.update'), [
                'enabled' => '1',
                'mode' => 'simulation',
                'driver' => 'fake',
                'api_key' => '',
                'password' => '',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('shomoos-secret', $shomoos->fresh()->password);
        $this->assertSame('api-key-one', $ntmp->fresh()->api_key);
        $this->assertSame('ntmp-secret', $ntmp->fresh()->password);
    }

    public function test_submission_details_are_limited_to_current_branch(): void
    {
        $this->withoutMiddleware();

        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        [$otherUser, $otherProperty, $otherTenant] = $this->createTenantUserWithProperty();
        $otherUnit = $this->createUnitForProperty($otherProperty, $otherTenant);
        $otherReservation = $this->createReservation($otherUser, $otherProperty, $otherUnit);

        $shomoosSubmission = ShomoosSubmission::create([
            'company_id' => $otherTenant->id,
            'branch_id' => $otherProperty->branch_id,
            'reservation_id' => $otherReservation->id,
            'event_type' => 'check_in',
            'status' => 'simulated',
        ]);

        $ntmpSubmission = NtmpSubmission::create([
            'company_id' => $otherTenant->id,
            'branch_id' => $otherProperty->branch_id,
            'reservation_id' => $otherReservation->id,
            'event_type' => 'check_in',
            'status' => 'simulated',
        ]);

        $this->setTenantAndPropertyContext($tenant, $property);

        $this
            ->withSession(['current_property_id' => $property->id, 'branch_id' => $property->branch_id])
            ->actingAs($user)
            ->get(route('setup-sidebar.shomoos.show', $shomoosSubmission))
            ->assertNotFound();

        $this
            ->withSession(['current_property_id' => $property->id, 'branch_id' => $property->branch_id])
            ->actingAs($user)
            ->get(route('setup-sidebar.ntmp.show', $ntmpSubmission))
            ->assertNotFound();
    }
}

<?php

namespace Tests\Feature;

use App\Models\CancelReason;
use App\Models\Penalty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class ReservationPenaltyRouteTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_reservation_page_and_legacy_url_load_cancel_reason_penalties(): void
    {
        [$user, $property, $tenant] = $this->createTenantUserWithProperty();

        $cancelReason = CancelReason::create([
            'company_id' => $tenant->id,
            'name' => 'No Show',
            'description' => 'Guest did not arrive',
            'is_active' => true,
        ]);

        $penalty = Penalty::create([
            'company_id' => $tenant->id,
            'name' => 'No Show Fee',
            'category' => 'cancellation',
            'penalty_type' => 'currency',
            'value' => 100,
            'tax_applicable' => false,
            'is_active' => true,
            'description' => 'No show cancellation fee',
        ]);

        $cancelReason->penalties()->attach($penalty->id, ['auto_apply' => true]);

        $session = [
            'current_property_id' => $property->id,
            'branch_id' => $property->branch_id,
        ];

        $this
            ->actingAs($user)
            ->withSession($session)
            ->get(route('dashboard.reservation.index'))
            ->assertOk()
            ->assertSee(str_replace('/', '\/', route('dashboard.reservation.cancel_reason.penalties', ['id' => '__REASON__'])), false)
            ->assertDontSee('/app/cancel-reason', false);

        $this
            ->actingAs($user)
            ->withSession($session)
            ->getJson(route('dashboard.reservation.cancel_reason.penalties', ['id' => $cancelReason->id]))
            ->assertOk()
            ->assertJsonFragment(['name' => 'No Show Fee'])
            ->assertJsonPath('0.pivot.auto_apply', 1);

        $this
            ->actingAs($user)
            ->withSession($session)
            ->getJson('/app/cancel-reason/'.$cancelReason->id.'/penalties')
            ->assertOk()
            ->assertJsonFragment(['name' => 'No Show Fee']);
    }
}

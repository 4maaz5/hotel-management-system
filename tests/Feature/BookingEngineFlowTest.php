<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\ReservationGuest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class BookingEngineFlowTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_public_booking_creates_guest_reservation_invoice_and_primary_occupant(): void
    {
        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $unit = $this->createUnitForProperty($property, $tenant);
        $paymentMethod = $this->createPaymentMethodConfig($tenant, 'Card');

        $this->clearTenantAndPropertyContext();

        $this
            ->post(route('booking.store', ['property_id' => $property->id]), [
                'unit_id' => $unit->id,
                'check_in' => now()->addDays(2)->toDateString(),
                'check_out' => now()->addDays(4)->toDateString(),
                'adults' => 2,
                'children' => 0,
                'full_name' => 'Website Guest',
                'phone' => '+966500000002',
                'payment_method_id' => $paymentMethod->id,
                'agree_policies' => '1',
            ])
            ->assertRedirect();

        $reservation = Reservation::withoutGlobalScopes()->firstOrFail();
        $guest = Guest::withoutGlobalScopes()->firstOrFail();

        $this->assertSame($tenant->id, $guest->company_id);
        $this->assertSame($property->branch_id, $guest->branch_id);
        $this->assertSame($tenant->id, $reservation->company_id);
        $this->assertSame($property->branch_id, $reservation->branch_id);
        $this->assertSame($unit->id, $reservation->unit_id);
        $this->assertSame($guest->id, $reservation->guest_id);

        $this->assertDatabaseHas('reservation_guests', [
            'company_id' => $tenant->id,
            'branch_id' => $property->branch_id,
            'reservation_id' => $reservation->id,
            'guest_id' => $guest->id,
            'is_primary' => true,
        ]);

        $this->assertTrue(Invoice::withoutGlobalScopes()->where('reservation_id', $reservation->id)->exists());
        $this->assertTrue(ReservationGuest::withoutGlobalScopes()->where('reservation_id', $reservation->id)->exists());
    }
}

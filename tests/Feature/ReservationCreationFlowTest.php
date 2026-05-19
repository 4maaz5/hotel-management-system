<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ReceiptVoucher;
use App\Models\Reservation;
use App\Models\ReservationGuest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class ReservationCreationFlowTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_reservation_creation_uses_company_branch_and_creates_financial_documents(): void
    {
        $this->withoutMiddleware();

        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $unit = $this->createUnitForProperty($property, $tenant);
        $guest = $this->createGuestForProperty($property, $tenant);
        $paymentMethod = $this->createPaymentMethodConfig($tenant, 'Cash');

        $this->setTenantAndPropertyContext($tenant, $property);

        $this
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->actingAs($user)
            ->post(route('dashboard.reservation.store'), [
                'guest_id' => $guest->id,
                'unit_id' => $unit->id,
                'payment_method_id' => $paymentMethod->id,
                'check_in_date' => '2026-04-20',
                'check_out_date' => '2026-04-22',
                'nights' => 2,
                'adults' => 2,
                'children' => 1,
                'reservation_type' => 'daily',
                'daily_rate' => 150,
                'monthly_rate' => 0,
                'total_rent' => 300,
                'discount' => 20,
                'total_taxes_fees' => 30,
                'security_deposit' => 50,
                'paid_amount' => 100,
                'balance' => 260,
                'notes' => 'End-to-end reservation flow',
            ])
            ->assertRedirect(route('dashboard.reservation.index'));

        $reservation = Reservation::withoutGlobalScopes()->firstOrFail();

        $this->assertSame($tenant->id, $reservation->company_id);
        $this->assertSame($property->branch_id, $reservation->branch_id);
        $this->assertSame($guest->id, $reservation->guest_id);
        $this->assertSame($unit->id, $reservation->unit_id);
        $this->assertSame('pending', $reservation->status);

        $invoice = Invoice::withoutGlobalScopes()->where('reservation_id', $reservation->id)->firstOrFail();

        $this->assertSame($tenant->id, $invoice->company_id);
        $this->assertSame($property->branch_id, $invoice->branch_id);
        $this->assertSame(360.0, (float) $invoice->total);
        $this->assertSame('pending', $invoice->status);

        $this->assertSame(
            4,
            InvoiceItem::withoutGlobalScopes()->where('invoice_id', $invoice->id)->count()
        );

        $receipt = ReceiptVoucher::withoutGlobalScopes()->where('reservation_id', $reservation->id)->firstOrFail();

        $this->assertSame($tenant->id, $receipt->company_id);
        $this->assertSame($property->branch_id, $receipt->branch_id);
        $this->assertSame(100.0, (float) $receipt->amount);
        $this->assertSame($paymentMethod->id, $receipt->payment_method_id);
    }

    public function test_check_in_creation_syncs_primary_and_additional_occupants(): void
    {
        $this->withoutMiddleware();

        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $unit = $this->createUnitForProperty($property, $tenant);
        $primaryGuest = $this->createGuestForProperty($property, $tenant, [
            'first_name' => 'Primary',
        ]);
        $additionalGuest = $this->createGuestForProperty($property, $tenant, [
            'first_name' => 'Extra',
        ]);

        $this->setTenantAndPropertyContext($tenant, $property);

        $this
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->actingAs($user)
            ->post(route('dashboard.reservation.store'), [
                'guest_id' => $primaryGuest->id,
                'occupants' => [
                    [
                        'guest_id' => $additionalGuest->id,
                        'relationship' => 'spouse',
                    ],
                ],
                'unit_id' => $unit->id,
                'check_in_date' => '2026-04-20',
                'check_out_date' => '2026-04-22',
                'nights' => 2,
                'adults' => 2,
                'children' => 0,
                'reservation_type' => 'daily',
                'daily_rate' => 150,
                'monthly_rate' => 0,
                'total_rent' => 300,
                'discount' => 0,
                'total_taxes_fees' => 0,
                'security_deposit' => 0,
                'paid_amount' => 0,
                'balance' => 300,
                'reservation_action' => 'check_in',
            ])
            ->assertRedirect(route('dashboard.reservation.index'));

        $reservation = Reservation::withoutGlobalScopes()->latest('id')->firstOrFail();

        $this->assertSame('checked_in', $reservation->status);
        $this->assertNotNull($reservation->checked_in_at);

        $occupants = ReservationGuest::withoutGlobalScopes()
            ->where('reservation_id', $reservation->id)
            ->orderByDesc('is_primary')
            ->get();

        $this->assertCount(2, $occupants);
        $this->assertSame($tenant->id, $occupants->first()->company_id);
        $this->assertSame($property->branch_id, $occupants->first()->branch_id);
        $this->assertSame($primaryGuest->id, $occupants->firstWhere('is_primary', true)?->guest_id);
        $this->assertSame('checked_in', $occupants->firstWhere('is_primary', true)?->check_in_status);
        $this->assertSame($additionalGuest->id, $occupants->firstWhere('is_primary', false)?->guest_id);
        $this->assertSame('spouse', $occupants->firstWhere('is_primary', false)?->relationship);
    }

    public function test_reservation_numbers_can_repeat_across_tenants(): void
    {
        [, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        [, $propertyB, $tenantB] = $this->createTenantUserWithProperty();
        $unitA = $this->createUnitForProperty($propertyA, $tenantA);
        $unitB = $this->createUnitForProperty($propertyB, $tenantB);

        $number = 'RES'.now()->format('ymd').'0001';

        $this->createReservationForTenant($tenantA->id, $propertyA->branch_id, $unitA->id, $number);
        $this->createReservationForTenant($tenantB->id, $propertyB->branch_id, $unitB->id, $number);

        $this->assertSame(2, Reservation::withoutGlobalScopes()
            ->where('reservation_number', $number)
            ->count());

        $this->setTenantAndPropertyContext($tenantB, $propertyB);
        $this->assertSame(
            'RES'.now()->format('ymd').'0002',
            Reservation::generateReservationNumber($tenantB->id)
        );
    }

    private function createReservationForTenant(int $companyId, int $branchId, int $unitId, string $reservationNumber): Reservation
    {
        return Reservation::create([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'reservation_number' => $reservationNumber,
            'unit_id' => $unitId,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDay()->toDateString(),
            'nights' => 1,
            'reservation_type' => 'daily',
            'status' => 'pending',
        ]);
    }
}

<?php

namespace Tests\Concerns;

use App\Models\Block;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Floor;
use App\Models\Guest;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodConfig;
use App\Models\Property;
use App\Models\PropertyCommercialDetail;
use App\Models\PropertyType;
use App\Models\Reservation;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitClass;
use App\Models\UnitType;
use App\Models\UnitTypeCustomization;
use App\Models\UnitTypeRate;
use App\Models\User;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Illuminate\Support\Str;

trait BuildsTenantHotelContext
{
    protected function createTenant(array $overrides = []): Tenant
    {
        static $sequence = 1;

        $index = $sequence++;
        $overrides = $this->normalizeTenantOverrides($overrides);

        return Tenant::create(array_merge([
            'name' => "Tenant {$index}",
            'email' => "tenant{$index}@example.com",
            'subdomain' => "tenant{$index}",
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'subscription_status' => 'active',
        ], $overrides));
    }

    protected function createTenantUserWithProperty(
        ?Tenant $tenant = null,
        array $userOverrides = [],
        array $propertyOverrides = []
    ): array {
        $tenant ??= $this->createTenant();

        app(TenantContext::class)->setTenant($tenant);
        app(PropertyContext::class)->forget();

        $brand = Brand::create([
            'company_id' => $tenant->id,
            'name' => 'Brand '.Str::upper(Str::random(6)),
        ]);

        $branch = Branch::create([
            'company_id' => $tenant->id,
            'brand_id' => $brand->id,
            'name' => 'Branch '.Str::upper(Str::random(6)),
            'location' => 'Riyadh',
            'manager' => 'Manager',
            'email' => 'branch-'.Str::lower(Str::random(8)).'@example.com',
            'phone' => '+9665'.random_int(10000000, 99999999),
            'status' => 'Active',
        ]);

        $user = User::factory()->create(array_merge([
            'company_id' => $tenant->id,
            'branch_id' => $branch->id,
            'role' => 'employee',
            'status' => 'active',
        ], $userOverrides));

        $propertyType = PropertyType::create([
            'code' => 'HOTEL-'.Str::upper(Str::random(8)),
            'name_en' => 'Hotel',
            'name_ar' => 'Hotel AR',
            'is_active' => true,
        ]);

        $property = Property::create(array_merge([
            'company_id' => $tenant->id,
            'branch_id' => $branch->id,
            'owner_user_id' => $user->id,
            'property_type_id' => $propertyType->id,
            'property_name_en' => 'Riyadh Hotel',
            'property_name_ar' => 'Riyadh Hotel AR',
            'report_name_en' => 'Riyadh Hotel Report',
            'report_name_ar' => 'Riyadh Hotel Report AR',
            'property_code' => 'PROP-'.Str::upper(Str::random(8)),
            'status' => 'ACTIVE',
            'account_expiry_date' => $tenant->end_date,
            'time_zone' => 'Asia/Riyadh',
            'latitude' => 24.71360000,
            'longitude' => 46.67530000,
        ], $propertyOverrides));

        PropertyCommercialDetail::create([
            'company_id' => $tenant->id,
            'branch_id' => $branch->id,
            'registration_number' => 'REG-'.Str::upper(Str::random(8)),
            'activity_license_number' => 'LIC-'.Str::upper(Str::random(6)),
            'vat_registration_number' => 'VAT-'.Str::upper(Str::random(8)),
        ]);

        $property->users()->syncWithoutDetaching([$user->id]);
        app(PropertyContext::class)->setProperty($property);

        return [$user, $property, $tenant, $branch];
    }

    protected function createUnitForProperty(Property $property, Tenant $tenant, array $overrides = []): Unit
    {
        app(TenantContext::class)->setTenant($tenant);
        app(PropertyContext::class)->setProperty($property);

        $suffix = Str::upper(Str::random(6));

        $unitClass = UnitClass::create([
            'name' => "Standard {$suffix}",
            'is_active' => true,
        ]);

        $unitType = UnitType::create([
            'name' => "Room {$suffix}",
            'is_active' => true,
        ]);

        $unitTypeCustomization = UnitTypeCustomization::create([
            'company_id' => $tenant->id,
            'unit_type_id' => $unitType->id,
            'name' => "Room {$suffix}",
            'base_occupancy' => 4,
            'is_published_online' => true,
        ]);

        UnitTypeRate::create([
            'company_id' => $tenant->id,
            'unit_type_id' => $unitType->id,
            'low_weekday_rate' => 100,
            'high_weekday_rate' => 120,
            'daily_min_rate' => 80,
            'monthly_rate' => 2000,
            'monthly_min_rate' => 1800,
            'is_active' => true,
        ]);

        $block = Block::create([
            'company_id' => $tenant->id,
            'branch_id' => $property->branch_id,
            'name' => "B{$suffix}",
            'is_active' => true,
        ]);

        $floor = Floor::create([
            'company_id' => $tenant->id,
            'branch_id' => $property->branch_id,
            'block_id' => $block->id,
            'name' => "F{$suffix}",
            'order' => 1,
            'is_active' => true,
        ]);

        return Unit::create(array_merge([
            'company_id' => $tenant->id,
            'branch_id' => $property->branch_id,
            'unit_number' => 'U'.Str::upper(Str::random(8)),
            'unit_class_id' => $unitClass->id,
            'unit_type_id' => $unitTypeCustomization->id,
            'block_id' => $block->id,
            'floor_id' => $floor->id,
            'base_occupancy' => 4,
            'is_active' => true,
            'housekeeping_status' => 'clean',
        ], $overrides));
    }

    protected function createGuestForProperty(Property $property, Tenant $tenant, array $overrides = []): Guest
    {
        app(TenantContext::class)->setTenant($tenant);
        app(PropertyContext::class)->setProperty($property);

        return Guest::create(array_merge([
            'company_id' => $tenant->id,
            'branch_id' => $property->branch_id,
            'first_name' => 'John',
            'last_name' => 'Guest',
            'email' => 'guest-'.Str::lower(Str::random(8)).'@example.com',
            'mobile_number' => '555000123',
            'is_active' => true,
        ], $overrides));
    }

    protected function createPaymentMethodConfig(Tenant $tenant, string $name): PaymentMethodConfig
    {
        app(TenantContext::class)->setTenant($tenant);

        $paymentMethod = PaymentMethod::create([
            'name' => $name,
        ]);

        return PaymentMethodConfig::create([
            'company_id' => $tenant->id,
            'payment_method_id' => $paymentMethod->id,
            'description' => "{$name} config",
            'is_active' => true,
        ]);
    }

    protected function createReservation(User $user, Property $property, Unit $unit, array $overrides = []): Reservation
    {
        app(TenantContext::class)->setTenantId((int) $property->company_id);
        app(PropertyContext::class)->setProperty($property);

        return Reservation::create(array_merge([
            'company_id' => $property->company_id,
            'reservation_number' => 'RES-TEST-'.Str::upper(Str::random(8)),
            'branch_id' => $property->branch_id,
            'unit_id' => $unit->id,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDay()->toDateString(),
            'nights' => 1,
            'adults' => 1,
            'children' => 0,
            'reservation_type' => 'daily',
            'daily_rate' => 100,
            'monthly_rate' => 0,
            'total_rent' => 100,
            'discount' => 0,
            'total_taxes_fees' => 0,
            'security_deposit' => 0,
            'paid_amount' => 0,
            'balance' => 100,
            'subtotal' => 100,
            'grand_total' => 100,
            'status' => 'pending',
            'booking_date' => now()->toDateString(),
            'created_by' => $user->id,
        ], $overrides));
    }

    protected function setTenantAndPropertyContext(Tenant $tenant, ?Property $property = null): void
    {
        app(TenantContext::class)->setTenant($tenant);

        if ($property) {
            app(PropertyContext::class)->setProperty($property);
        } else {
            app(PropertyContext::class)->forget();
        }
    }

    protected function clearTenantAndPropertyContext(): void
    {
        app(TenantContext::class)->forget();
        app(PropertyContext::class)->forget();
    }

    private function normalizeTenantOverrides(array $overrides): array
    {
        if (array_key_exists('status', $overrides)) {
            $overrides['subscription_status'] = $overrides['status'];
            unset($overrides['status']);
        }

        return $overrides;
    }
}

<?php

namespace Tests\Feature;

use App\Models\Categories;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\ItemCategory;
use App\Models\OutletItem;
use App\Models\OutletSetup;
use App\Models\PaymentMethodConfig;
use App\Models\PaymentVoucher;
use App\Models\Payroll;
use App\Models\PlatformRevenue;
use App\Models\PlatformSubscription;
use App\Models\Product;
use App\Models\ReceiptVoucher;
use App\Models\ReservationSourceMaster;
use App\Models\ReservationSourceSetting;
use App\Models\TaxFeeCustomization;
use App\Models\ThirdPartyPlatform;
use App\Models\UnitCustomRate;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class ModuleIsolationRegressionTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_warehouse_inventory_rejects_other_tenant_records(): void
    {
        $this->withoutMiddleware();

        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        [$userB, $propertyB, $tenantB] = $this->createTenantUserWithProperty();

        $this->setTenantAndPropertyContext($tenantA, $propertyA);
        $categoryA = Categories::create(['company_id' => $tenantA->id, 'name' => 'Tenant A Linen']);
        $productA = Product::create(['company_id' => $tenantA->id, 'category_id' => $categoryA->id, 'name' => 'A Towel', 'unit' => 'pcs']);
        $warehouseA = Warehouse::create(['company_id' => $tenantA->id, 'branch_id' => $propertyA->branch_id, 'name' => 'A Warehouse']);

        $this->setTenantAndPropertyContext($tenantB, $propertyB);
        $categoryB = Categories::create(['company_id' => $tenantB->id, 'name' => 'Tenant B Linen']);
        $productB = Product::create(['company_id' => $tenantB->id, 'category_id' => $categoryB->id, 'name' => 'B Towel', 'unit' => 'pcs']);
        $warehouseB = Warehouse::create(['company_id' => $tenantB->id, 'branch_id' => $propertyB->branch_id, 'name' => 'B Warehouse']);

        $this->setTenantAndPropertyContext($tenantA, $propertyA);

        $this->actingAs($userA)
            ->withSession($this->propertySession($propertyA))
            ->put(route('dashboard.products.update'), [
                'id' => $productB->id,
                'name' => 'Changed By A',
                'category_id' => $categoryA->id,
                'unit' => 'pcs',
            ])
            ->assertNotFound();

        $this->actingAs($userA)
            ->withSession($this->propertySession($propertyA))
            ->from(route('dashboard.inventory.index'))
            ->post(route('dashboard.inventories.store'), [
                'warehouse_id' => $warehouseB->id,
                'product_id' => $productA->id,
                'quantity' => 1,
            ])
            ->assertInvalid(['warehouse_id']);

        $this->actingAs($userA)
            ->withSession($this->propertySession($propertyA))
            ->from(route('dashboard.inventory.index'))
            ->post(route('dashboard.inventories.store'), [
                'warehouse_id' => $warehouseA->id,
                'product_id' => $productB->id,
                'quantity' => 1,
            ])
            ->assertInvalid(['product_id']);

        $this->assertSame(0, Inventory::count());
    }

    public function test_outlet_items_reject_other_property_outlet_and_category(): void
    {
        $this->withoutMiddleware();

        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        [$userB, $propertyB, $tenantB] = $this->createTenantUserWithProperty();

        $this->setTenantAndPropertyContext($tenantB, $propertyB);
        $outletB = OutletSetup::create([
            'company_id' => $tenantB->id,
            'branch_id' => $propertyB->branch_id,
            'operating_status' => 'open',
            'outlet_code' => '101',
            'name' => 'Tenant B Cafe',
        ]);
        $categoryB = ItemCategory::create([
            'company_id' => $tenantB->id,
            'outlet_id' => $outletB->id,
            'name' => 'Tenant B Drinks',
            'ntmp_category' => 'food',
        ]);

        $this->setTenantAndPropertyContext($tenantA, $propertyA);

        $this->actingAs($userA)
            ->withSession($this->propertySession($propertyA))
            ->from(route('setup-sidebar.items.index'))
            ->post(route('setup-sidebar.items.store'), [
                'name' => 'Cross Tenant Coffee',
                'type' => 'food',
                'outlet_id' => $outletB->id,
                'category_id' => $categoryB->id,
                'price' => 10,
            ])
            ->assertInvalid(['outlet_id', 'category_id']);

        $this->assertSame(0, OutletItem::count());
    }

    public function test_unit_custom_rates_reject_other_property_units(): void
    {
        $this->withoutMiddleware();

        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        [$userB, $propertyB, $tenantB] = $this->createTenantUserWithProperty();
        $unitB = $this->createUnitForProperty($propertyB, $tenantB);

        $this->setTenantAndPropertyContext($tenantA, $propertyA);

        $this->actingAs($userA)
            ->withSession($this->propertySession($propertyA))
            ->from(route('setup-sidebar.base_rate.index'))
            ->post(route('setup-sidebar.custom_rate.store'), [
                'unit_id' => $unitB->id,
                'unit_type_id' => $unitB->unit_type_id,
                'low_weekday_rate' => 100,
                'high_weekday_rate' => 150,
                'daily_min_rate' => 80,
                'monthly_rate' => 1000,
                'monthly_min_rate' => 900,
            ])
            ->assertInvalid(['unit_id']);

        $this->assertSame(0, UnitCustomRate::withoutGlobalScopes()->where('unit_id', $unitB->id)->count());
    }

    public function test_payroll_cannot_use_other_tenant_employee_or_payroll(): void
    {
        $this->withoutMiddleware();

        [$userA, $propertyA, $tenantA, $branchA] = $this->createTenantUserWithProperty();
        [$userB, $propertyB, $tenantB, $branchB] = $this->createTenantUserWithProperty();

        $this->setTenantAndPropertyContext($tenantA, $propertyA);
        $employeeA = $this->createEmployee($tenantA->id, $branchA->id);

        $this->setTenantAndPropertyContext($tenantB, $propertyB);
        $employeeB = $this->createEmployee($tenantB->id, $branchB->id);
        $payrollB = Payroll::create([
            'employee_id' => $employeeB->id,
            'month' => '2026-05',
            'basic_salary' => 1000,
            'allowance' => 0,
            'deductions' => 0,
            'commission_amount' => 0,
            'total_amount' => 1000,
            'net_pay' => 1000,
            'status' => 'Pending',
        ]);

        $this->setTenantAndPropertyContext($tenantA, $propertyA);

        $this->actingAs($userA)
            ->withSession($this->propertySession($propertyA))
            ->post(route('dashboard.payroll.payslip.store'), [
                'employee_id' => $employeeB->id,
                'month' => '2026-05',
                'commission' => 0,
                'total_amount' => 1000,
                'deductions' => 0,
                'basic_salary' => 1000,
                'allowance' => 0,
                'net_pay' => 1000,
                'status' => 'Pending',
            ])
            ->assertInvalid(['employee_id']);

        $this->actingAs($userA)
            ->withSession($this->propertySession($propertyA))
            ->put(route('dashboard.payroll.payslip.update', $payrollB->id), [
                'month' => '2026-05',
                'basic_salary' => 2000,
                'allowance' => 0,
                'net_pay' => 2000,
                'status' => 'Paid',
            ])
            ->assertNotFound();

        $this->actingAs($userA)
            ->withSession($this->propertySession($propertyA))
            ->get(route('payroll.getEmployeeData', [$employeeB->id, '2026-05']))
            ->assertNotFound();

        $this->assertDatabaseMissing('payrolls', [
            'employee_id' => $employeeA->id,
            'month' => '2026-05',
        ]);
    }

    public function test_subscription_revenue_rejects_other_tenant_subscription(): void
    {
        $this->withoutMiddleware();

        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        [, $propertyB, $tenantB] = $this->createTenantUserWithProperty();

        $this->setTenantAndPropertyContext($tenantB, $propertyB);
        $platformB = ThirdPartyPlatform::create([
            'company_id' => $tenantB->id,
            'name' => 'Tenant B OTA',
        ]);
        $subscriptionB = PlatformSubscription::create([
            'third_party_platform_id' => $platformB->id,
            'company_id' => $tenantB->id,
            'branch_id' => $propertyB->branch_id,
            'subscription_start_date' => '2026-01-01',
            'subscription_end_date' => '2026-12-31',
            'contract_amount' => 10000,
            'commission_percentage' => 10,
            'status' => 'active',
        ]);

        $this->setTenantAndPropertyContext($tenantA, $propertyA);

        $this->actingAs($userA)
            ->withSession($this->propertySession($propertyA))
            ->from(route('dashboard.company.revenue.index'))
            ->post(route('platform-revenues.store'), [
                'subscription_id' => $subscriptionB->id,
                'amount_collected' => 1000,
                'payment_date' => '2026-05-01',
            ])
            ->assertInvalid(['subscription_id']);

        $this->assertSame(0, PlatformRevenue::count());
    }

    public function test_vouchers_and_settings_do_not_open_other_tenant_records(): void
    {
        $this->withoutMiddleware();

        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        [, $propertyB, $tenantB] = $this->createTenantUserWithProperty();

        $this->setTenantAndPropertyContext($tenantB, $propertyB);
        $paymentConfigB = $this->createPaymentMethodConfig($tenantB, 'Tenant B Cash');
        $receiptB = ReceiptVoucher::create([
            'company_id' => $tenantB->id,
            'branch_id' => $propertyB->branch_id,
            'payment_method_id' => $paymentConfigB->id,
            'voucher_number' => 'RV-B-001',
            'amount' => 100,
            'received_from_name' => 'Tenant B Guest',
            'purpose' => 'Room payment',
            'date' => '2026-05-01',
            'time' => '10:00:00',
            'status' => 'active',
        ]);
        $paymentB = PaymentVoucher::create([
            'company_id' => $tenantB->id,
            'branch_id' => $propertyB->branch_id,
            'payment_method_id' => $paymentConfigB->id,
            'voucher_number' => 'PV-B-001',
            'purpose' => 'Supplier payment',
            'vendor_name' => 'Tenant B Vendor',
            'amount' => 75,
            'date' => '2026-05-01',
            'time' => '10:00:00',
            'status' => 'active',
        ]);
        $taxB = TaxFeeCustomization::create([
            'company_id' => $tenantB->id,
            'type' => 'tax',
            'custom_name' => 'Tenant B VAT',
            'method' => 'percentage',
            'amount' => 15,
            'applied_on' => ['rent'],
            'start_date' => '2026-01-01',
        ]);
        $sourceMaster = ReservationSourceMaster::create([
            'name' => 'Direct',
            'channel_type' => 'direct',
            'is_active' => true,
        ]);
        $sourceSettingB = ReservationSourceSetting::create([
            'company_id' => $tenantB->id,
            'master_source_id' => $sourceMaster->id,
            'status' => true,
            'report_name' => 'Tenant B Direct',
            'tax_mode' => 'auto',
        ]);

        $this->setTenantAndPropertyContext($tenantA, $propertyA);
        $paymentConfigA = $this->createPaymentMethodConfig($tenantA, 'Tenant A Cash');

        $session = $this->propertySession($propertyA);

        $this->actingAs($userA)->withSession($session)
            ->get(route('dashboard.receipt.show', $receiptB->id))
            ->assertNotFound();

        $this->actingAs($userA)->withSession($session)
            ->get(route('dashboard.payment.show', $paymentB->id))
            ->assertNotFound();

        $this->actingAs($userA)->withSession($session)
            ->put(route('setup-sidebar.taxes.update', $taxB->id), [
                'type' => 'tax',
                'custom_name' => 'Changed By A',
                'method' => 'percentage',
                'amount' => 5,
                'start_date' => '2026-01-01',
                'applied_on' => ['rent'],
            ])
            ->assertNotFound();

        $this->actingAs($userA)->withSession($session)
            ->put(route('setup-sidebar.payments.update', $paymentConfigB->id), [
                'description' => 'Changed By A',
            ])
            ->assertNotFound();

        $this->actingAs($userA)->withSession($session)
            ->put(route('setup-sidebar.reservation_source.update', $sourceSettingB->id), [
                'report_name' => 'Changed By A',
                'tax_mode' => 'auto',
                'commission_rate' => 0,
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('payment_method_configs', [
            'id' => $paymentConfigA->id,
            'company_id' => $tenantA->id,
        ]);
    }

    private function createEmployee(int $companyId, int $branchId): Employee
    {
        return Employee::create([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'first_name' => 'Employee',
            'last_name' => Str::upper(Str::random(6)),
            'employee_id' => 'EMP-'.Str::upper(Str::random(8)),
            'email' => 'employee-'.Str::lower(Str::random(8)).'@example.com',
            'base_salary' => 1000,
        ]);
    }

    private function propertySession($property): array
    {
        return [
            'current_property_id' => $property->id,
            'branch_id' => $property->branch_id,
        ];
    }
}

<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Income;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class CommissionReportExportTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_commission_report_excel_and_pdf_downloads_are_scoped_and_working(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_finance', 'web');

        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $user->givePermissionTo('manage_finance');

        $this->setTenantAndPropertyContext($tenant, $property);

        $employee = Employee::create([
            'company_id' => $tenant->id,
            'branch_id' => $property->branch_id,
            'first_name' => 'Commission',
            'last_name' => 'User',
            'employee_id' => 'EMP-COM-001',
            'commission_percentage' => 10,
        ]);

        Income::create([
            'branch_id' => $property->branch_id,
            'employee_id' => $employee->id,
            'type' => 'Sales',
            'amount' => 500,
            'income_date' => now()->toDateString(),
        ]);

        $session = [
            'current_property_id' => $property->id,
            'branch_id' => $property->branch_id,
        ];

        $this->actingAs($user)->withSession($session)
            ->get(route('finance.reports.commission.excel'))
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->actingAs($user)->withSession($session)
            ->get(route('finance.reports.commission.pdf'))
            ->assertOk()
            ->assertHeader('content-disposition');
    }
}

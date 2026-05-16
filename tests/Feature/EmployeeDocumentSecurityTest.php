<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class EmployeeDocumentSecurityTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_documents', 'web');
    }

    public function test_employee_document_uploads_are_stored_on_private_disk(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        [$user, $property, $tenant, $branch] = $this->createTenantUserWithProperty();
        $user->givePermissionTo('manage_documents');
        $employee = $this->createEmployee($tenant->id, $branch->id);
        $this->setTenantAndPropertyContext($tenant, $property);

        $response = $this
            ->actingAs($user)
            ->withSession($this->propertySession($property))
            ->postJson(route('dashboard.document.employee.store'), [
                'employee_id' => $employee->id,
                'type' => 'Iqama',
                'issue_date' => '2026-05-01',
                'expiry_date' => '2027-05-01',
                'doc_number' => 'IQ-1001',
                'file' => UploadedFile::fake()->create('iqama.pdf', 10, 'application/pdf'),
            ]);

        $response->assertOk()->assertJsonPath('success', true);

        $document = EmployeeDocument::firstOrFail();

        Storage::disk('local')->assertExists($document->file_path);
        Storage::disk('public')->assertMissing($document->file_path);
        $this->assertStringStartsWith('employee_documents/', $document->file_path);
    }

    public function test_authorized_user_can_stream_private_employee_document(): void
    {
        Storage::fake('local');

        [$user, $property, $tenant, $branch] = $this->createTenantUserWithProperty();
        $user->givePermissionTo('manage_documents');
        $employee = $this->createEmployee($tenant->id, $branch->id);
        $path = 'employee_documents/secure-document.pdf';
        Storage::disk('local')->put($path, 'private employee document');

        $document = EmployeeDocument::create([
            'employee_id' => $employee->id,
            'type' => 'Iqama',
            'file_path' => $path,
            'document_number' => 'IQ-1002',
            'issue_date' => '2026-05-01',
            'expiration_date' => '2027-05-01',
        ]);

        $this->setTenantAndPropertyContext($tenant, $property);

        $this
            ->actingAs($user)
            ->withSession($this->propertySession($property))
            ->get(route('dashboard.document.employee.file', $document))
            ->assertOk();
    }

    public function test_other_tenant_cannot_stream_employee_document(): void
    {
        Storage::fake('local');

        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        $userA->givePermissionTo('manage_documents');
        [, $propertyB, $tenantB, $branchB] = $this->createTenantUserWithProperty();
        $employeeB = $this->createEmployee($tenantB->id, $branchB->id);
        $path = 'employee_documents/other-tenant.pdf';
        Storage::disk('local')->put($path, 'other tenant document');

        $documentB = EmployeeDocument::create([
            'employee_id' => $employeeB->id,
            'type' => 'Passport',
            'file_path' => $path,
            'document_number' => 'PASS-2001',
            'issue_date' => '2026-05-01',
            'expiration_date' => '2027-05-01',
        ]);

        $this->setTenantAndPropertyContext($tenantA, $propertyA);

        $this
            ->actingAs($userA)
            ->withSession($this->propertySession($propertyA))
            ->get(route('dashboard.document.employee.file', $documentB))
            ->assertNotFound();
    }

    public function test_guest_cannot_stream_employee_document(): void
    {
        [$user, $property, $tenant, $branch] = $this->createTenantUserWithProperty();
        $employee = $this->createEmployee($tenant->id, $branch->id);

        $document = EmployeeDocument::create([
            'employee_id' => $employee->id,
            'type' => 'Iqama',
            'file_path' => 'employee_documents/guest-blocked.pdf',
            'document_number' => 'IQ-1003',
            'issue_date' => '2026-05-01',
            'expiration_date' => '2027-05-01',
        ]);

        $this
            ->get(route('dashboard.document.employee.file', $document))
            ->assertRedirect(route('login'));
    }

    private function createEmployee(int $companyId, int $branchId): Employee
    {
        return Employee::create([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'first_name' => 'Document',
            'last_name' => 'Owner',
            'employee_id' => 'EMP-DOC-'.uniqid(),
            'email' => 'employee-doc-'.uniqid().'@example.com',
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

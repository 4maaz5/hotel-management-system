<?php

namespace Tests\Feature;

use App\Models\Housekeeper;
use App\Models\StaffAttendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class MobileStaffAttendanceTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_mobile_housekeeper_can_check_in_and_check_out_for_assigned_branch(): void
    {
        [$user, $property, $tenant] = $this->createTenantUserWithProperty();

        Housekeeper::create([
            'company_id' => $tenant->id,
            'branch_id' => $property->branch_id,
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        $login = $this->postJson('/api/mobile/login', [
            'login' => $user->email,
            'password' => 'password',
            'device_name' => 'Feature Test Device',
            'branch_id' => $property->branch_id,
        ]);

        $login->assertOk();
        $token = $login->json('access_token');

        $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/mobile/attendance/check-in', [
                'latitude' => (float) $property->latitude,
                'longitude' => (float) $property->longitude,
                'accuracy_meters' => 10,
            ])
            ->assertCreated()
            ->assertJsonPath('attendance.branch_id', $property->branch_id)
            ->assertJsonPath('attendance.status', 'checked_in');

        $attendance = StaffAttendance::withoutGlobalScopes()->firstOrFail();

        $this->assertSame($tenant->id, $attendance->company_id);
        $this->assertSame($property->branch_id, $attendance->branch_id);
        $this->assertSame($user->id, $attendance->user_id);

        $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/mobile/attendance/check-out', [
                'latitude' => (float) $property->latitude,
                'longitude' => (float) $property->longitude,
                'accuracy_meters' => 10,
            ])
            ->assertOk()
            ->assertJsonPath('attendance.status', 'checked_out');

        $this->assertSame('checked_out', $attendance->fresh()->status);
        $this->assertNotNull($attendance->fresh()->check_out_at);
    }
}

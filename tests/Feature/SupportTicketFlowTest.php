<?php

namespace Tests\Feature;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class SupportTicketFlowTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_tenant_and_saas_admin_can_chat_on_support_ticket_with_attachment(): void
    {
        Storage::fake('public');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        [$tenantUser] = $this->createTenantUserWithProperty();
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($tenantUser)->post(route('support.tickets.store'), [
            'subject' => 'Reservation report issue',
            'category' => 'Reservation',
            'priority' => 'high',
            'body' => '<p>Report total is <strong>wrong</strong><script>alert(1)</script></p>',
            'attachments' => [
                UploadedFile::fake()->image('report-screen.png'),
            ],
        ]);

        $ticket = SupportTicket::with(['messages.attachments'])->firstOrFail();
        $response->assertRedirect(route('support.tickets.show', $ticket));

        $this->assertSame('Reservation report issue', $ticket->subject);
        $this->assertSame('reservation', $ticket->support_area);
        $this->assertSame('open', $ticket->status);
        $this->assertStringContainsString('<strong>wrong</strong>', $ticket->messages->first()->body);
        $this->assertStringNotContainsString('<script>', $ticket->messages->first()->body);
        Storage::disk('public')->assertExists($ticket->messages->first()->attachments->first()->path);

        $this->actingAs($admin)
            ->post(route('super-admin.support.reply', $ticket), [
                'body' => '<p>Thanks, we are checking the calculation.</p>',
                'status' => 'pending',
            ])
            ->assertRedirect(route('super-admin.support.show', $ticket));

        $ticket->refresh();
        $this->assertSame('pending', $ticket->status);
        $this->assertCount(2, $ticket->messages()->get());

        $this->actingAs($tenantUser)
            ->get(route('support.tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Thanks, we are checking the calculation.');
    }

    public function test_reservation_support_create_page_loads_editor_assets(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        [$tenantUser] = $this->createTenantUserWithProperty();

        $this->actingAs($tenantUser)
            ->get(route('support.tickets.create'))
            ->assertOk()
            ->assertSee('bundles/summernote/summernote-bs4.css', false)
            ->assertSee('js-support-summernote', false)
            ->assertSee('Message');
    }

    public function test_saas_admin_support_queue_lists_tickets(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        [$tenantUser] = $this->createTenantUserWithProperty();
        $admin = $this->createSuperAdmin();

        $this->actingAs($tenantUser)->post(route('support.tickets.store'), [
            'subject' => 'Payroll import help',
            'priority' => 'normal',
            'body' => '<p>Please check payroll import.</p>',
        ]);

        $this->actingAs($admin)
            ->get(route('super-admin.support.index'))
            ->assertOk()
            ->assertSee('Support Tickets')
            ->assertSee('Payroll import help');
    }

    public function test_hr_support_routes_render_hr_ticket_pages(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        [$tenantUser] = $this->createTenantUserWithProperty();

        $response = $this->actingAs($tenantUser)->post(route('dashboard.support.tickets.store'), [
            'subject' => 'Attendance device issue',
            'category' => 'HR',
            'priority' => 'normal',
            'body' => '<p>The attendance scanner is not syncing.</p>',
        ]);

        $ticket = SupportTicket::firstOrFail();
        $response->assertRedirect(route('dashboard.support.tickets.show', $ticket));

        $this->actingAs($tenantUser)
            ->get(route('dashboard.support.tickets.index'))
            ->assertOk()
            ->assertSee('HR Support Tickets')
            ->assertSee('Attendance device issue');

        $this->actingAs($tenantUser)
            ->get(route('dashboard.support.tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Your HR team')
            ->assertSee('The attendance scanner is not syncing.');
    }

    public function test_reservation_and_hr_dashboards_only_show_their_own_support_tickets(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        [$tenantUser] = $this->createTenantUserWithProperty();

        $this->actingAs($tenantUser)->post(route('support.tickets.store'), [
            'subject' => 'Reservation calendar issue',
            'category' => 'Reservation',
            'priority' => 'normal',
            'body' => '<p>Calendar is not loading.</p>',
        ]);

        $this->actingAs($tenantUser)->post(route('dashboard.support.tickets.store'), [
            'subject' => 'HR payroll issue',
            'category' => 'HR',
            'priority' => 'normal',
            'body' => '<p>Payroll export is not loading.</p>',
        ]);

        $reservationTicket = SupportTicket::where('support_area', 'reservation')->firstOrFail();
        $hrTicket = SupportTicket::where('support_area', 'hr')->firstOrFail();

        $this->actingAs($tenantUser)
            ->get(route('support.tickets.index'))
            ->assertOk()
            ->assertSee('Reservation calendar issue')
            ->assertDontSee('HR payroll issue');

        $this->actingAs($tenantUser)
            ->get(route('dashboard.support.tickets.index'))
            ->assertOk()
            ->assertSee('HR payroll issue')
            ->assertDontSee('Reservation calendar issue');

        $this->actingAs($tenantUser)
            ->get(route('support.tickets.show', $hrTicket))
            ->assertNotFound();

        $this->actingAs($tenantUser)
            ->get(route('dashboard.support.tickets.show', $reservationTicket))
            ->assertNotFound();
    }

    public function test_support_unread_badges_follow_ticket_replies(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        [$tenantUser] = $this->createTenantUserWithProperty();
        $admin = $this->createSuperAdmin();

        $this->actingAs($tenantUser)->post(route('support.tickets.store'), [
            'subject' => 'Reservation unread badge',
            'priority' => 'normal',
            'body' => '<p>Please check the reservation badge.</p>',
        ]);

        $reservationTicket = SupportTicket::where('support_area', 'reservation')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('super-admin.support.index'))
            ->assertOk()
            ->assertSee('support-unread-count', false)
            ->assertSee('data-support-unread-count="1"', false);

        $this->actingAs($admin)
            ->get(route('super-admin.support.show', $reservationTicket))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('super-admin.support.index'))
            ->assertOk()
            ->assertDontSee('support-unread-count', false);

        $this->actingAs($admin)->post(route('super-admin.support.reply', $reservationTicket), [
            'body' => '<p>The reservation badge is fixed.</p>',
            'status' => 'pending',
        ]);

        $this->actingAs($tenantUser)
            ->get(route('support.tickets.index'))
            ->assertOk()
            ->assertSee('reservation-support-unread-count', false)
            ->assertSee('data-reservation-support-unread-count="1"', false);

        $this->actingAs($tenantUser)
            ->get(route('support.tickets.show', $reservationTicket))
            ->assertOk();

        $this->actingAs($tenantUser)
            ->get(route('support.tickets.index'))
            ->assertOk()
            ->assertDontSee('reservation-support-unread-count', false);

        $this->actingAs($tenantUser)->post(route('dashboard.support.tickets.store'), [
            'subject' => 'HR unread badge',
            'priority' => 'normal',
            'body' => '<p>Please check the HR badge.</p>',
        ]);

        $hrTicket = SupportTicket::where('support_area', 'hr')->firstOrFail();

        $this->actingAs($admin)->post(route('super-admin.support.reply', $hrTicket), [
            'body' => '<p>The HR badge is fixed.</p>',
            'status' => 'pending',
        ]);

        $this->actingAs($tenantUser)
            ->get(route('dashboard.support.tickets.index'))
            ->assertOk()
            ->assertSee('hr-support-unread-count', false)
            ->assertSee('data-hr-support-unread-count="1"', false);

        $this->actingAs($tenantUser)
            ->get(route('dashboard.support.tickets.show', $hrTicket))
            ->assertOk();

        $this->actingAs($tenantUser)
            ->get(route('dashboard.support.tickets.index'))
            ->assertOk()
            ->assertDontSee('hr-support-unread-count', false);
    }

    private function createSuperAdmin(): User
    {
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $admin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
        ]);
        $admin->assignRole($role);

        return $admin;
    }
}

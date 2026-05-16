<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Models\SupportTicketMessage;
use App\Models\Tenant;
use App\Support\SupportTicketRichText;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function index(Request $request): View
    {
        $today = today();

        $tenants = Tenant::query()
            ->with(['plan'])
            ->withCount(['users', 'properties'])
            ->latest()
            ->get();

        $withoutOwner = $tenants->filter(function (Tenant $tenant): bool {
            return ! $tenant->users()
                ->whereHas('roles', fn ($query) => $query->where('name', 'owner'))
                ->exists();
        });

        $overLimit = $tenants->filter(function (Tenant $tenant): bool {
            $maxUsers = $tenant->maxLimit('max_users');
            $maxProperties = $tenant->maxLimit('max_properties');

            return ($maxUsers > 0 && $tenant->users_count > $maxUsers)
                || ($maxProperties > 0 && $tenant->properties_count > $maxProperties);
        });

        $expiringSoon = $tenants->filter(fn (Tenant $tenant): bool => $tenant->end_date
            && $tenant->subscription_status === 'active'
            && $tenant->end_date->betweenIncluded($today, $today->copy()->addDays(14)));

        $checks = [
            [
                'label' => 'Database migrations',
                'status' => Schema::hasTable('migrations') ? 'healthy' : 'critical',
                'detail' => Schema::hasTable('migrations') ? 'Migration table is available.' : 'Migration table is missing.',
            ],
            [
                'label' => 'Subscription plans',
                'status' => Schema::hasTable('subscription_plans') ? 'healthy' : 'critical',
                'detail' => Schema::hasTable('subscription_plans') ? 'Plan table is available.' : 'Plan table is missing.',
            ],
            [
                'label' => 'Tenant ownership',
                'status' => $withoutOwner->isEmpty() ? 'healthy' : 'warning',
                'detail' => $withoutOwner->isEmpty()
                    ? 'Every tenant has an owner user.'
                    : $withoutOwner->count().' tenant(s) need an owner user.',
            ],
            [
                'label' => 'Usage limits',
                'status' => $overLimit->isEmpty() ? 'healthy' : 'warning',
                'detail' => $overLimit->isEmpty()
                    ? 'No tenant is over its configured plan limits.'
                    : $overLimit->count().' tenant(s) are above plan limits.',
            ],
        ];

        $stats = [
            'openItems' => $withoutOwner->count() + $overLimit->count() + $expiringSoon->count(),
            'withoutOwner' => $withoutOwner->count(),
            'overLimit' => $overLimit->count(),
            'expiringSoon' => $expiringSoon->count(),
        ];

        $attentionTenants = $tenants
            ->filter(fn (Tenant $tenant): bool => $withoutOwner->contains('id', $tenant->id)
                || $overLimit->contains('id', $tenant->id)
                || $expiringSoon->contains('id', $tenant->id)
                || $tenant->subscription_status !== 'active')
            ->take(15);

        $ticketQuery = SupportTicket::query()
            ->with(['tenant', 'creator', 'latestMessage.user'])
            ->withCount('messages')
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->string('status')))
            ->when($request->filled('support_area'), fn ($query) => $query->where('support_area', (string) $request->string('support_area')))
            ->when($request->filled('tenant_id'), fn ($query) => $query->where('company_id', $request->integer('tenant_id')));

        $tickets = (clone $ticketQuery)
            ->latest('last_message_at')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $ticketStats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'pending' => SupportTicket::where('status', 'pending')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
            'reservation' => SupportTicket::where('support_area', 'reservation')->count(),
            'hr' => SupportTicket::where('support_area', 'hr')->count(),
        ];

        return view('super_admin.support.index', compact(
            'stats',
            'checks',
            'attentionTenants',
            'tickets',
            'ticketStats',
            'tenants'
        ));
    }

    public function show(SupportTicket $ticket): View
    {
        $this->markReadForSuperAdmin($ticket);
        $ticket->load(['tenant', 'creator', 'messages.user', 'messages.attachments']);

        return view('super_admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:20000'],
            'status' => ['nullable', 'in:open,pending,closed'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,txt,csv'],
        ]);

        $body = SupportTicketRichText::sanitize($data['body'] ?? '');
        $files = $request->file('attachments', []);

        if (SupportTicketRichText::plainText($body) === '' && empty($files)) {
            return back()->withInput()->withErrors(['body' => 'Write a reply or attach a file before sending.']);
        }

        DB::transaction(function () use ($ticket, $body, $files, $request, $data): void {
            $message = $ticket->messages()->create([
                'user_id' => $request->user()?->id,
                'sender_role' => 'super_admin',
                'body' => $body,
            ]);

            $this->storeAttachments($message, $files);

            $ticket->update([
                'status' => $data['status'] ?? 'pending',
                'last_message_at' => now(),
                'last_sender_role' => 'super_admin',
                'tenant_last_read_at' => null,
                'super_admin_last_read_at' => now(),
            ]);
        });

        return redirect()
            ->route('super-admin.support.show', $ticket)
            ->with('success', 'Support reply sent successfully.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,pending,closed'],
        ]);

        $ticket->update($data);

        return back()->with('success', 'Ticket status updated.');
    }

    public function download(SupportTicketAttachment $attachment)
    {
        abort_unless(Storage::disk('public')->exists($attachment->path), 404);

        return Storage::disk('public')->download($attachment->path, $attachment->original_name);
    }

    private function storeAttachments(SupportTicketMessage $message, array $files): void
    {
        foreach ($files as $file) {
            $path = $file->store('support-attachments', 'public');

            $message->attachments()->create([
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize() ?: 0,
            ]);
        }
    }

    private function markReadForSuperAdmin(SupportTicket $ticket): void
    {
        if ($ticket->last_sender_role !== 'tenant') {
            return;
        }

        $ticket->forceFill([
            'super_admin_last_read_at' => now(),
        ])->saveQuietly();
    }
}

<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Models\SupportTicketMessage;
use App\Support\SupportTicketRichText;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()?->isSuperAdmin()) {
            return redirect()->route('super-admin.support.index');
        }

        $tickets = $this->ticketList($request, 'reservation');

        return view('support.tickets.index', compact('tickets'));
    }

    public function hrIndex(Request $request): View|RedirectResponse
    {
        if ($request->user()?->isSuperAdmin()) {
            return redirect()->route('super-admin.support.index');
        }

        $tickets = $this->ticketList($request, 'hr');

        return view('support.hr.index', compact('tickets'));
    }

    public function create(): View
    {
        return view('support.tickets.create');
    }

    public function hrCreate(): View
    {
        return view('support.hr.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $ticket = $this->createTicket($request, 'reservation');

        return redirect()
            ->route('support.tickets.show', $ticket)
            ->with('success', 'Support ticket created successfully.');
    }

    public function hrStore(Request $request): RedirectResponse
    {
        $ticket = $this->createTicket($request, 'hr');

        return redirect()
            ->route('dashboard.support.tickets.show', $ticket)
            ->with('success', 'Support ticket created successfully.');
    }

    private function createTicket(Request $request, string $supportArea): SupportTicket
    {
        $data = $this->validateTicketRequest($request, true);
        $body = SupportTicketRichText::sanitize($data['body'] ?? '');
        $files = $request->file('attachments', []);

        if (SupportTicketRichText::plainText($body) === '' && empty($files)) {
            throw ValidationException::withMessages([
                'body' => 'Write a message or attach a file before submitting the ticket.',
            ]);
        }

        return DB::transaction(function () use ($data, $body, $files, $request, $supportArea): SupportTicket {
            $ticket = SupportTicket::create([
                'company_id' => app(TenantContext::class)->id(),
                'created_by' => $request->user()?->id,
                'subject' => $data['subject'],
                'category' => $data['category'] ?? null,
                'support_area' => $supportArea,
                'priority' => $data['priority'],
                'status' => 'open',
                'last_message_at' => now(),
                'last_sender_role' => 'tenant',
                'tenant_last_read_at' => now(),
                'super_admin_last_read_at' => null,
            ]);

            $message = $ticket->messages()->create([
                'user_id' => $request->user()?->id,
                'sender_role' => 'tenant',
                'body' => $body,
            ]);

            $this->storeAttachments($message, $files);

            return $ticket;
        });
    }

    public function show(SupportTicket $ticket): View
    {
        $this->ensureTicketArea($ticket, 'reservation');
        $this->markReadForTenant($ticket);
        $ticket->load(['tenant', 'creator', 'messages.user', 'messages.attachments']);

        return view('support.tickets.show', compact('ticket'));
    }

    public function hrShow(SupportTicket $ticket): View
    {
        $this->ensureTicketArea($ticket, 'hr');
        $this->markReadForTenant($ticket);
        $ticket->load(['tenant', 'creator', 'messages.user', 'messages.attachments']);

        return view('support.hr.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->ensureTicketArea($ticket, 'reservation');
        $this->addTenantReply($request, $ticket);

        return redirect()
            ->route('support.tickets.show', $ticket)
            ->with('success', 'Reply sent successfully.');
    }

    public function hrReply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->ensureTicketArea($ticket, 'hr');
        $this->addTenantReply($request, $ticket);

        return redirect()
            ->route('dashboard.support.tickets.show', $ticket)
            ->with('success', 'Reply sent successfully.');
    }

    private function addTenantReply(Request $request, SupportTicket $ticket): void
    {
        $data = $this->validateReplyRequest($request);
        $body = SupportTicketRichText::sanitize($data['body'] ?? '');
        $files = $request->file('attachments', []);

        if (SupportTicketRichText::plainText($body) === '' && empty($files)) {
            throw ValidationException::withMessages([
                'body' => 'Write a reply or attach a file before sending.',
            ]);
        }

        DB::transaction(function () use ($ticket, $body, $files, $request): void {
            $message = $ticket->messages()->create([
                'user_id' => $request->user()?->id,
                'sender_role' => 'tenant',
                'body' => $body,
            ]);

            $this->storeAttachments($message, $files);

            $ticket->update([
                'status' => 'open',
                'last_message_at' => now(),
                'last_sender_role' => 'tenant',
                'tenant_last_read_at' => now(),
                'super_admin_last_read_at' => null,
            ]);
        });
    }

    public function download(Request $request, SupportTicketAttachment $attachment)
    {
        $attachment->load('message.ticket');
        $tenantId = app(TenantContext::class)->id();
        $supportArea = $request->routeIs('dashboard.support.tickets.*') ? 'hr' : 'reservation';

        abort_unless((int) $attachment->message->ticket->company_id === (int) $tenantId, 404);
        $this->ensureTicketArea($attachment->message->ticket, $supportArea);
        abort_unless(Storage::disk('public')->exists($attachment->path), 404);

        return Storage::disk('public')->download($attachment->path, $attachment->original_name);
    }

    private function validateTicketRequest(Request $request, bool $creating = false): array
    {
        return $request->validate([
            'subject' => [$creating ? 'required' : 'sometimes', 'string', 'max:180'],
            'category' => ['nullable', 'string', 'max:80'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'body' => ['nullable', 'string', 'max:20000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,txt,csv'],
        ]);
    }

    private function validateReplyRequest(Request $request): array
    {
        return $request->validate([
            'body' => ['nullable', 'string', 'max:20000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,txt,csv'],
        ]);
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

    private function ticketList(Request $request, string $supportArea)
    {
        return SupportTicket::query()
            ->with(['latestMessage.user'])
            ->withCount('messages')
            ->where('support_area', $supportArea)
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->string('status')))
            ->latest('last_message_at')
            ->latest()
            ->paginate(12)
            ->withQueryString();
    }

    private function ensureTicketArea(SupportTicket $ticket, string $supportArea): void
    {
        abort_unless($ticket->support_area === $supportArea, 404);
    }

    private function markReadForTenant(SupportTicket $ticket): void
    {
        if ($ticket->last_sender_role !== 'super_admin') {
            return;
        }

        $ticket->forceFill([
            'tenant_last_read_at' => now(),
        ])->saveQuietly();
    }
}

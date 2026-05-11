<?php

namespace App\Http\Controllers\SMS;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Reservation;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ManualSMSController extends Controller
{
    private const SEGMENTS = ['all', 'vip', 'in_house', 'today_arrivals'];

    public function index(Request $request, SmsService $smsService)
    {
        $segment = $this->normalizeSegment($request->query('segment'));
        $search = trim((string) $request->query('search', ''));

        $guestQuery = $this->applySegmentFilter($this->guestBaseQuery(), $segment);

        if ($search !== '') {
            $guestQuery->where(function (Builder $query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $matchingCount = (clone $guestQuery)->count();

        $guests = $guestQuery
            ->with('guestClass')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->limit(50)
            ->get();

        $selectedGuestIds = collect(old('guest_ids', []))
            ->map(fn ($id) => (int) $id)
            ->all();

        return view('admin.manual_sms.index', [
            'segment' => $segment,
            'search' => $search,
            'guests' => $guests,
            'selectedGuestIds' => $selectedGuestIds,
            'stats' => [
                'all' => $this->guestBaseQuery()->count(),
                'vip' => $this->applySegmentFilter($this->guestBaseQuery(), 'vip')->count(),
                'in_house' => $this->applySegmentFilter($this->guestBaseQuery(), 'in_house')->count(),
                'today_arrivals' => $this->applySegmentFilter($this->guestBaseQuery(), 'today_arrivals')->count(),
                'matching' => $matchingCount,
            ],
            'deliveryMode' => $smsService->deliveryMode(),
            'gatewayConfigured' => $smsService->isGatewayConfigured(),
            'lastResult' => session('manual_sms_result'),
        ]);
    }

    public function send(Request $request, SmsService $smsService): RedirectResponse
    {
        $validated = $request->validate([
            'sms_type' => ['required', Rule::in(['general', 'alert'])],
            'segment' => ['nullable', Rule::in(self::SEGMENTS)],
            'search' => ['nullable', 'string', 'max:100'],
            'guest_ids' => ['nullable', 'array'],
            'guest_ids.*' => ['integer', 'exists:guests,id'],
            'phone_numbers' => ['nullable', 'string', 'max:2000'],
            'message' => ['nullable', 'string', 'max:2000'],
            'alert_title' => ['nullable', 'string', 'max:120'],
            'alert_message' => ['nullable', 'string', 'max:2000'],
        ]);

        $segment = $this->normalizeSegment($validated['segment'] ?? null);
        $search = trim((string) ($validated['search'] ?? ''));
        $message = $this->buildMessage($validated);

        $messageField = $validated['sms_type'] === 'alert' ? 'alert_message' : 'message';

        if ($message === '') {
            throw ValidationException::withMessages([
                $messageField => app()->getLocale() === 'ar'
                    ? 'يرجى كتابة الرسالة قبل الإرسال.'
                    : 'Please enter a message before sending.',
            ]);
        }

        $parsedPhones = $smsService->parsePhoneList($validated['phone_numbers'] ?? null);

        if ($parsedPhones['invalid'] !== []) {
            $preview = implode(', ', array_slice($parsedPhones['invalid'], 0, 5));
            $suffix = count($parsedPhones['invalid']) > 5 ? '...' : '';

            throw ValidationException::withMessages([
                'phone_numbers' => app()->getLocale() === 'ar'
                    ? "توجد أرقام غير صحيحة: {$preview}{$suffix}"
                    : "These phone numbers are invalid: {$preview}{$suffix}",
            ]);
        }

        $guestIds = collect($validated['guest_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $guestRecipients = Guest::query()
            ->whereIn('id', $guestIds)
            ->get()
            ->map(function (Guest $guest) use ($smsService) {
                $phone = $smsService->normalizePhone($guest->mobile);

                if (! $phone) {
                    return null;
                }

                return [
                    'phone' => $phone,
                    'source' => 'guest',
                    'guest_id' => $guest->id,
                    'name' => $guest->full_name,
                ];
            })
            ->filter()
            ->values();

        $manualRecipients = collect($parsedPhones['valid'])
            ->map(fn (string $phone) => [
                'phone' => $phone,
                'source' => 'manual',
                'guest_id' => null,
                'name' => $phone,
            ]);

        $recipients = $guestRecipients
            ->concat($manualRecipients)
            ->unique('phone')
            ->values();

        if ($recipients->isEmpty()) {
            throw ValidationException::withMessages([
                'guest_ids' => app()->getLocale() === 'ar'
                    ? 'اختر ضيفا واحدا على الأقل أو أدخل رقم جوال واحدا على الأقل.'
                    : 'Select at least one guest or enter at least one phone number.',
            ]);
        }

        $result = $smsService->sendManual(
            $recipients->all(),
            $message,
            [
                'sms_type' => $validated['sms_type'],
                'alert_title' => $validated['alert_title'] ?? null,
                'requested_by' => auth()->id(),
                'segment' => $segment,
            ]
        );

        $flashPayload = [
            'sms_type' => $validated['sms_type'],
            'delivery_mode' => $result['mode'],
            'requested' => $result['requested'],
            'sent' => $result['sent'],
            'failed' => $result['failed'],
            'message_preview' => Str::limit($message, 120),
            'recipients' => collect($result['results'])
                ->map(fn (array $item) => [
                    'phone' => $item['phone'],
                    'status' => $item['status'],
                ])
                ->take(10)
                ->values()
                ->all(),
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ];

        return redirect()
            ->route('dashboard.manual_sms.index', array_filter([
                'segment' => $segment !== 'all' ? $segment : null,
                'search' => $search !== '' ? $search : null,
            ]))
            ->with($result['sent'] > 0 ? 'success' : 'danger', $this->buildStatusMessage($result))
            ->with('manual_sms_result', $flashPayload);
    }

    private function guestBaseQuery(): Builder
    {
        return Guest::query()
            ->where('is_active', true)
            ->whereNotNull('mobile_number')
            ->where('mobile_number', '!=', '');
    }

    private function applySegmentFilter(Builder $query, string $segment): Builder
    {
        return match ($segment) {
            'vip' => $query->whereNotNull('guest_class_id'),
            'in_house' => $query->whereIn('id', $this->inHouseGuestIdsQuery()),
            'today_arrivals' => $query->whereIn('id', $this->todayArrivalGuestIdsQuery()),
            default => $query,
        };
    }

    private function inHouseGuestIdsQuery(): Builder
    {
        return Reservation::query()
            ->select('guest_id')
            ->whereNotNull('guest_id')
            ->where('status', 'checked_in');
    }

    private function todayArrivalGuestIdsQuery(): Builder
    {
        return Reservation::query()
            ->select('guest_id')
            ->whereNotNull('guest_id')
            ->whereDate('check_in_date', Carbon::today())
            ->whereNotIn('status', ['cancelled', 'checked_out', 'no_show']);
    }

    private function normalizeSegment(?string $segment): string
    {
        return in_array($segment, self::SEGMENTS, true) ? $segment : 'all';
    }

    private function buildMessage(array $validated): string
    {
        if (($validated['sms_type'] ?? 'general') === 'alert') {
            $parts = Collection::make([
                trim((string) ($validated['alert_title'] ?? '')),
                trim((string) ($validated['alert_message'] ?? '')),
            ])->filter();

            return trim($parts->implode(' - '));
        }

        return trim((string) ($validated['message'] ?? ''));
    }

    private function buildStatusMessage(array $result): string
    {
        if ($result['mode'] === 'simulation') {
            return app()->getLocale() === 'ar'
                ? "تم تنفيذ محاكاة الإرسال لـ {$result['sent']} مستلمين. لم يتم استدعاء مزود رسائل خارجي لأن الإعدادات الحية غير مفعلة."
                : "Simulation completed for {$result['sent']} recipients. No external SMS gateway was called because live settings are not configured.";
        }

        if ($result['failed'] > 0 && $result['sent'] > 0) {
            return app()->getLocale() === 'ar'
                ? "تم إرسال {$result['sent']} رسالة، وتعذر إرسال {$result['failed']} رسالة."
                : "{$result['sent']} messages were sent, and {$result['failed']} failed.";
        }

        if ($result['failed'] > 0) {
            return app()->getLocale() === 'ar'
                ? 'تعذر إرسال الرسائل. راجع إعدادات مزود الرسائل أو السجل لمعرفة السبب.'
                : 'The messages could not be sent. Please review the SMS gateway settings or application log.';
        }

        return app()->getLocale() === 'ar'
            ? "تم إرسال الرسائل بنجاح إلى {$result['sent']} مستلمين."
            : "Messages were sent successfully to {$result['sent']} recipients.";
    }
}

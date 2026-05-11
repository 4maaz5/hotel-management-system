<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Mail\SubscriptionRequestMail;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function index()
    {
        return view('admin.setup_subscription.index', [
            'property' => Property::current(),
            'user' => auth()->user(),
        ]);
    }

    public function sendRequest(Request $request): RedirectResponse
    {
        $integrations = $this->integrationCatalog();

        $validated = $request->validate([
            'integration_key' => ['required', Rule::in(array_keys($integrations))],
            'property_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'requested_plan' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $integration = $integrations[$validated['integration_key']];

        $mailData = [
            'integration_key' => $validated['integration_key'],
            'integration_name' => $integration['name'],
            'integration_price' => $integration['price'],
            'integration_billing' => $integration['billing'],
            'property_name' => $validated['property_name'],
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'] ?? null,
            'requested_plan' => $validated['requested_plan'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'submitted_at' => now(),
        ];

        try {
            Mail::to('info@b-it.co')->send(new SubscriptionRequestMail($mailData));

            return redirect()
                ->route('setup-sidebar.setup_subscription.index')
                ->with('subscription_request_success', 'Your subscription request has been sent successfully.');
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('subscription_modal_key', $validated['integration_key'])
                ->with('subscription_request_error', 'The request could not be sent right now. Please try again.');
        }
    }

    protected function integrationCatalog(): array
    {
        return [
            'zatca' => [
                'name' => 'ZATCA Phase 2',
                'price' => 'From SAR 1,499',
                'billing' => 'Activation + annual support',
            ],
            'booking' => [
                'name' => 'Booking.com Synchronization',
                'price' => 'From SAR 999',
                'billing' => 'Monthly subscription',
            ],
            'shomoos' => [
                'name' => 'Saudi Shomoos Integration',
                'price' => 'From SAR 1,299',
                'billing' => 'Activation + annual follow-up',
            ],
            'sms' => [
                'name' => 'SMS Subscription',
                'price' => 'From SAR 250',
                'billing' => 'Monthly bundles',
            ],
        ];
    }
}

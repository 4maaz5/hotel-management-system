<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Support\UserActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
   public function index(Request $request)
    {
        $query = Invoice::with(['reservation.guest', 'reservation.unit']);

        if ($request->invoice_number) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->guest_name) {
            $query->whereHas('reservation.guest', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->guest_name . '%')
                  ->orWhere('last_name', 'like', '%' . $request->guest_name . '%');
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        $printingOption = \App\Models\PrintingOption::where('report_key', 'invoice')->first();

        return view('admin.voucher_invoice.index', compact('invoices', 'printingOption'));
    }

    public function show($id)
    {
        $invoice = Invoice::with(['reservation.guest', 'reservation.unit', 'reservation.corporate', 'items'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'invoice' => $invoice,
        ]);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $before = $this->invoiceActivityData($invoice);

        $invoice->update([
            'status' => $request->status,
        ]);

        app(UserActivityLogger::class)->log(
            'invoices',
            'updated',
            $invoice,
            "Updated invoice {$invoice->invoice_number}",
            $before,
            $this->invoiceActivityData($invoice->fresh())
        );

        return response()->json([
            'success' => true,
            'message' => __('dashboard.invoice_updated')
        ]);
    }

    public function print(Request $request, $id)
    {
        $invoice = Invoice::with(['reservation.guest', 'reservation.unit', 'items'])->findOrFail($id);

        $printingOption = \App\Models\PrintingOption::where('report_key', 'invoice')->first();
        $globalSetting = \App\Models\PrintingOption::first();
        $property = \App\Models\Property::current(['commercialDetail']);

        return view('admin.voucher_invoice.print', compact('invoice', 'printingOption', 'globalSetting', 'property'));
    }

    public function sendEmail(Request $request, $id)
    {
        $invoice = Invoice::with(['reservation.guest', 'reservation.unit', 'reservation.corporate', 'items'])->findOrFail($id);

        $email = $request->input('email');

        if (!$email) {
            $email = $invoice->reservation->guest->email ?? null;
        }

        if (!$email) {
            $email = $invoice->reservation->corporate->email ?? null;
        }

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => __('dashboard.invoice_no_email')
            ]);
        }

        try {
            Mail::to($email)->send(new InvoiceMail($invoice));

            return response()->json([
                'success' => true,
                'message' => __('dashboard.invoice_sent') . ' ' . $email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ]);
        }
    }

    protected function invoiceActivityData(Invoice $invoice): array
    {
        return [
            'invoice_number' => $invoice->invoice_number,
            'status' => $invoice->status,
            'total' => (float) $invoice->total,
            'paid_amount' => (float) $invoice->paid_amount,
            'balance' => (float) $invoice->balance,
            'reservation_id' => $invoice->reservation_id,
        ];
    }
}

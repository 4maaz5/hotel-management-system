<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditNote::with(['reservation', 'guest', 'outlet']);

        if ($request->credit_note_number) {
            $query->where('credit_note_number', 'like', '%'.$request->credit_note_number.'%');
        }

        if ($request->invoice_type) {
            $query->where('invoice_type', $request->invoice_type);
        }

        if ($request->guest_name) {
            $query->whereHas('guest', function ($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->guest_name.'%')
                    ->orWhere('last_name', 'like', '%'.$request->guest_name.'%');
            });
        }

        if ($request->date_from) {
            $query->where('cn_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('cn_date', '<=', $request->date_to);
        }

        $creditNotes = $query->orderByDesc('id')->paginate(20);
        $printingOption = \App\Models\PrintingOption::where('report_key', 'credit_note')->first();
        $property = \App\Models\Property::current();

        return view('admin.voucher_credit.index', compact('creditNotes', 'printingOption', 'property'));
    }

    public function show($id)
    {
        $creditNote = CreditNote::with(['reservation', 'guest', 'outlet', 'invoice'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'creditNote' => [
                'id' => $creditNote->id,
                'credit_note_number' => $creditNote->credit_note_number,
                'invoice_type' => $creditNote->invoice_type,
                'cn_date' => $creditNote->cn_date ? $creditNote->cn_date->format('Y-m-d') : null,
                'period_from' => $creditNote->period_from ? $creditNote->period_from->format('Y-m-d') : null,
                'period_to' => $creditNote->period_to ? $creditNote->period_to->format('Y-m-d') : null,
                'amount' => $creditNote->amount,
                'invoice_number' => $creditNote->invoice_number,
                'guest' => $creditNote->guest ? [
                    'id' => $creditNote->guest->id,
                    'name' => $creditNote->guest->first_name.' '.$creditNote->guest->last_name,
                ] : null,
                'reservation' => $creditNote->reservation ? [
                    'id' => $creditNote->reservation->id,
                    'reservation_number' => $creditNote->reservation->reservation_number,
                ] : null,
                'outlet' => $creditNote->outlet ? [
                    'id' => $creditNote->outlet->id,
                    'name' => $creditNote->outlet->name,
                ] : null,
            ],
        ]);
    }

    public function print(Request $request, $id)
    {
        $creditNote = CreditNote::with(['reservation', 'guest', 'outlet', 'invoice'])->findOrFail($id);

        $printingOption = \App\Models\PrintingOption::where('report_key', 'credit_note')->first();
        $globalSetting = \App\Models\PrintingOption::first();
        $property = \App\Models\Property::current(['commercialDetail']);

        return view('admin.voucher_credit.print', compact('creditNote', 'printingOption', 'globalSetting', 'property'));
    }

    public function sendWhatsApp(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'WhatsApp feature coming soon',
        ]);
    }

    public function sendSms(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'SMS feature coming soon',
        ]);
    }
}

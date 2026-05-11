<?php

namespace App\Http\Controllers;

use App\Models\DropCashVoucher;
use App\Models\PaymentVoucher;
use App\Models\PrintingOption;
use App\Models\ReceiptVoucher;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CashDrawerController extends Controller
{
    private function getData(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $receiptVouchers = ReceiptVoucher::whereNotIn('status', ['cancelled'])
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $paymentVouchers = PaymentVoucher::whereNotIn('status', ['cancelled'])
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $dropCash = DropCashVoucher::whereBetween('date_from', [$startDate, $endDate])
            ->get();

        $cashReceived = $receiptVouchers->sum('amount');
        $cashReceivedCount = $receiptVouchers->count();

        $cashPaidOut = $paymentVouchers->where('voucher_type', '!=', 'refund')->sum('amount');
        $cashPaidOutCount = $paymentVouchers->where('voucher_type', '!=', 'refund')->count();

        $dropCashTotal = $dropCash->where('drop_method', 'cash')->sum('amount');
        $dropCashCount = $dropCash->where('drop_method', 'cash')->count();

        $securityDepositsReceived = $receiptVouchers->filter(function ($receipt) {
            if ($receipt->reservation_id) {
                $reservation = Reservation::find($receipt->reservation_id);

                return $reservation && $reservation->security_deposit > 0;
            }

            return false;
        })->sum('amount');

        $securityDepositCount = $receiptVouchers->filter(function ($receipt) {
            if ($receipt->reservation_id) {
                $reservation = Reservation::find($receipt->reservation_id);

                return $reservation && $reservation->security_deposit > 0;
            }

            return false;
        })->count();

        $securityDepositsPaidOut = $paymentVouchers->where('voucher_type', 'refund')->sum('amount');
        $securityDepositPaidCount = $paymentVouchers->where('voucher_type', 'refund')->count();

        $currentBalance = $cashReceived - $cashPaidOut - $dropCashTotal;

        $recentVouchers = $paymentVouchers->merge($receiptVouchers)
            ->sortByDesc('created_at')
            ->take(20);

        $printingOption = PrintingOption::where('report_key', 'cash_drawer_balance')->first();
        $globalSetting = PrintingOption::first();
        $property = \App\Models\Property::current(['commercialDetail']);

        return compact(
            'cashReceived', 'cashReceivedCount',
            'cashPaidOut', 'cashPaidOutCount',
            'dropCashTotal', 'dropCashCount',
            'securityDepositsReceived', 'securityDepositCount',
            'securityDepositsPaidOut', 'securityDepositPaidCount',
            'currentBalance',
            'startDate', 'endDate',
            'recentVouchers',
            'printingOption', 'globalSetting', 'property'
        );
    }

    public function index(Request $request)
    {
        $data = $this->getData($request);

        return view('admin.cash_drawer.index', $data);
    }

    public function print(Request $request)
    {
        return $this->index($request);
    }

    public function export(Request $request)
    {
        $data = $this->getData($request);

        $csvContent = "Cash Drawer Balance Report\n";
        $csvContent .= "Date Range: {$data['startDate']} to {$data['endDate']}\n\n";
        $csvContent .= "Transaction,Count,Amount\n";
        $csvContent .= "Cash Received,{$data['cashReceivedCount']},SAR ".number_format($data['cashReceived'], 2)."\n";
        $csvContent .= "Security Deposit Received,{$data['securityDepositCount']},SAR ".number_format($data['securityDepositsReceived'], 2)."\n";
        $csvContent .= "Cash Paid Out,{$data['cashPaidOutCount']},SAR ".number_format($data['cashPaidOut'], 2)."\n";
        $csvContent .= "Security Deposit Paid Out,{$data['securityDepositPaidCount']},SAR ".number_format($data['securityDepositsPaidOut'], 2)."\n";
        $csvContent .= "Drop Cash Vouchers,{$data['dropCashCount']},SAR ".number_format($data['dropCashTotal'], 2)."\n";
        $csvContent .= 'Current Balance, -,SAR '.number_format($data['currentBalance'], 2)."\n";

        $filename = 'cash_drawer_report_'.date('Y-m-d').'.csv';
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_');
        file_put_contents($tempFile, $csvContent);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }
}

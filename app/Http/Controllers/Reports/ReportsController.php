<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\DropCashVoucher;
use App\Models\Invoice;
use App\Models\NightAudit;
use App\Models\PaymentVoucher;
use App\Models\PrintingOption;
use App\Models\PromissoryNote;
use App\Models\Property;
use App\Models\ReceiptVoucher;
use App\Models\Reservation;
use App\Models\Unit;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    private function getFilters(Request $request)
    {
        return [
            'date_from' => $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d')),
            'date_to' => $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d')),
        ];
    }

    private function getPrintVariables($reportKey = 'report')
    {
        $printingOption = PrintingOption::where('report_key', $reportKey)->first();
        $globalSetting = PrintingOption::first();
        $property = Property::current(['commercialDetail']);

        return compact('printingOption', 'globalSetting', 'property');
    }

    public function financialTransactions(Request $request)
    {
        $filters = $this->getFilters($request);

        $receipts = ReceiptVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $payments = PaymentVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $totalReceipts = $receipts->sum('amount');
        $totalPayments = $payments->where('voucher_type', '!=', 'refund')->sum('amount');
        $totalRefunds = $payments->where('voucher_type', 'refund')->sum('amount');
        $netAmount = $totalReceipts - $totalPayments;

        $printVars = $this->getPrintVariables('financial_transactions');

        return view('admin.reports.financial_transactions', array_merge(
            compact('filters', 'receipts', 'payments', 'totalReceipts', 'totalPayments', 'totalRefunds', 'netAmount'),
            $printVars
        ));
    }

    public function dailyTransactions(Request $request)
    {
        $filters = $this->getFilters($request);

        $receipts = ReceiptVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $payments = PaymentVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $dailyData = [];
        $start = Carbon::parse($filters['date_from']);
        $end = Carbon::parse($filters['date_to']);

        while ($start <= $end) {
            $date = $start->format('Y-m-d');
            $dailyData[$date] = [
                'date' => $date,
                'receipts_count' => $receipts->where('date', $date)->count(),
                'receipts_amount' => $receipts->where('date', $date)->sum('amount'),
                'payments_count' => $payments->where('date', $date)->count(),
                'payments_amount' => $payments->where('date', $date)->sum('amount'),
            ];
            $start->addDay();
        }

        $printVars = $this->getPrintVariables('daily_transactions');

        return view('admin.reports.daily_transactions', array_merge(
            compact('filters', 'dailyData'),
            $printVars
        ));
    }

    public function trialBalance(Request $request)
    {
        $filters = $this->getFilters($request);

        $receiptsTotal = ReceiptVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])
            ->sum('amount');

        $paymentsTotal = PaymentVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])
            ->where('voucher_type', '!=', 'refund')
            ->sum('amount');

        $dropCashTotal = DropCashVoucher::whereBetween('date_from', [$filters['date_from'], $filters['date_to']])
            ->sum('amount');

        $invoicesTotal = Invoice::whereBetween('issue_date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');

        $printVars = $this->getPrintVariables('trial_balance');

        return view('admin.reports.trial_balance', array_merge(
            compact('filters', 'receiptsTotal', 'paymentsTotal', 'dropCashTotal', 'invoicesTotal'),
            $printVars
        ));
    }

    public function tax(Request $request)
    {
        $filters = $this->getFilters($request);

        $invoices = Invoice::whereBetween('issue_date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $totalTaxableAmount = $invoices->sum('subtotal');
        $totalTaxAmount = $invoices->sum('tax_amount');
        $totalAmount = $invoices->sum('total');

        $printVars = $this->getPrintVariables('tax_report');

        return view('admin.reports.tax', array_merge(
            compact('filters', 'invoices', 'totalTaxableAmount', 'totalTaxAmount', 'totalAmount'),
            $printVars
        ));
    }

    public function reservationBalances(Request $request)
    {
        $filters = $this->getFilters($request);

        $reservations = Reservation::whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
            ->with(['guest', 'unit'])
            ->get();

        $printVars = $this->getPrintVariables('reservation_balances');

        return view('admin.reports.reservation_balances', array_merge(
            compact('filters', 'reservations'),
            $printVars
        ));
    }

    public function receiptVouchers(Request $request)
    {
        $filters = $this->getFilters($request);

        $vouchers = ReceiptVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->with(['paymentMethod', 'reservation'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        $printVars = $this->getPrintVariables('receipt_vouchers_report');

        return view('admin.reports.receipt_vouchers', array_merge(
            compact('filters', 'vouchers'),
            $printVars
        ));
    }

    public function paymentVouchers(Request $request)
    {
        $filters = $this->getFilters($request);

        $vouchers = PaymentVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->with(['paymentMethod'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        $printVars = $this->getPrintVariables('payment_vouchers_report');

        return view('admin.reports.payment_vouchers', array_merge(
            compact('filters', 'vouchers'),
            $printVars
        ));
    }

    public function invoices(Request $request)
    {
        $filters = $this->getFilters($request);

        $invoices = Invoice::whereBetween('issue_date', [$filters['date_from'], $filters['date_to']])
            ->with(['reservation.guest'])
            ->orderBy('issue_date', 'desc')
            ->paginate(20);

        $printVars = $this->getPrintVariables('invoices_report');

        return view('admin.reports.invoices', array_merge(
            compact('filters', 'invoices'),
            $printVars
        ));
    }

    public function creditNotes(Request $request)
    {
        $filters = $this->getFilters($request);

        $creditNotes = CreditNote::whereBetween('cn_date', [$filters['date_from'], $filters['date_to']])
            ->with(['reservation', 'guest'])
            ->orderBy('cn_date', 'desc')
            ->paginate(20);

        $printVars = $this->getPrintVariables('credit_notes_report');

        return view('admin.reports.credit_notes', array_merge(
            compact('filters', 'creditNotes'),
            $printVars
        ));
    }

    public function promissoryNotes(Request $request)
    {
        $filters = $this->getFilters($request);

        $promissoryNotes = PromissoryNote::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->with(['guest', 'reservation'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        $printVars = $this->getPrintVariables('promissory_notes_report');

        return view('admin.reports.promissory_notes', array_merge(
            compact('filters', 'promissoryNotes'),
            $printVars
        ));
    }

    public function dropCash(Request $request)
    {
        $filters = $this->getFilters($request);

        $dropCash = DropCashVoucher::whereBetween('date_from', [$filters['date_from'], $filters['date_to']])
            ->with(['bank'])
            ->orderBy('date_from', 'desc')
            ->paginate(20);

        $printVars = $this->getPrintVariables('drop_cash_report');

        return view('admin.reports.drop_cash', array_merge(
            compact('filters', 'dropCash'),
            $printVars
        ));
    }

    public function guestLedger(Request $request)
    {
        $filters = $this->getFilters($request);

        $reservations = Reservation::with(['guest', 'invoice'])
            ->where(function ($query) use ($filters) {
                $query->whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
                    ->orWhereBetween('check_out_date', [$filters['date_from'], $filters['date_to']]);
            })
            ->get();

        $printVars = $this->getPrintVariables('guest_ledger');

        return view('admin.reports.guest_ledger', array_merge(
            compact('filters', 'reservations'),
            $printVars
        ));
    }

    public function cityLedger(Request $request)
    {
        $filters = $this->getFilters($request);

        $reservations = Reservation::with(['guest', 'invoice'])
            ->where(function ($q) use ($filters) {
                $q->whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
                    ->orWhereBetween('check_out_date', [$filters['date_from'], $filters['date_to']]);
            })
            ->get();

        $printVars = $this->getPrintVariables('city_ledger');

        return view('admin.reports.city_ledger', array_merge(
            compact('filters', 'reservations'),
            $printVars
        ));
    }

    public function revenueBySource(Request $request)
    {
        $filters = $this->getFilters($request);

        $reservations = Reservation::whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
            ->with(['source'])
            ->get();

        $bySource = $reservations->groupBy(function ($item) {
            return $item->source->name ?? 'Direct';
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('grand_total'),
            ];
        });

        $printVars = $this->getPrintVariables('revenue_by_source');

        return view('admin.reports.revenue_by_source', array_merge(
            compact('filters', 'bySource'),
            $printVars
        ));
    }

    public function reservationRevenue(Request $request)
    {
        $filters = $this->getFilters($request);

        $reservations = Reservation::whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
            ->with(['guest', 'unit', 'invoice'])
            ->get();

        $totalRevenue = $reservations->sum('grand_total');
        $totalPaid = $reservations->sum('paid_amount');
        $totalOutstanding = $totalRevenue - $totalPaid;

        $printVars = $this->getPrintVariables('reservation_revenue');

        return view('admin.reports.reservation_revenue', array_merge(
            compact('filters', 'reservations', 'totalRevenue', 'totalPaid', 'totalOutstanding'),
            $printVars
        ));
    }

    public function reservationSummary(Request $request)
    {
        $filters = $this->getFilters($request);

        $reservations = Reservation::whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
            ->with(['guest', 'unit'])
            ->get();

        $summary = [
            'total' => $reservations->count(),
            'checked_in' => $reservations->where('status', 'checked_in')->count(),
            'checked_out' => $reservations->where('status', 'checked_out')->count(),
            'cancelled' => $reservations->where('status', 'cancelled')->count(),
            'pending' => $reservations->where('status', 'confirmed')->count(),
        ];

        $printVars = $this->getPrintVariables('reservation_summary');

        return view('admin.reports.reservation_summary', array_merge(
            compact('filters', 'reservations', 'summary'),
            $printVars
        ));
    }

    public function reservationDetails(Request $request)
    {
        $filters = $this->getFilters($request);

        $reservations = Reservation::whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
            ->with(['guest', 'unit', 'invoice'])
            ->orderBy('check_in_date', 'desc')
            ->paginate(20);

        $printVars = $this->getPrintVariables('reservation_details');

        return view('admin.reports.reservation_details', array_merge(
            compact('filters', 'reservations'),
            $printVars
        ));
    }

    public function expectedArrivals(Request $request)
    {
        $filters = $this->getFilters($request);

        $reservations = Reservation::whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled', 'checked_out', 'no_show'])
            ->with(['guest', 'unit'])
            ->orderBy('check_in_date', 'asc')
            ->paginate(20);

        $printVars = $this->getPrintVariables('expected_arrivals');

        return view('admin.reports.expected_arrivals', array_merge(
            compact('filters', 'reservations'),
            $printVars
        ));
    }

    public function expectedDepartures(Request $request)
    {
        $filters = $this->getFilters($request);

        $reservations = Reservation::whereBetween('check_out_date', [$filters['date_from'], $filters['date_to']])
            ->whereIn('status', ['checked_in', 'checked_out'])
            ->with(['guest', 'unit', 'invoice'])
            ->orderBy('check_out_date', 'asc')
            ->paginate(20);

        $printVars = $this->getPrintVariables('expected_departures');

        return view('admin.reports.expected_departures', array_merge(
            compact('filters', 'reservations'),
            $printVars
        ));
    }

    public function nightAuditSummary(Request $request)
    {
        $filters = $this->getFilters($request);

        $audits = NightAudit::whereBetween('start_date_time', [$filters['date_from'], $filters['date_to']])
            ->with('user')
            ->get();

        $summary = [
            'total' => $audits->count(),
            'completed' => $audits->where('status', 'completed')->count(),
            'pending' => $audits->where('status', 'pending')->count(),
            'failed' => $audits->where('status', 'failed')->count(),
        ];

        $printVars = $this->getPrintVariables('night_audit_summary');

        return view('admin.reports.night_audit_summary', array_merge(
            compact('filters', 'audits', 'summary'),
            $printVars
        ));
    }

    public function nightAuditHistory(Request $request)
    {
        $filters = $this->getFilters($request);

        $audits = NightAudit::with('user')
            ->whereBetween('start_date_time', [$filters['date_from'], $filters['date_to']])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $printVars = $this->getPrintVariables('night_audit_history');

        return view('admin.reports.night_audit_history', array_merge(
            compact('filters', 'audits'),
            $printVars
        ));
    }

    public function housekeepingStatus(Request $request)
    {
        $filters = $this->getFilters($request);

        $units = \App\Models\Unit::with(['floor', 'unitType'])
            ->get();

        $printVars = $this->getPrintVariables('housekeeping_status');

        return view('admin.reports.housekeeping_status', array_merge(
            compact('filters', 'units'),
            $printVars
        ));
    }

    public function occupancy(Request $request)
    {
        $filters = $this->getFilters($request);
        $occupancyData = $this->buildOccupancyData($filters);

        $printVars = $this->getPrintVariables('occupancy');

        return view('admin.reports.occupancy', array_merge(
            compact('filters'),
            $occupancyData,
            $printVars
        ));
    }

    public function printReport(Request $request, $reportType)
    {
        $reportType = str_replace('-', '_', $reportType);
        $filters = $this->getFilters($request);
        $printVars = $this->getPrintVariables($reportType);

        $data = match ($reportType) {
            'financial_transactions' => $this->getFinancialTransactionsData($filters),
            'daily_transactions' => $this->getDailyTransactionsData($filters),
            'trial_balance' => $this->getTrialBalanceData($filters),
            'tax' => $this->getTaxData($filters),
            'reservation_balances' => $this->getReservationBalancesData($filters),
            'receipt_vouchers' => $this->getReceiptVouchersData($filters),
            'payment_vouchers' => $this->getPaymentVouchersData($filters),
            'invoices' => $this->getInvoicesData($filters),
            'credit_notes' => $this->getCreditNotesData($filters),
            'promissory_notes' => $this->getPromissoryNotesData($filters),
            'drop_cash' => $this->getDropCashData($filters),
            'guest_ledger' => $this->getGuestLedgerData($filters),
            'city_ledger' => $this->getCityLedgerData($filters),
            'revenue_by_source' => $this->getRevenueBySourceData($filters),
            'reservation_revenue' => $this->getReservationRevenueData($filters),
            'reservation_summary' => $this->getReservationSummaryData($filters),
            'reservation_details' => $this->getReservationDetailsData($filters),
            'expected_arrivals' => $this->getExpectedArrivalsData($filters),
            'expected_departures' => $this->getExpectedDeparturesData($filters),
            'night_audit_summary' => $this->getNightAuditSummaryData($filters),
            'night_audit_history' => $this->getNightAuditHistoryData($filters),
            'occupancy' => $this->getOccupancyData($filters),
            'housekeeping_status' => $this->getHousekeepingData($filters),
            default => [],
        };

        $reportTitle = $this->getReportTitle($reportType);

        return view('admin.reports.print', array_merge($data, $printVars, [
            'reportTitle' => $reportTitle,
            'filters' => $filters,
            'reportType' => $reportType,
        ]));
    }

    private function getReportTitle($reportType)
    {
        return match ($reportType) {
            'financial_transactions' => __('dashboard.financial_transactions_report'),
            'daily_transactions' => __('dashboard.daily_transactions'),
            'trial_balance' => __('dashboard.trial_balance_summary'),
            'tax' => __('dashboard.tax_report'),
            'reservation_balances' => __('dashboard.reservation_balances_report'),
            'receipt_vouchers' => __('dashboard.receipt_vouchers_report'),
            'payment_vouchers' => __('dashboard.payments_report'),
            'invoices' => __('dashboard.invoices_report'),
            'credit_notes' => __('dashboard.credit_notes_report'),
            'promissory_notes' => __('dashboard.promissory_notes_report'),
            'drop_cash' => __('dashboard.drop_cash_report'),
            'guest_ledger' => __('dashboard.guest_ledger_report'),
            'city_ledger' => __('dashboard.city_ledger_report'),
            'revenue_by_source' => __('dashboard.revenue_by_source_report'),
            'reservation_revenue' => __('dashboard.reservation_revenue_reports'),
            'reservation_summary' => __('dashboard.reservation_summary_report'),
            'reservation_details' => __('dashboard.reservation_details_report'),
            'expected_arrivals' => __('dashboard.expected_arrivals_report'),
            'expected_departures' => __('dashboard.expected_departures_report'),
            'night_audit_summary' => __('dashboard.night_audit_summary_report'),
            'night_audit_history' => __('dashboard.night_audit_history_report'),
            'occupancy' => __('dashboard.occupancy_report'),
            'housekeeping_status' => __('dashboard.housekeeping_status_report'),
            default => 'Report',
        };
    }

    private function getFinancialTransactionsData($filters)
    {
        $receipts = ReceiptVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])->get();
        $payments = PaymentVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])->get();

        return [
            'receipts' => $receipts,
            'payments' => $payments,
            'totalReceipts' => $receipts->sum('amount'),
            'totalPayments' => $payments->where('voucher_type', '!=', 'refund')->sum('amount'),
            'totalRefunds' => $payments->where('voucher_type', 'refund')->sum('amount'),
            'netAmount' => $receipts->sum('amount') - $payments->where('voucher_type', '!=', 'refund')->sum('amount'),
        ];
    }

    private function getDailyTransactionsData($filters)
    {
        $receipts = ReceiptVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])->get();
        $payments = PaymentVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])->get();

        $dailyData = [];
        $start = Carbon::parse($filters['date_from']);
        $end = Carbon::parse($filters['date_to']);

        while ($start <= $end) {
            $date = $start->format('Y-m-d');
            $dailyData[$date] = [
                'date' => $date,
                'receipts_count' => $receipts->where('date', $date)->count(),
                'receipts_amount' => $receipts->where('date', $date)->sum('amount'),
                'payments_count' => $payments->where('date', $date)->count(),
                'payments_amount' => $payments->where('date', $date)->sum('amount'),
            ];
            $start->addDay();
        }

        return ['dailyData' => $dailyData];
    }

    private function getTrialBalanceData($filters)
    {
        return [
            'receiptsTotal' => ReceiptVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
                ->whereNotIn('status', ['cancelled'])->sum('amount'),
            'paymentsTotal' => PaymentVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
                ->whereNotIn('status', ['cancelled'])->where('voucher_type', '!=', 'refund')->sum('amount'),
            'dropCashTotal' => DropCashVoucher::whereBetween('date_from', [$filters['date_from'], $filters['date_to']])
                ->sum('amount'),
            'invoicesTotal' => Invoice::whereBetween('issue_date', [$filters['date_from'], $filters['date_to']])
                ->whereNotIn('status', ['cancelled'])->sum('total'),
        ];
    }

    private function getTaxData($filters)
    {
        $invoices = Invoice::whereBetween('issue_date', [$filters['date_from'], $filters['date_to']])
            ->whereNotIn('status', ['cancelled'])->get();

        return [
            'invoices' => $invoices,
            'totalTaxableAmount' => $invoices->sum('subtotal'),
            'totalTaxAmount' => $invoices->sum('tax_amount'),
            'totalAmount' => $invoices->sum('total'),
        ];
    }

    private function getReservationBalancesData($filters)
    {
        return [
            'reservations' => Reservation::whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
                ->with(['guest', 'unit'])->get(),
        ];
    }

    private function getReceiptVouchersData($filters)
    {
        return [
            'vouchers' => ReceiptVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
                ->with(['paymentMethod', 'reservation'])->orderBy('date', 'desc')->get(),
        ];
    }

    private function getPaymentVouchersData($filters)
    {
        return [
            'vouchers' => PaymentVoucher::whereBetween('date', [$filters['date_from'], $filters['date_to']])
                ->with(['paymentMethod'])->orderBy('date', 'desc')->get(),
        ];
    }

    private function getInvoicesData($filters)
    {
        return [
            'invoices' => Invoice::whereBetween('issue_date', [$filters['date_from'], $filters['date_to']])
                ->with(['reservation.guest'])->orderBy('issue_date', 'desc')->get(),
        ];
    }

    private function getCreditNotesData($filters)
    {
        return [
            'creditNotes' => CreditNote::whereBetween('cn_date', [$filters['date_from'], $filters['date_to']])
                ->with(['reservation', 'guest'])->orderBy('cn_date', 'desc')->get(),
        ];
    }

    private function getPromissoryNotesData($filters)
    {
        return [
            'promissoryNotes' => PromissoryNote::whereBetween('date', [$filters['date_from'], $filters['date_to']])
                ->with(['guest', 'reservation'])->orderBy('date', 'desc')->get(),
        ];
    }

    private function getDropCashData($filters)
    {
        return [
            'dropCash' => DropCashVoucher::whereBetween('date_from', [$filters['date_from'], $filters['date_to']])
                ->with(['bank'])->orderBy('date_from', 'desc')->get(),
        ];
    }

    private function getGuestLedgerData($filters)
    {
        return [
            'reservations' => Reservation::with(['guest', 'invoice'])
                ->where(function ($query) use ($filters) {
                    $query->whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
                        ->orWhereBetween('check_out_date', [$filters['date_from'], $filters['date_to']]);
                })->get(),
        ];
    }

    private function getCityLedgerData($filters)
    {
        return [
            'reservations' => Reservation::with(['guest', 'invoice'])
                ->where(function ($q) use ($filters) {
                    $q->whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
                        ->orWhereBetween('check_out_date', [$filters['date_from'], $filters['date_to']]);
                })->get(),
        ];
    }

    private function getRevenueBySourceData($filters)
    {
        $reservations = Reservation::whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
            ->with(['source'])->get();

        $bySource = $reservations->groupBy(fn ($item) => $item->source->name ?? 'Direct')
            ->map(fn ($group) => ['count' => $group->count(), 'total' => $group->sum('grand_total')]);

        return ['bySource' => $bySource];
    }

    private function getReservationRevenueData($filters)
    {
        $reservations = Reservation::whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
            ->with(['guest', 'unit', 'invoice'])->get();

        return [
            'reservations' => $reservations,
            'totalRevenue' => $reservations->sum('grand_total'),
            'totalPaid' => $reservations->sum('paid_amount'),
            'totalOutstanding' => $reservations->sum('balance'),
        ];
    }

    private function getReservationSummaryData($filters)
    {
        $reservations = Reservation::whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
            ->with(['guest', 'unit'])->get();

        return [
            'reservations' => $reservations,
            'summary' => [
                'total' => $reservations->count(),
                'checked_in' => $reservations->where('status', 'checked_in')->count(),
                'checked_out' => $reservations->where('status', 'checked_out')->count(),
                'cancelled' => $reservations->where('status', 'cancelled')->count(),
                'pending' => $reservations->where('status', 'confirmed')->count(),
            ],
        ];
    }

    private function getReservationDetailsData($filters)
    {
        return [
            'reservations' => Reservation::whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
                ->with(['guest', 'unit', 'invoice'])
                ->orderBy('check_in_date', 'desc')->get(),
        ];
    }

    private function getExpectedArrivalsData($filters)
    {
        return [
            'reservations' => Reservation::whereBetween('check_in_date', [$filters['date_from'], $filters['date_to']])
                ->whereNotIn('status', ['cancelled', 'checked_out', 'no_show'])
                ->with(['guest', 'unit'])->orderBy('check_in_date', 'asc')->get(),
        ];
    }

    private function getExpectedDeparturesData($filters)
    {
        return [
            'reservations' => Reservation::whereBetween('check_out_date', [$filters['date_from'], $filters['date_to']])
                ->whereIn('status', ['checked_in', 'checked_out'])
                ->with(['guest', 'unit', 'invoice'])->orderBy('check_out_date', 'asc')->get(),
        ];
    }

    private function getNightAuditSummaryData($filters)
    {
        $audits = NightAudit::whereBetween('start_date_time', [$filters['date_from'], $filters['date_to']])
            ->with('user')->get();

        return [
            'audits' => $audits,
            'summary' => [
                'total' => $audits->count(),
                'completed' => $audits->where('status', 'completed')->count(),
                'pending' => $audits->where('status', 'pending')->count(),
                'failed' => $audits->where('status', 'failed')->count(),
            ],
        ];
    }

    private function getNightAuditHistoryData($filters)
    {
        return [
            'audits' => NightAudit::with('user')
                ->whereBetween('start_date_time', [$filters['date_from'], $filters['date_to']])
                ->orderBy('created_at', 'desc')->get(),
        ];
    }

    private function getOccupancyData($filters)
    {
        return $this->buildOccupancyData($filters);
    }

    private function getHousekeepingData($filters)
    {
        return [
            'units' => \App\Models\Unit::with(['floor', 'unitType'])->get(),
        ];
    }

    private function buildOccupancyData(array $filters): array
    {
        $start = Carbon::parse($filters['date_from'])->startOfDay();
        $end = Carbon::parse($filters['date_to'])->startOfDay();
        $reportEndExclusive = $end->copy()->addDay();
        $totalDays = $start->diffInDays($end) + 1;

        $tenantId = app(TenantContext::class)->id();
        $branchId = app(PropertyContext::class)->branchId();

        $unitsQuery = Unit::withoutGlobalScopes()
            ->with(['reservations' => function ($query) use ($start, $reportEndExclusive, $tenantId, $branchId) {
                $query->withoutGlobalScopes()
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->where('check_in_date', '<', $reportEndExclusive->toDateString())
                ->where('check_out_date', '>', $start->toDateString());

                if ($tenantId) {
                    $query->where('company_id', $tenantId);
                }

                if ($branchId) {
                    $query->where('branch_id', $branchId);
                }
            }, 'floor', 'unitType']);

        if ($tenantId) {
            $unitsQuery->where('company_id', $tenantId);
        }

        if ($branchId) {
            $unitsQuery->where('branch_id', $branchId);
        }

        $units = $unitsQuery->get();

        $units->each(function ($unit) use ($start, $reportEndExclusive) {
            $occupiedDates = [];

            foreach ($unit->reservations as $reservation) {
                $overlapStart = Carbon::parse($reservation->check_in_date)->startOfDay()->max($start);
                $overlapEndExclusive = Carbon::parse($reservation->check_out_date)->startOfDay()->min($reportEndExclusive);

                for ($date = $overlapStart->copy(); $date->lt($overlapEndExclusive); $date->addDay()) {
                    $occupiedDates[$date->toDateString()] = true;
                }
            }

            $unit->occupied_days = count($occupiedDates);
            $unit->is_occupied_in_range = $unit->occupied_days > 0;
        });

        $totalUnits = $units->count();
        $occupiedDays = $units->sum('occupied_days');
        $occupancyRate = $totalUnits > 0 ? round(($occupiedDays / ($totalUnits * $totalDays)) * 100, 2) : 0;

        return compact('units', 'totalUnits', 'occupiedDays', 'occupancyRate', 'totalDays');
    }
}

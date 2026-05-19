<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\DropCashVoucher;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodConfig;
use App\Models\ReceiptVoucher;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DropController extends Controller
{
    public function index(Request $request)
    {
        $query = DropCashVoucher::with(['user', 'creator', 'bank']);

        if ($request->voucher_number) {
            $query->where('voucher_number', 'like', '%'.$request->voucher_number.'%');
        }

        if ($request->drop_method) {
            $query->where('drop_method', $request->drop_method);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->amount_min) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->amount_max) {
            $query->where('amount', '<=', $request->amount_max);
        }

        if ($request->date_from) {
            $query->where('date_from', '>=', $request->date_from.' 00:00:00');
        }

        if ($request->date_to) {
            $query->where('date_to', '<=', $request->date_to.' 23:59:59');
        }

        $vouchers = $query->orderByDesc('id')->paginate(20);
        $printingOption = \App\Models\PrintingOption::where('report_key', 'drop_cash')->first();
        $property = \App\Models\Property::current();
        $dropMethods = DropCashVoucher::getDropMethods();
        $banks = Bank::where('is_active', 1)->orderBy('name')->get();
        $paymentMethods = PaymentMethod::all();

        return view('admin.voucher_cash.index', compact('vouchers', 'printingOption', 'property', 'dropMethods', 'banks', 'paymentMethods'));
    }

    public function calculateAmount(Request $request)
    {
        $request->validate([
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        $dateFrom = $request->date_from.' 00:00:00';
        $dateTo = $request->date_to.' 23:59:59';

        $cashMethodIds = PaymentMethodConfig::idsForMethodNameLike('%cash%');

        $query = ReceiptVoucher::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'active');

        if (! empty($cashMethodIds)) {
            $query->whereIn('payment_method_id', $cashMethodIds);
        }

        $total = $query->sum('amount');

        $securityDepositQuery = ReceiptVoucher::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'active')
            ->whereNotNull('reservation_id')
            ->whereHas('reservation', function ($q) {
                $q->whereNotNull('security_deposit');
            });

        if (! empty($cashMethodIds)) {
            $securityDepositQuery->whereIn('payment_method_id', $cashMethodIds);
        }

        $securityDeposit = $securityDepositQuery->sum('amount');
        $totalWithoutSecurity = $total - $securityDeposit;

        return response()->json([
            'success' => true,
            'data' => [
                'total' => round($total, 2),
                'security_deposit' => round($securityDeposit, 2),
                'total_without_security' => round($totalWithoutSecurity, 2),
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $companyId = app(TenantContext::class)->id() ?: $request->user()?->company_id;
        $branchId = app(PropertyContext::class)->branchId() ?: $request->user()?->branch_id;

        abort_unless($companyId && $branchId, 422, 'Please select or create a branch first.');

        $request->validate([
            'date_from' => 'required',
            'date_to' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'drop_method' => ['required', Rule::in(array_keys(DropCashVoucher::getDropMethods()))],
            'paid_to' => 'required|max:100',
            'purpose' => 'required|max:200',
        ]);

        try {
            $voucher = DropCashVoucher::create([
                'voucher_number' => DropCashVoucher::generateVoucherNumber($companyId, $branchId),
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'user_id' => auth()->id(),
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'amount' => $request->amount,
                'drop_method' => $request->drop_method,
                'bank_id' => $request->drop_method === 'bank_transfer' ? $request->bank_id : null,
                'paid_to' => $request->paid_to,
                'purpose' => $request->purpose,
                'comment' => $request->comment,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('dashboard.drop_cash_created'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $voucher = DropCashVoucher::with(['user', 'creator', 'bank'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'voucher' => [
                'id' => $voucher->id,
                'voucher_number' => $voucher->voucher_number,
                'date_from' => $voucher->date_from ? $voucher->date_from->format('Y-m-d H:i') : null,
                'date_to' => $voucher->date_to ? $voucher->date_to->format('Y-m-d H:i') : null,
                'amount' => $voucher->amount,
                'drop_method' => $voucher->drop_method,
                'bank_id' => $voucher->bank_id,
                'paid_to' => $voucher->paid_to,
                'purpose' => $voucher->purpose,
                'comment' => $voucher->comment,
                'user' => $voucher->user ? [
                    'id' => $voucher->user->id,
                    'name' => $voucher->user->name,
                ] : null,
                'bank' => $voucher->bank ? [
                    'id' => $voucher->bank->id,
                    'name' => $voucher->bank->name,
                ] : null,
                'creator' => $voucher->creator ? [
                    'id' => $voucher->creator->id,
                    'name' => $voucher->creator->name,
                ] : null,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'drop_method' => 'required',
            'paid_to' => 'required|max:100',
            'purpose' => 'required|max:200',
        ]);

        $voucher = DropCashVoucher::findOrFail($id);

        $voucher->update([
            'drop_method' => $request->drop_method,
            'bank_id' => $request->drop_method === 'bank_transfer' ? $request->bank_id : null,
            'paid_to' => $request->paid_to,
            'purpose' => $request->purpose,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('dashboard.drop_cash_updated'),
        ]);
    }

    public function destroy($id)
    {
        $voucher = DropCashVoucher::findOrFail($id);
        $voucher->delete();

        return response()->json([
            'success' => true,
            'message' => __('dashboard.drop_cash_deleted'),
        ]);
    }

    public function print(Request $request, $id)
    {
        $voucher = DropCashVoucher::with(['user', 'creator', 'bank'])->findOrFail($id);

        $printingOption = \App\Models\PrintingOption::where('report_key', 'drop_cash')->first();
        $globalSetting = \App\Models\PrintingOption::first();
        $property = \App\Models\Property::current(['commercialDetail']);

        return view('admin.voucher_cash.print', compact('voucher', 'printingOption', 'globalSetting', 'property'));
    }
}

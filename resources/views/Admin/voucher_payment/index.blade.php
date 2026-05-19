@extends('layouts.app')

@section('title', 'Payment Vouchers')
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@endpush
<style>
    .Parent-Contact { display: flex; align-items: center; justify-content: flex-end; gap: 1rem; margin-bottom: 1rem; }
    .contact-number.style-number { color: #333; font-weight: 500; font-size: 0.9rem; }
    .contact-number.background-icon, .contact-number.u-cursor-pointer { width: 32px; height: 32px; border-radius: 50%; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; text-decoration: none; }
    .page-category { font-size: 0.875rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .page-header__title { font-size: 1.75rem; font-weight: 600; color: #2c3e50; margin-bottom: 0.5rem; }
    .page-header__subtitle { font-size: 1rem; color: #6c757d; }
    .n-table__top-btns { display: flex; gap: 0.75rem; }
    .n-button { padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; cursor: pointer; transition: all 0.2s ease; border: 1px solid transparent; display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; }
    .n-button--primary { background-color: white; color: #333; border-color: #dee2e6; }
    .n-button--primary:hover { background-color: #f8f9fa; border-color: #4a90e2; }
    .filter-form__container { background-color: #343a40; border-radius: 0.5rem; margin-bottom: 1.5rem; overflow: hidden; }
    .filter-form { padding: 1.5rem; }
    .filter-form--dark label { color: #e9ecef; font-weight: 500; margin-bottom: 0.5rem; display: block; font-size: 0.875rem; }
    .filter-form--dark .form-control { background-color: #495057; border: 1px solid #6c757d; color: white; width: 100%; padding: 0.5rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; }
</style>
@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">
        <div class="page-category">{{ __('dashboard.vouchers') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.payment') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" id="toggleFilterBtn">{{ __('dashboard.filter') }}</button>
                @can('payment.add')
                      <button class="n-button n-button--primary" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> {{ __('dashboard.add_payment_voucher') }}
                </button>
                @endcan

            </div>
        </div>

        <form method="GET" action="{{ route('dashboard.payment.index') }}">
            <div class="filter-form__container mb-4" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.voucher_number') }}</label>
                                <input type="text" name="voucher_number" value="{{ request('voucher_number') }}" class="form-control" placeholder="{{ __('dashboard.enter_voucher_number') }}">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.vendor_name') }}</label>
                                <input type="text" name="vendor_name" value="{{ request('vendor_name') }}" class="form-control" placeholder="{{ __('dashboard.enter_vendor_name') }}">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.payment_method') }}</label>
                                <select name="payment_method" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}" {{ request('payment_method') == $method->id ? 'selected' : '' }}>{{ $method->paymentMethod->name ?? 'N/A' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('dashboard.active') }}</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('dashboard.cancelled') }}</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.date_from') }}</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.date_to') }}</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                            </div>
                            <div class="col-lg-2 col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">{{ __('dashboard.search') }}</button>
                                <a href="{{ route('dashboard.payment.index') }}" class="btn btn-outline-secondary">{{ __('dashboard.reset') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-body" style="overflow-x: auto;">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap">{{ __('dashboard.no') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.type') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.amount') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.payment_method') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.vendor_name') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.purpose') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.reservation_no') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.date_time') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.outlet') }}</th>
                            <th class="text-center text-nowrap">{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $voucher)
                            <tr>
                                <td>{{ $voucher->voucher_number }}</td>
                                <td>
                                    @if($voucher->voucher_type === 'refund')
                                        <span class="badge bg-warning">Refund Voucher</span>
                                    @else
                                        <span class="badge bg-primary">Payment Voucher</span>
                                    @endif
                                </td>
                                <td>{{ number_format($voucher->amount, 2) }} SAR</td>
                                <td>{{ $voucher->paymentMethod->paymentMethod->name ?? $voucher->paymentMethod->name ?? '-' }}</td>
                                <td>{{ $voucher->voucher_type === 'refund' ? ($voucher->guest->first_name . ' ' . $voucher->guest->last_name ?? '-') : ($voucher->vendor_name ?? '-') }}</td>
                                <td>{{ $voucher->purpose ?? '-' }}</td>
                                <td>{{ $voucher->reservation->reservation_number ?? '-' }}</td>
                                <td>{{ $voucher->date ? \Carbon\Carbon::parse($voucher->date)->format('d/m/Y') : '-' }} {{ $voucher->time ? \Carbon\Carbon::parse($voucher->time)->format('H:i') : '' }}</td>
                                <td>{{ $voucher->reservation->property->property_name_en ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-secondary py-1 px-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('payment.print')
                                                 <li><a class="dropdown-item" href="#" onclick="printVoucher({{ $voucher->id }})"><i class="fas fa-print me-2"></i> {{__('dashboard.print')}}</a></li>
                                            @endcan

                                            @if($voucher->status === 'active' || $voucher->status === 'pending')
                                            @can('payment.edit')
                                               <li><a class="dropdown-item" href="#" onclick="editVoucher({{ $voucher->id }})"><i class="fas fa-edit me-2"></i> {{__('dashboard.edit')}}</a></li>
                                            @endcan
                                            @can('payment.cancel')
                                                 <li><a class="dropdown-item text-danger" href="#" onclick="cancelVoucher({{ $voucher->id }})"><i class="fas fa-times me-2"></i> {{__('dashboard.cancel')}}</a></li>
                                            @endcan

                                            @else
                                            <li><span class="badge bg-danger ms-2">{{ __('dashboard.cancelled') }}</span></li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center py-4 text-muted">{{ __('dashboard.no_records_found') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">{{ $vouchers->appends(request()->query())->links() }}</div>
        </div>
    </main>

    <!-- Add Voucher Modal -->
    <div class="modal fade" id="addVoucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <div>
                        <h5 class="modal-title">{{ __('dashboard.add_payment_voucher') }}</h5>
                        <p class="mb-0 small text-white-50">{{ __('dashboard.add_payment_voucher_desc') }}</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addVoucherForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.date') }} <span class="text-danger">*</span></label>
                                <input type="date" id="addDate" class="form-control" value="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.time') }} <span class="text-danger">*</span></label>
                                <input type="time" id="addTime" class="form-control" value="{{ now()->format('H:i') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.cost_center') }} <span class="text-danger">*</span></label>
                                <select id="addCostCenter" class="form-select" required>
                                    <option value="">{{ __('dashboard.select_cost_center') }}</option>
                                    @foreach($costCenters as $center)
                                    <option value="{{ $center->id }}">{{ $center->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.purpose') }} <span class="text-danger">*</span></label>
                                <input type="text" id="addPurpose" class="form-control" placeholder="{{ __('dashboard.enter_purpose') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.vendor') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" id="addVendorName" class="form-control" placeholder="{{ __('dashboard.search_or_enter_vendor') }}" required>
                                    <input type="hidden" id="addVendorId">
                                    <button class="btn btn-outline-secondary" type="button" onclick="openVendorSearchModal()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('dashboard.vendor_tax_no') }}</label>
                                <input type="text" id="addVendorTaxNo" class="form-control" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('dashboard.vendor_invoice_no') }}</label>
                                <input type="text" id="addVendorInvoiceNo" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('dashboard.amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" id="addAmount" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                                    <span class="input-group-text">SAR</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('dashboard.vat_type') }}</label>
                                <select id="addVatType" class="form-select" onchange="toggleVatInput()">
                                    <option value="no_vat">{{ __('dashboard.no_vat') }}</option>
                                    <option value="manual">{{ __('dashboard.manual') }}</option>
                                    <option value="auto">{{ __('dashboard.auto') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('dashboard.vat_percentage') }}</label>
                                <input type="number" id="addVatPercentage" class="form-control" placeholder="15" step="0.01" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('dashboard.vat_amount') }}</label>
                                <input type="number" id="addVatAmount" class="form-control" placeholder="0.00" step="0.01" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('dashboard.amount_before_vat') }}</label>
                                <input type="number" id="addAmountBeforeVat" class="form-control" placeholder="0.00" step="0.01" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.payment_method') }} <span class="text-danger">*</span></label>
                                <select id="addPaymentMethod" class="form-select" onchange="togglePaymentFields()" required>
                                    <option value="">{{ __('dashboard.select_payment_method') }}</option>
                                    @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}" data-name="{{ $method->paymentMethod->name ?? '' }}">{{ $method->paymentMethod->name ?? 'N/A' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="madaFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.receiving_bank') }}</label>
                                    <select id="addReceivingBank" class="form-select">
                                        <option value="">{{ __('dashboard.select_bank') }}</option>
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.transaction_number') }}</label>
                                    <input type="text" id="addTransactionNumber" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div id="chequeFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('dashboard.receiving_bank') }}</label>
                                    <select id="addChequeReceivingBank" class="form-select">
                                        <option value="">{{ __('dashboard.select_bank') }}</option>
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('dashboard.sending_bank_name') }}</label>
                                    <input type="text" id="addSendingBankName" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('dashboard.cheque_number') }}</label>
                                    <input type="text" id="addChequeNumber" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('dashboard.comment') }}</label>
                                <textarea id="addComment" class="form-control" rows="3" placeholder="{{ __('dashboard.enter_comment') }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.discard') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('dashboard.add_voucher') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Vendor Search Modal -->
    <div class="modal fade" id="vendorSearchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.search_vendor') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" id="vendorSearchInput" class="form-control" placeholder="{{ __('dashboard.search_by_name_phone_vat') }}">
                            <button class="btn btn-primary" type="button" onclick="searchVendors()">
                                <i class="fas fa-search"></i> {{ __('dashboard.search') }}
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('dashboard.vendor_name') }}</th>
                                    <th>{{ __('dashboard.phone') }}</th>
                                    <th>{{ __('dashboard.vendor_tax_no') }}</th>
                                    <th>{{ __('dashboard.email') }}</th>
                                    <th>{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="vendorSearchResults">
                                @foreach($vendors as $vendor)
                                <tr>
                                    <td>{{ $vendor->name }}</td>
                                    <td>{{ $vendor->phone ?? '-' }}</td>
                                    <td>{{ $vendor->vat_registration_number ?? '-' }}</td>
                                    <td>{{ $vendor->email ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="selectVendor({{ $vendor->id }}, '{{ $vendor->name }}', '{{ $vendor->vat_registration_number ?? '' }}')">
                                            <i class="fas fa-check"></i> {{ __('dashboard.select') }}
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Voucher Modal -->
    <div class="modal fade" id="editVoucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <div>
                        <h5 class="modal-title" id="editModalTitle">{{ __('dashboard.edit_payment_voucher') }}</h5>
                        <p class="mb-0 small text-white-50" id="editModalDesc">{{ __('dashboard.edit_payment_voucher_desc') }}</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editVoucherForm">
                    <div class="modal-body">
                        <input type="hidden" id="editVoucherId">
                        <input type="hidden" id="editVoucherType" value="payment">
                        <input type="hidden" id="editVendorName">
                        <input type="hidden" id="editVendorTaxNo">
                        <input type="hidden" id="editVendorInvoiceNo">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted">{{ __('dashboard.voucher_number') }}</small>
                                    <div class="fw-bold" id="editVoucherNumberDisplay">-</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted">{{ __('dashboard.reservation_no') }}</small>
                                    <div class="fw-bold" id="editReservationNumberDisplay">-</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted" id="editVendorLabel">{{ __('dashboard.vendor_name') }}</small>
                                    <div class="fw-bold" id="editVendorNameDisplay">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.date') }} <span class="text-danger">*</span></label>
                                <input type="date" id="editDate" class="form-control edit-disable-refund" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.time') }} <span class="text-danger">*</span></label>
                                <input type="time" id="editTime" class="form-control edit-disable-refund" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.cost_center') }}</label>
                                <select id="editCostCenter" class="form-select edit-disable-refund">
                                    <option value="">{{ __('dashboard.select_cost_center') }}</option>
                                    @foreach($costCenters as $center)
                                    <option value="{{ $center->id }}">{{ $center->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.purpose') }} <span class="text-danger">*</span></label>
                                <input type="text" id="editPurpose" class="form-control edit-disable-refund" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('dashboard.amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" id="editAmount" class="form-control edit-disable-refund" placeholder="0.00" step="0.01" min="0" required>
                                    <span class="input-group-text">SAR</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('dashboard.vat_type') }}</label>
                                <select id="editVatType" class="form-select edit-disable-refund" onchange="toggleEditVatInput()">
                                    <option value="no_vat">{{ __('dashboard.no_vat') }}</option>
                                    <option value="manual">{{ __('dashboard.manual') }}</option>
                                    <option value="auto">{{ __('dashboard.auto') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('dashboard.vat_percentage') }}</label>
                                <input type="number" id="editVatPercentage" class="form-control edit-disable-refund" placeholder="15" step="0.01" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('dashboard.vat_amount') }}</label>
                                <input type="number" id="editVatAmount" class="form-control edit-disable-refund" placeholder="0.00" step="0.01" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('dashboard.amount_before_vat') }}</label>
                                <input type="number" id="editAmountBeforeVat" class="form-control edit-disable-refund" placeholder="0.00" step="0.01" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.payment_method') }} <span class="text-danger">*</span></label>
                                <select id="editPaymentMethod" class="form-select" onchange="toggleEditPaymentFields()" required>
                                    <option value="">{{ __('dashboard.select_payment_method') }}</option>
                                    @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}" data-name="{{ $method->paymentMethod->name ?? '' }}">{{ $method->paymentMethod->name ?? 'N/A' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="editMadaFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.receiving_bank') }}</label>
                                    <select id="editReceivingBank" class="form-select">
                                        <option value="">{{ __('dashboard.select_bank') }}</option>
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.transaction_number') }}</label>
                                    <input type="text" id="editTransactionNumber" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div id="editChequeFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('dashboard.receiving_bank') }}</label>
                                    <select id="editChequeReceivingBank" class="form-select">
                                        <option value="">{{ __('dashboard.select_bank') }}</option>
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('dashboard.sending_bank_name') }}</label>
                                    <input type="text" id="editSendingBankName" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('dashboard.cheque_number') }}</label>
                                    <input type="text" id="editChequeNumber" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('dashboard.comment') }}</label>
                                <textarea id="editComment" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.discard') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('dashboard.save_changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Voucher Modal -->
    <div class="modal fade" id="cancelVoucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.cancel_voucher') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="cancelVoucherForm">
                    <div class="modal-body">
                        <input type="hidden" id="cancelVoucherId">
                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.cancel_reason') }}</label>
                            <textarea id="cancelReason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('dashboard.confirm_cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Print Modal -->
    <div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header no-print">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('en')">English</button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('ar')">العربية</button>
                        @if(optional($printingOption)->contract_template_type == 'double')
                        <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('both')">Both</button>
                        @endif
                    </div>
                    <div class="btn-group ms-3" role="group">
                        <button type="button" class="btn btn-primary" onclick="printPage()"><i class="bi bi-printer"></i> Print</button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="printIframe" src="" style="width: 100%; height: 70vh; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var taxConfigs = @json($taxConfigs);

        function showToast(message, isError = false) {
            const bgClass = isError ? 'alert-danger' : 'alert-success';
            let toast = `<div class="alert ${bgClass} alert-dismissible fade show position-fixed" style="top:20px; right:20px; z-index:9999;">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            document.body.insertAdjacentHTML('beforeend', toast);
            setTimeout(function() { document.querySelector('.alert')?.remove(); }, 3000);
        }

        const toggleBtn = document.getElementById('toggleFilterBtn');
        const filterContainer = document.querySelector('.filter-form__container');
        toggleBtn?.addEventListener('click', function() { filterContainer.style.display = filterContainer.style.display === 'none' ? 'block' : 'none'; });
        @if(request()->hasAny(['voucher_number', 'vendor_name', 'status', 'payment_method', 'date_from', 'date_to'])) filterContainer.style.display = 'block'; @endif

        const paymentVoucherIndexUrl = @json(route('dashboard.payment.store'));
        const paymentVoucherShowUrlTemplate = @json(route('dashboard.payment.show', ['id' => '__PAYMENT__']));
        const paymentVoucherPrintUrlTemplate = @json(route('dashboard.payment.print', ['id' => '__PAYMENT__']));
        const paymentVoucherUpdateUrlTemplate = @json(route('dashboard.payment.update', ['id' => '__PAYMENT__']));
        const paymentVoucherCancelUrlTemplate = @json(route('dashboard.payment.cancel', ['id' => '__PAYMENT__']));
        const paymentVoucherSearchVendorsUrl = @json(route('dashboard.payment.searchVendors'));
        const paymentVoucherJsonHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        };
        const paymentVoucherUrl = (template, id) => template.replace('__PAYMENT__', encodeURIComponent(id));

        function printVoucher(id) {
            document.getElementById('printIframe').src = paymentVoucherUrl(paymentVoucherPrintUrlTemplate, id);
            new bootstrap.Modal(document.getElementById('printModal')).show();
            document.getElementById('printIframe').onload = function() { setTimeout(function() { switchPrintLang('en'); }, 500); };
        }

        function switchPrintLang(lang) {
            const iframe = document.getElementById('printIframe');
            try { if (iframe?.contentWindow?.switchLanguage) iframe.contentWindow.switchLanguage(lang); }
            catch(e) { setTimeout(function() { if (iframe?.contentWindow?.switchLanguage) iframe.contentWindow.switchLanguage(lang); }, 500); }
        }

        function printPage() { document.querySelector('#printIframe')?.contentWindow?.print(); }

        function openVendorSearchModal() { document.getElementById('vendorSearchInput').value = ''; new bootstrap.Modal(document.getElementById('vendorSearchModal')).show(); }

        function searchVendors() {
            const search = document.getElementById('vendorSearchInput').value;
            fetch(`${paymentVoucherSearchVendorsUrl}?q=${encodeURIComponent(search)}`, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(vendors => {
                    const tbody = document.getElementById('vendorSearchResults');
                    if (vendors.length === 0) { tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No records found</td></tr>'; }
                    else {
                        tbody.innerHTML = vendors.map(v => `
                            <tr>
                                <td>${v.name}</td>
                                <td>${v.phone || '-'}</td>
                                <td>${v.vat_registration_number || '-'}</td>
                                <td>${v.email || '-'}</td>
                                <td><button class="btn btn-sm btn-success" onclick="selectVendor(${v.id}, '${v.name}', '${v.vat_registration_number || ''}')"><i class="fas fa-check"></i> Select</button></td>
                            </tr>
                        `).join('');
                    }
                });
        }

        function selectVendor(id, name, taxNo) {
            document.getElementById('addVendorName').value = name;
            document.getElementById('addVendorId').value = id;
            document.getElementById('addVendorTaxNo').value = taxNo;
            bootstrap.Modal.getInstance(document.getElementById('vendorSearchModal')).hide();
        }

        document.getElementById('vendorSearchInput')?.addEventListener('keypress', function(e) { if (e.key === 'Enter') searchVendors(); });

        function toggleVatInput() {
            const vatType = document.getElementById('addVatType').value;
            document.getElementById('addVatPercentage').disabled = vatType === 'no_vat';
            calculateVat();
        }

        function toggleEditVatInput() {
            const vatType = document.getElementById('editVatType').value;
            document.getElementById('editVatPercentage').disabled = vatType === 'no_vat';
            calculateEditVat();
        }

        function calculateVat() {
            const amount = parseFloat(document.getElementById('addAmount').value) || 0;
            const vatType = document.getElementById('addVatType').value;
            let vatPercentage = 0;
            if (vatType === 'manual') {
                vatPercentage = parseFloat(document.getElementById('addVatPercentage').value) || 0;
            } else if (vatType === 'auto' && taxConfigs.length > 0) {
                vatPercentage = parseFloat(taxConfigs[0].amount) || 15;
                document.getElementById('addVatPercentage').value = vatPercentage;
            }
            const vatAmount = (amount * vatPercentage / 100);
            document.getElementById('addVatAmount').value = vatAmount.toFixed(2);
            document.getElementById('addAmountBeforeVat').value = amount.toFixed(2);
        }

        function calculateEditVat() {
            const amount = parseFloat(document.getElementById('editAmount').value) || 0;
            const vatType = document.getElementById('editVatType').value;
            let vatPercentage = 0;
            if (vatType === 'manual') {
                vatPercentage = parseFloat(document.getElementById('editVatPercentage').value) || 0;
            } else if (vatType === 'auto' && taxConfigs.length > 0) {
                vatPercentage = parseFloat(taxConfigs[0].amount) || 15;
                document.getElementById('editVatPercentage').value = vatPercentage;
            }
            const vatAmount = (amount * vatPercentage / 100);
            document.getElementById('editVatAmount').value = vatAmount.toFixed(2);
            document.getElementById('editAmountBeforeVat').value = amount.toFixed(2);
        }

        document.getElementById('addAmount').addEventListener('input', calculateVat);
        document.getElementById('addVatPercentage').addEventListener('input', calculateVat);
        document.getElementById('editAmount').addEventListener('input', calculateEditVat);
        document.getElementById('editVatPercentage').addEventListener('input', calculateEditVat);

        function togglePaymentFields() {
            const sel = document.getElementById('addPaymentMethod');
            const name = sel.options[sel.selectedIndex]?.getAttribute('data-name')?.toLowerCase() || '';
            document.getElementById('madaFields').style.display = (name.includes('mada') || name.includes('مدى')) ? 'block' : 'none';
            document.getElementById('chequeFields').style.display = (name.includes('cheque') || name.includes('شيك')) ? 'block' : 'none';
        }

        function toggleEditPaymentFields() {
            const sel = document.getElementById('editPaymentMethod');
            const name = sel.options[sel.selectedIndex]?.getAttribute('data-name')?.toLowerCase() || '';
            document.getElementById('editMadaFields').style.display = (name.includes('mada') || name.includes('مدى')) ? 'block' : 'none';
            document.getElementById('editChequeFields').style.display = (name.includes('cheque') || name.includes('شيك')) ? 'block' : 'none';
        }

        function openAddModal() {
            document.getElementById('addVoucherForm').reset();
            document.getElementById('addDate').value = new Date().toISOString().split('T')[0];
            document.getElementById('addTime').value = new Date().toTimeString().slice(0, 5);
            document.getElementById('addVendorId').value = '';
            document.getElementById('addVatType').value = 'no_vat';
            togglePaymentFields();
            new bootstrap.Modal(document.getElementById('addVoucherModal')).show();
        }

        document.getElementById('addVoucherForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const sel = document.getElementById('addPaymentMethod');
            const pname = sel.options[sel.selectedIndex]?.getAttribute('data-name')?.toLowerCase() || '';
            let rbid = null, tn = null, sbn = null, cn = null;
            if (pname.includes('mada') || pname.includes('مدى')) { rbid = document.getElementById('addReceivingBank').value; tn = document.getElementById('addTransactionNumber').value; }
            else if (pname.includes('cheque') || pname.includes('شيك')) { rbid = document.getElementById('addChequeReceivingBank').value; sbn = document.getElementById('addSendingBankName').value; cn = document.getElementById('addChequeNumber').value; }

            const vatType = document.getElementById('addVatType').value;
            const amount = parseFloat(document.getElementById('addAmount').value) || 0;
            const vatAmount = parseFloat(document.getElementById('addVatAmount').value) || 0;

            fetch(paymentVoucherIndexUrl, {
                method: 'POST', headers: paymentVoucherJsonHeaders,
                body: JSON.stringify({
                    date: document.getElementById('addDate').value, time: document.getElementById('addTime').value,
                    cost_center_id: document.getElementById('addCostCenter').value || null, purpose: document.getElementById('addPurpose').value,
                    vendor_name: document.getElementById('addVendorName').value, vendor_id: document.getElementById('addVendorId').value,
                    vendor_tax_no: document.getElementById('addVendorTaxNo').value,
                    vendor_invoice_no: document.getElementById('addVendorInvoiceNo').value, amount: amount,
                    apply_vat: vatType !== 'no_vat', vat_amount: vatAmount,
                    vat_type: vatType, vat_percentage: document.getElementById('addVatPercentage').value || 0,
                    payment_method_id: document.getElementById('addPaymentMethod').value, receiving_bank_id: rbid, transaction_number: tn,
                    sending_bank_name: sbn, cheque_number: cn, comment: document.getElementById('addComment').value,
                })
            }).then(r => r.json()).then(data => {
                if (data.success) { bootstrap.Modal.getInstance(document.getElementById('addVoucherModal')).hide(); showToast('Voucher created successfully!'); location.reload(); }
                else { showToast(data.message || 'Error creating voucher', true); }
            }).catch(err => { console.error(err); showToast('Error creating voucher', true); });
        });

        function editVoucher(id) {
            fetch(paymentVoucherUrl(paymentVoucherShowUrlTemplate, id), { headers: { 'Accept': 'application/json' } }).then(r => r.json()).then(data => {
                const v = data.voucher;
                const isRefund = v.voucher_type === 'refund';

                document.getElementById('editVoucherId').value = v.id;
                document.getElementById('editVoucherType').value = v.voucher_type || 'payment';
                document.getElementById('editVoucherNumberDisplay').textContent = v.voucher_number || '-';
                document.getElementById('editReservationNumberDisplay').textContent = v.reservation?.reservation_number || '-';

                if (isRefund) {
                    document.getElementById('editModalTitle').textContent = '{{ __("dashboard.edit_refund_voucher") }}';
                    document.getElementById('editModalDesc').textContent = '{{ __("dashboard.edit_refund_voucher_desc") }}';
                    document.getElementById('editVendorLabel').textContent = '{{ __("dashboard.guest_name") }}';
                    document.getElementById('editVendorNameDisplay').textContent = v.guest?.name || '-';
                } else {
                    document.getElementById('editModalTitle').textContent = '{{ __("dashboard.edit_payment_voucher") }}';
                    document.getElementById('editModalDesc').textContent = '{{ __("dashboard.edit_payment_voucher_desc") }}';
                    document.getElementById('editVendorLabel').textContent = '{{ __("dashboard.vendor_name") }}';
                    document.getElementById('editVendorNameDisplay').textContent = v.vendor_name || '-';
                }

                document.getElementById('editDate').value = v.date || '';
                document.getElementById('editTime').value = v.time ? v.time.substring(0, 5) : '';
                document.getElementById('editCostCenter').value = v.cost_center_id || '';
                document.getElementById('editPurpose').value = v.purpose || '';
                document.getElementById('editAmount').value = v.amount;
                document.getElementById('editVatType').value = v.apply_vat ? 'manual' : 'no_vat';
                document.getElementById('editVatPercentage').value = v.vat_percentage || 15;
                document.getElementById('editVatAmount').value = v.vat_amount || 0;
                document.getElementById('editAmountBeforeVat').value = v.amount_before_vat || v.amount;
                document.getElementById('editVendorName').value = v.vendor_name || v.guest?.name || '';
                document.getElementById('editVendorTaxNo').value = v.vendor_tax_no || '';
                document.getElementById('editVendorInvoiceNo').value = v.vendor_invoice_no || '';
                document.getElementById('editPaymentMethod').value = String(v.payment_method_id || '');
                document.getElementById('editReceivingBank').value = v.receiving_bank_id || '';
                document.getElementById('editTransactionNumber').value = v.transaction_number || '';
                document.getElementById('editChequeReceivingBank').value = v.receiving_bank_id || '';
                document.getElementById('editSendingBankName').value = v.sending_bank_name || '';
                document.getElementById('editChequeNumber').value = v.cheque_number || '';
                document.getElementById('editComment').value = v.comment || '';

                // Disable/enable fields based on voucher type
                const disableFields = document.querySelectorAll('.edit-disable-refund');
                disableFields.forEach(field => {
                    field.disabled = isRefund;
                });
                document.getElementById('editPaymentMethod').disabled = false;
                document.getElementById('editReceivingBank').disabled = false;
                document.getElementById('editTransactionNumber').disabled = false;
                document.getElementById('editChequeReceivingBank').disabled = false;
                document.getElementById('editSendingBankName').disabled = false;
                document.getElementById('editChequeNumber').disabled = false;
                document.getElementById('editComment').disabled = false;

                toggleEditPaymentFields();
                toggleEditVatInput();
                new bootstrap.Modal(document.getElementById('editVoucherModal')).show();
            });
        }

        document.getElementById('editVoucherForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const sel = document.getElementById('editPaymentMethod');
            const pname = sel.options[sel.selectedIndex]?.getAttribute('data-name')?.toLowerCase() || '';
            let rbid = null, tn = null, sbn = null, cn = null;
            if (pname.includes('mada') || pname.includes('مدى')) { rbid = document.getElementById('editReceivingBank').value; tn = document.getElementById('editTransactionNumber').value; }
            else if (pname.includes('cheque') || pname.includes('شيك')) { rbid = document.getElementById('editChequeReceivingBank').value; sbn = document.getElementById('editSendingBankName').value; cn = document.getElementById('editChequeNumber').value; }

            const vatType = document.getElementById('editVatType').value;
            const amount = parseFloat(document.getElementById('editAmount').value) || 0;
            const vatAmount = parseFloat(document.getElementById('editVatAmount').value) || 0;

            fetch(paymentVoucherUrl(paymentVoucherUpdateUrlTemplate, document.getElementById('editVoucherId').value), {
                method: 'PUT', headers: paymentVoucherJsonHeaders,
                body: JSON.stringify({
                    date: document.getElementById('editDate').value, time: document.getElementById('editTime').value,
                    cost_center_id: document.getElementById('editCostCenter').value || null, purpose: document.getElementById('editPurpose').value,
                    vendor_name: document.getElementById('editVendorName').value,
                    vendor_tax_no: document.getElementById('editVendorTaxNo').value,
                    vendor_invoice_no: document.getElementById('editVendorInvoiceNo').value, amount: amount,
                    amount_before_vat: parseFloat(document.getElementById('editAmountBeforeVat').value) || amount,
                    apply_vat: vatType !== 'no_vat', vat_amount: vatAmount,
                    vat_type: vatType, vat_percentage: document.getElementById('editVatPercentage').value || 0,
                    payment_method_id: document.getElementById('editPaymentMethod').value, receiving_bank_id: rbid, transaction_number: tn,
                    sending_bank_name: sbn, cheque_number: cn, comment: document.getElementById('editComment').value,
                    voucher_type: document.getElementById('editVoucherType').value,
                })
            }).then(r => r.json()).then(data => {
                if (data.success) { bootstrap.Modal.getInstance(document.getElementById('editVoucherModal')).hide(); showToast('Voucher updated successfully!'); location.reload(); }
                else { showToast(data.message || 'Error updating voucher', true); }
            }).catch(err => {
                console.error(err);
                showToast('Error updating voucher', true);
            });
        });

        function cancelVoucher(id) { document.getElementById('cancelVoucherId').value = id; new bootstrap.Modal(document.getElementById('cancelVoucherModal')).show(); }

        document.getElementById('cancelVoucherForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(paymentVoucherUrl(paymentVoucherCancelUrlTemplate, document.getElementById('cancelVoucherId').value), {
                method: 'POST', headers: paymentVoucherJsonHeaders,
                body: JSON.stringify({ cancel_reason: document.getElementById('cancelReason').value })
            }).then(r => r.json()).then(data => {
                if (data.success) { bootstrap.Modal.getInstance(document.getElementById('cancelVoucherModal')).hide(); showToast('Voucher cancelled successfully!'); location.reload(); }
            });
        });
        
(function() {
    document.addEventListener('show.bs.dropdown', function(e) {
        var btn = e.target;
        if (!btn.closest('.table-responsive') && !btn.closest('[style*="overflow"]')) return;
        var menu = btn.closest('.dropdown').querySelector('.dropdown-menu');
        btn._ddFix = { menu: menu, parent: menu.parentNode };
    });
    document.addEventListener('shown.bs.dropdown', function(e) {
        var btn = e.target;
        var ref = btn._ddFix;
        if (!ref || !ref.menu) return;
        var r = ref.menu.getBoundingClientRect();
        document.body.appendChild(ref.menu);
        ref.menu.style.position = 'fixed';
        ref.menu.style.top = r.top + 'px';
        ref.menu.style.left = r.left + 'px';
        ref.menu.style.transform = 'none';
    });
    document.addEventListener('hidden.bs.dropdown', function(e) {
        var btn = e.target;
        var ref = btn._ddFix;
        if (!ref) return;
        if (ref.menu && ref.menu.parentNode === document.body) {
            ref.menu.style.cssText = '';
            ref.parent.appendChild(ref.menu);
        }
        delete btn._ddFix;
    });
})();
    </script>
@endpush

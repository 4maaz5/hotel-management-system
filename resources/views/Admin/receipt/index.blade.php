@extends('layouts.app')

@section('title', 'Receipt Vouchers')
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@endpush
<style>
    .parent-Contact {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .contact-number.style-number {
        color: #333;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .contact-number.background-icon,
    .contact-number.u-cursor-pointer {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .page-category {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-header__title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .page-header__subtitle {
        font-size: 1rem;
        color: #6c757d;
    }

    .n-table__top-btns {
        display: flex;
        gap: 0.75rem;
    }

    .n-button {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    .n-button--primary {
        background-color: white;
        color: #333;
        border-color: #dee2e6;
    }

    .n-button--primary:hover {
        background-color: #f8f9fa;
        border-color: #4a90e2;
    }

    .filter-form__container {
        background-color: #343a40;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .filter-form {
        padding: 1.5rem;
    }

    .filter-form--dark label {
        color: #e9ecef;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.875rem;
    }

    .filter-form--dark .form-control {
        background-color: #495057;
        border: 1px solid #6c757d;
        color: white;
        width: 100%;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
</style>
@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">
        <div class="page-category">{{ __('dashboard.vouchers') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.receipt_vouchers') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" id="toggleFilterBtn">
                    {{ __('dashboard.filter') }}
                </button>
                @can('receipt.add')
                      <button class="n-button n-button--primary" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> {{ __('dashboard.add_receipt_voucher') }}
                </button>
                @endcan

            </div>
        </div>

        <form method="GET" action="{{ route('dashboard.receipt.index') }}">
            <div class="filter-form__container mb-4" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.voucher_number') }}</label>
                                <input type="text" name="voucher_number" value="{{ request('voucher_number') }}" class="form-control" placeholder="{{ __('dashboard.enter_voucher_number') }}">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.guest_name') }}</label>
                                <input type="text" name="guest_name" value="{{ request('guest_name') }}" class="form-control" placeholder="{{ __('dashboard.enter_guest_name') }}">
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
                                <a href="{{ route('dashboard.receipt.index') }}" class="btn btn-outline-secondary">{{ __('dashboard.reset') }}</a>
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
                            <th class="text-nowrap">{{ __('dashboard.received_from') }}</th>
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
                                <td>Receipt Voucher</td>
                                <td>{{ number_format($voucher->amount, 2) }} SAR</td>
                                <td>{{ $voucher->paymentMethod->paymentMethod->name ?? $voucher->paymentMethod->name ?? '-' }}</td>
                                <td>{{ $voucher->received_from_name ?? '-' }}</td>
                                <td>{{ $voucher->purpose ?? '-' }}</td>
                                <td>{{ $voucher->reservation->reservation_number ?? '-' }}</td>
                                <td>{{ $voucher->date ? \Carbon\Carbon::parse($voucher->date)->format('d/m/Y') : '-' }} {{ $voucher->time ? \Carbon\Carbon::parse($voucher->time)->format('H:i') : '' }}</td>
                                <td>{{ $voucher->reservation->property->property_name_en ?? '-' }}</td>
                        <td class="text-center">
    <div class="dropdown d-inline-block">
        <button class="btn btn-sm btn-secondary py-1 px-2 dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
            <i class="fas fa-ellipsis-v"></i>
        </button>

        <ul class="dropdown-menu dropdown-menu-end">
@can('receipt.print')
     <li>
                <a class="dropdown-item" href="#" onclick="printVoucher({{ $voucher->id }})">
                    <i class="fas fa-print me-2"></i> {{__('dashboard.print')}}
                </a>
            </li>
@endcan

            @if($voucher->status === 'active')
@can('receipt.edit')
    <li>
                <a class="dropdown-item" href="#" onclick="editVoucher({{ $voucher->id }})">
                    <i class="fas fa-edit me-2"></i> {{__('dashboard.edit')}}
                </a>
            </li>
@endcan

@can('receipt.cancel')
     <li>
                <a class="dropdown-item text-danger" href="#" onclick="cancelVoucher({{ $voucher->id }})">
                    <i class="fas fa-times me-2"></i> {{__('dashboard.cancel')}}
                </a>
            </li>
@endcan


            @else

            <li>
                <span class="dropdown-item text-danger">
                    {{ __('dashboard.cancelled') }}
                </span>
            </li>

            @endif

        </ul>
    </div>
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox me-2"></i>
                                    {{ __('dashboard.no_records_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $vouchers->appends(request()->query())->links() }}
            </div>
        </div>
    </main>

    <!-- Add Voucher Modal -->
    <div class="modal fade" id="addVoucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <div>
                        <h5 class="modal-title">{{ __('dashboard.add_receive_money_voucher') }}</h5>
                        <p class="mb-0 small text-white-50">{{ __('dashboard.add_receive_money_voucher_desc') }}</p>
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
                                <label class="form-label">{{ __('dashboard.received_from') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" id="addReceivedFrom" class="form-control" placeholder="{{ __('dashboard.enter_received_from') }}" required>
                                    <input type="hidden" id="addGuestId">
                                    <input type="hidden" id="addCorporateId">
                                    <button class="btn btn-outline-secondary" type="button" onclick="openGuestSearchModal()" title="{{ __('dashboard.guest') }}">
                                        <i class="fas fa-user"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" type="button" onclick="openCorporateSearchModal()" title="{{ __('dashboard.corporate') }}">
                                        <i class="fas fa-building"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.purpose') }} <span class="text-danger">*</span></label>
                                <input type="text" id="addPurpose" class="form-control" placeholder="{{ __('dashboard.enter_purpose') }}" required>
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
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" id="addAmount" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                                    <span class="input-group-text">SAR</span>
                                </div>
                            </div>
                        </div>

                        <!-- Mada Conditional Fields -->
                        <div id="madaFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.receiving_bank') }} <span class="text-danger">*</span></label>
                                    <select id="addReceivingBank" class="form-select">
                                        <option value="">{{ __('dashboard.select_bank') }}</option>
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.transaction_number') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="addTransactionNumber" class="form-control" placeholder="{{ __('dashboard.enter_transaction_number') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Cheque Conditional Fields -->
                        <div id="chequeFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('dashboard.receiving_bank') }} <span class="text-danger">*</span></label>
                                    <select id="addChequeReceivingBank" class="form-select">
                                        <option value="">{{ __('dashboard.select_bank') }}</option>
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('dashboard.sending_bank_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="addSendingBankName" class="form-control" placeholder="{{ __('dashboard.enter_sending_bank_name') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('dashboard.cheque_number') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="addChequeNumber" class="form-control" placeholder="{{ __('dashboard.enter_cheque_number') }}">
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

    <!-- Edit Voucher Modal -->
    <div class="modal fade" id="editVoucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <div>
                        <h5 class="modal-title">{{ __('dashboard.edit_receive_money_voucher') }}</h5>
                        <p class="mb-0 small text-white-50">{{ __('dashboard.edit_receive_money_voucher_desc') }}</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editVoucherForm">
                    <div class="modal-body">
                        <input type="hidden" id="editVoucherId">

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
                                    <small class="text-muted">{{ __('dashboard.guest_name') }}</small>
                                    <div class="fw-bold" id="editGuestNameDisplay">-</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.date') }} <span class="text-danger">*</span></label>
                                <input type="date" id="editDate" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.time') }} <span class="text-danger">*</span></label>
                                <input type="time" id="editTime" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.received_from') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" id="editReceivedFrom" class="form-control" placeholder="{{ __('dashboard.enter_received_from') }}" required>
                                    <input type="hidden" id="editGuestId">
                                    <input type="hidden" id="editCorporateId">
                                    <button class="btn btn-outline-secondary" type="button" onclick="openEditGuestSearchModal()" title="{{ __('dashboard.guest') }}">
                                        <i class="fas fa-user"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" type="button" onclick="openEditCorporateSearchModal()" title="{{ __('dashboard.corporate') }}">
                                        <i class="fas fa-building"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.purpose') }} <span class="text-danger">*</span></label>
                                <input type="text" id="editPurpose" class="form-control" placeholder="{{ __('dashboard.enter_purpose') }}" required>
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
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" id="editAmount" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                                    <span class="input-group-text">SAR</span>
                                </div>
                            </div>
                        </div>

                        <!-- Mada Conditional Fields -->
                        <div id="editMadaFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.receiving_bank') }} <span class="text-danger">*</span></label>
                                    <select id="editReceivingBank" class="form-select">
                                        <option value="">{{ __('dashboard.select_bank') }}</option>
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.transaction_number') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="editTransactionNumber" class="form-control" placeholder="{{ __('dashboard.enter_transaction_number') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Cheque Conditional Fields -->
                        <div id="editChequeFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('dashboard.receiving_bank') }} <span class="text-danger">*</span></label>
                                    <select id="editChequeReceivingBank" class="form-select">
                                        <option value="">{{ __('dashboard.select_bank') }}</option>
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('dashboard.sending_bank_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="editSendingBankName" class="form-control" placeholder="{{ __('dashboard.enter_sending_bank_name') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('dashboard.cheque_number') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="editChequeNumber" class="form-control" placeholder="{{ __('dashboard.enter_cheque_number') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('dashboard.comment') }}</label>
                                <textarea id="editComment" class="form-control" rows="3" placeholder="{{ __('dashboard.enter_comment') }}"></textarea>
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
                        <button type="button" class="btn btn-primary" onclick="printPage()">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="printIframe" src="" style="width: 100%; height: 70vh; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Guest Search Modal -->
    <div class="modal fade" id="guestSearchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.search_guest') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" id="guestSearchInput" class="form-control" placeholder="{{ __('dashboard.search_by_name_phone_email') }}">
                            <button class="btn btn-primary" type="button" onclick="searchGuests()">
                                <i class="fas fa-search"></i> {{ __('dashboard.search') }}
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('dashboard.name') }}</th>
                                    <th>{{ __('dashboard.phone') }}</th>
                                    <th>{{ __('dashboard.email') }}</th>
                                    <th>{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="guestSearchResults">
                                @foreach($guests as $guest)
                                <tr>
                                    <td>{{ $guest->first_name }} {{ $guest->last_name }}</td>
                                    <td>{{ $guest->mobile_number ?? '-' }}</td>
                                    <td>{{ $guest->email ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="selectGuest({{ $guest->id }}, '{{ $guest->first_name }} {{ $guest->last_name }}')">
                                            <i class="fas fa-check"></i> {{ __('dashboard.select') }}
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="openAddGuestModal()">
                        <i class="fas fa-plus"></i> {{ __('dashboard.add_new_guest') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Corporate Search Modal -->
    <div class="modal fade" id="corporateSearchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.search_corporate') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" id="corporateSearchInput" class="form-control" placeholder="{{ __('dashboard.search_by_name_phone_email') }}">
                            <button class="btn btn-primary" type="button" onclick="searchCorporates()">
                                <i class="fas fa-search"></i> {{ __('dashboard.search') }}
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('dashboard.corporate_name') }}</th>
                                    <th>{{ __('dashboard.phone') }}</th>
                                    <th>{{ __('dashboard.email') }}</th>
                                    <th>{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="corporateSearchResults">
                                @foreach($corporates as $corporate)
                                <tr>
                                    <td>{{ $corporate->name }}</td>
                                    <td>{{ $corporate->phone ?? '-' }}</td>
                                    <td>{{ $corporate->email ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="selectCorporate({{ $corporate->id }}, '{{ $corporate->name }}')">
                                            <i class="fas fa-check"></i> {{ __('dashboard.select') }}
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="openAddCorporateModal()">
                        <i class="fas fa-plus"></i> {{ __('dashboard.add_new_corporate') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Guest Modal -->
    <div class="modal fade" id="addGuestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.add_new_guest') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addGuestForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.first_name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="guestFirstName" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.last_name') }}</label>
                                <input type="text" id="guestLastName" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.phone') }}</label>
                                <input type="text" id="guestPhone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.email') }}</label>
                                <input type="email" id="guestEmail" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.id_number') }}</label>
                                <input type="text" id="guestIdNumber" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.nationality') }}</label>
                                <input type="text" id="guestNationality" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('dashboard.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Corporate Modal -->
    <div class="modal fade" id="addCorporateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.add_new_corporate') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCorporateForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.corporate_name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="corporateName" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.phone') }}</label>
                                <input type="text" id="corporatePhone" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.email') }}</label>
                                <input type="email" id="corporateEmail" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.contact_person') }}</label>
                                <input type="text" id="corporateContactPerson" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('dashboard.address') }}</label>
                                <textarea id="corporateAddress" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('dashboard.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Guest Search Modal -->
    <div class="modal fade" id="editGuestSearchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.search_guest') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" id="editGuestSearchInput" class="form-control" placeholder="{{ __('dashboard.search_by_name_phone_email') }}">
                            <button class="btn btn-primary" type="button" onclick="searchEditGuests()">
                                <i class="fas fa-search"></i> {{ __('dashboard.search') }}
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('dashboard.name') }}</th>
                                    <th>{{ __('dashboard.phone') }}</th>
                                    <th>{{ __('dashboard.email') }}</th>
                                    <th>{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="editGuestSearchResults">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Corporate Search Modal -->
    <div class="modal fade" id="editCorporateSearchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.search_corporate') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" id="editCorporateSearchInput" class="form-control" placeholder="{{ __('dashboard.search_by_name_phone_email') }}">
                            <button class="btn btn-primary" type="button" onclick="searchEditCorporates()">
                                <i class="fas fa-search"></i> {{ __('dashboard.search') }}
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('dashboard.corporate_name') }}</th>
                                    <th>{{ __('dashboard.phone') }}</th>
                                    <th>{{ __('dashboard.email') }}</th>
                                    <th>{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="editCorporateSearchResults">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const receiptRoutes = {
            index: @json(route('dashboard.receipt.index')),
            store: @json(route('dashboard.receipt.store')),
            searchGuests: @json(route('dashboard.receipt.searchGuests')),
            searchCorporates: @json(route('dashboard.receipt.searchCorporates')),
        };

        function receiptUrl(path = '') {
            return `${receiptRoutes.index}${path}`;
        }

        async function parseJsonResponse(response) {
            const payload = await response.text();
            let data = null;

            try {
                data = payload ? JSON.parse(payload) : {};
            } catch (error) {
                throw new Error('The server returned an unexpected response.');
            }

            if (!response.ok) {
                throw new Error(data.message || 'Request failed.');
            }

            return data;
        }

        function showToast(message, isError = false) {
            const bgClass = isError ? 'alert-danger' : 'alert-success';
            let toast = `
        <div class="alert ${bgClass} alert-dismissible fade show position-fixed"
             style="top:20px; right:20px; z-index:9999;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

            document.body.insertAdjacentHTML('beforeend', toast);

            setTimeout(function() {
                document.querySelector('.alert').remove();
            }, 3000);
        }

        const toggleBtn = document.getElementById('toggleFilterBtn');
        const filterContainer = document.querySelector('.filter-form__container');

        toggleBtn.addEventListener('click', function() {
            if (filterContainer.style.display === 'none') {
                filterContainer.style.display = 'block';
            } else {
                filterContainer.style.display = 'none';
            }
        });

        function printVoucher(id) {
            const printUrl = receiptUrl(`/${id}/print`);
            document.getElementById('printIframe').src = printUrl;
            var modal = new bootstrap.Modal(document.getElementById('printModal'));
            modal.show();

            document.getElementById('printIframe').onload = function() {
                setTimeout(function() {
                    switchPrintLang('en');
                }, 500);
            };
        }

        function switchPrintLang(lang) {
            const iframe = document.getElementById('printIframe');
            try {
                if (iframe && iframe.contentWindow && typeof iframe.contentWindow.switchLanguage === 'function') {
                    iframe.contentWindow.switchLanguage(lang);
                } else {
                    setTimeout(function() {
                        if (iframe.contentWindow && typeof iframe.contentWindow.switchLanguage === 'function') {
                            iframe.contentWindow.switchLanguage(lang);
                        }
                    }, 500);
                }
            } catch(e) {
            }
        }

        function printPage() {
            const iframe = document.querySelector('#printIframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.print();
            }
        }

        function editVoucher(id) {
            fetch(receiptUrl(`/${id}`), {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(parseJsonResponse)
                .then(data => {
                    const voucher = data.voucher;
                    document.getElementById('editVoucherId').value = voucher.id;
                    document.getElementById('editVoucherNumberDisplay').textContent = voucher.voucher_number || '-';
                    document.getElementById('editReservationNumberDisplay').textContent = voucher.reservation?.reservation_number || '-';
                    document.getElementById('editGuestNameDisplay').textContent = voucher.received_from_name || '-';
                    document.getElementById('editDate').value = voucher.date || '';
                    document.getElementById('editTime').value = voucher.time ? voucher.time.substring(0, 5) : '';
                    document.getElementById('editReceivedFrom').value = voucher.received_from_name || '';
                    document.getElementById('editGuestId').value = voucher.guest_id || '';
                    document.getElementById('editCorporateId').value = voucher.corporate_id || '';
                    document.getElementById('editAmount').value = voucher.amount;
                    document.getElementById('editPaymentMethod').value = voucher.payment_method_id || '';
                    document.getElementById('editPurpose').value = voucher.purpose || '';
                    document.getElementById('editComment').value = voucher.comment || '';

                    // Populate conditional payment fields
                    document.getElementById('editReceivingBank').value = voucher.receiving_bank_id || '';
                    document.getElementById('editTransactionNumber').value = voucher.transaction_number || '';
                    document.getElementById('editChequeReceivingBank').value = voucher.receiving_bank_id || '';
                    document.getElementById('editSendingBankName').value = voucher.sending_bank_name || '';
                    document.getElementById('editChequeNumber').value = voucher.cheque_number || '';

                    toggleEditPaymentFields();

                    var modal = new bootstrap.Modal(document.getElementById('editVoucherModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message || 'Error loading voucher', true);
                });
        }

        function toggleEditPaymentFields() {
            const paymentMethodSelect = document.getElementById('editPaymentMethod');
            const selectedOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
            const paymentMethodName = selectedOption?.getAttribute('data-name')?.toLowerCase() || '';

            const madaFields = document.getElementById('editMadaFields');
            const chequeFields = document.getElementById('editChequeFields');

            madaFields.style.display = 'none';
            chequeFields.style.display = 'none';

            if (paymentMethodName.includes('mada') || paymentMethodName.includes('مدى')) {
                madaFields.style.display = 'block';
            } else if (paymentMethodName.includes('cheque') || paymentMethodName.includes('شيك')) {
                chequeFields.style.display = 'block';
            }
        }

        document.getElementById('editVoucherForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('editVoucherId').value;

            const paymentMethodSelect = document.getElementById('editPaymentMethod');
            const selectedOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
            const paymentMethodName = selectedOption?.getAttribute('data-name')?.toLowerCase() || '';

            let receivingBankId = null;
            let transactionNumber = null;
            let sendingBankName = null;
            let chequeNumber = null;

            if (paymentMethodName.includes('mada') || paymentMethodName.includes('مدى')) {
                receivingBankId = document.getElementById('editReceivingBank').value;
                transactionNumber = document.getElementById('editTransactionNumber').value;
            } else if (paymentMethodName.includes('cheque') || paymentMethodName.includes('شيك')) {
                receivingBankId = document.getElementById('editChequeReceivingBank').value;
                sendingBankName = document.getElementById('editSendingBankName').value;
                chequeNumber = document.getElementById('editChequeNumber').value;
            }

            const formData = {
                date: document.getElementById('editDate').value,
                time: document.getElementById('editTime').value,
                received_from_name: document.getElementById('editReceivedFrom').value,
                purpose: document.getElementById('editPurpose').value,
                payment_method_id: document.getElementById('editPaymentMethod').value,
                amount: document.getElementById('editAmount').value,
                comment: document.getElementById('editComment').value,
                guest_id: document.getElementById('editGuestId').value || null,
                corporate_id: document.getElementById('editCorporateId').value || null,
                receiving_bank_id: receivingBankId || null,
                transaction_number: transactionNumber || null,
                sending_bank_name: sendingBankName || null,
                cheque_number: chequeNumber || null,
            };

            fetch(receiptUrl(`/${id}`), {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(parseJsonResponse)
            .then(data => {
                if (data.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('editVoucherModal'));
                    modal.hide();
                    showToast('Voucher updated successfully!');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Error updating voucher', true);
            });
        });

        function cancelVoucher(id) {
            document.getElementById('cancelVoucherId').value = id;
            var modal = new bootstrap.Modal(document.getElementById('cancelVoucherModal'));
            modal.show();
        }

        document.getElementById('cancelVoucherForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('cancelVoucherId').value;
            const cancelReason = document.getElementById('cancelReason').value;

            fetch(receiptUrl(`/${id}/cancel`), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ cancel_reason: cancelReason })
            })
            .then(parseJsonResponse)
            .then(data => {
                if (data.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('cancelVoucherModal'));
                    modal.hide();
                    showToast('Voucher cancelled successfully!');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Error cancelling voucher', true);
            });
        });

        function openAddModal() {
            document.getElementById('addVoucherForm').reset();
            document.getElementById('addDate').value = new Date().toISOString().split('T')[0];
            document.getElementById('addTime').value = new Date().toTimeString().slice(0, 5);
            document.getElementById('addGuestId').value = '';
            document.getElementById('addCorporateId').value = '';
            togglePaymentFields();
            var modal = new bootstrap.Modal(document.getElementById('addVoucherModal'));
            modal.show();
        }

        function togglePaymentFields() {
            const paymentMethodSelect = document.getElementById('addPaymentMethod');
            const selectedOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
            const paymentMethodName = selectedOption.getAttribute('data-name') ? selectedOption.getAttribute('data-name').toLowerCase() : '';

            const madaFields = document.getElementById('madaFields');
            const chequeFields = document.getElementById('chequeFields');

            madaFields.style.display = 'none';
            chequeFields.style.display = 'none';

            if (paymentMethodName.includes('mada') || paymentMethodName.includes('مدى')) {
                madaFields.style.display = 'block';
            } else if (paymentMethodName.includes('cheque') || paymentMethodName.includes('شيك')) {
                chequeFields.style.display = 'block';
            }
        }

        document.getElementById('addVoucherForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const paymentMethodSelect = document.getElementById('addPaymentMethod');
            const selectedOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
            const paymentMethodName = selectedOption.getAttribute('data-name') ? selectedOption.getAttribute('data-name').toLowerCase() : '';

            let receivingBankId = null;
            let transactionNumber = null;
            let sendingBankName = null;
            let chequeNumber = null;

            if (paymentMethodName.includes('mada') || paymentMethodName.includes('مدى')) {
                receivingBankId = document.getElementById('addReceivingBank').value;
                transactionNumber = document.getElementById('addTransactionNumber').value;
            } else if (paymentMethodName.includes('cheque') || paymentMethodName.includes('شيك')) {
                receivingBankId = document.getElementById('addChequeReceivingBank').value;
                sendingBankName = document.getElementById('addSendingBankName').value;
                chequeNumber = document.getElementById('addChequeNumber').value;
            }

            const formData = {
                date: document.getElementById('addDate').value,
                time: document.getElementById('addTime').value,
                received_from_name: document.getElementById('addReceivedFrom').value,
                purpose: document.getElementById('addPurpose').value,
                payment_method_id: document.getElementById('addPaymentMethod').value,
                amount: document.getElementById('addAmount').value,
                comment: document.getElementById('addComment').value,
                guest_id: document.getElementById('addGuestId').value || null,
                corporate_id: document.getElementById('addCorporateId').value || null,
                receiving_bank_id: receivingBankId || null,
                transaction_number: transactionNumber || null,
                sending_bank_name: sendingBankName || null,
                cheque_number: chequeNumber || null,
            };

            fetch(receiptRoutes.store, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(parseJsonResponse)
            .then(data => {
                if (data.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('addVoucherModal'));
                    modal.hide();
                    showToast('Voucher created successfully!');
                    location.reload();
                } else {
                    showToast(data.message || 'Error creating voucher', true);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error creating voucher', true);
            });
        });

        // Guest Search Functions
        function openGuestSearchModal() {
            document.getElementById('guestSearchInput').value = '';
            var modal = new bootstrap.Modal(document.getElementById('guestSearchModal'));
            modal.show();
        }

        function searchGuests() {
            const search = document.getElementById('guestSearchInput').value;
            fetch(`${receiptRoutes.searchGuests}?q=${encodeURIComponent(search)}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(parseJsonResponse)
                .then(guests => {
                    const tbody = document.getElementById('guestSearchResults');
                    if (guests.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">{{ __("dashboard.no_records_found") }}</td></tr>';
                    } else {
                        tbody.innerHTML = guests.map(guest => `
                            <tr>
                                <td>${guest.first_name} ${guest.last_name || ''}</td>
                                <td>${guest.mobile_number || '-'}</td>
                                <td>${guest.email || '-'}</td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="selectGuest(${guest.id}, '${guest.first_name} ${guest.last_name || ''}')">
                                        <i class="fas fa-check"></i> {{ __('dashboard.select') }}
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                    }
                });
        }

        function selectGuest(id, name) {
            document.getElementById('addReceivedFrom').value = name;
            document.getElementById('addGuestId').value = id;
            document.getElementById('addCorporateId').value = '';
            var modal = bootstrap.Modal.getInstance(document.getElementById('guestSearchModal'));
            modal.hide();
        }

        function openAddGuestModal() {
            var modal = bootstrap.Modal.getInstance(document.getElementById('guestSearchModal'));
            modal.hide();
            document.getElementById('addGuestForm').reset();
            var addModal = new bootstrap.Modal(document.getElementById('addGuestModal'));
            addModal.show();
        }

        document.getElementById('addGuestForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = {
                first_name: document.getElementById('guestFirstName').value,
                last_name: document.getElementById('guestLastName').value,
                phone: document.getElementById('guestPhone').value,
                email: document.getElementById('guestEmail').value,
                id_number: document.getElementById('guestIdNumber').value,
                nationality: document.getElementById('guestNationality').value,
            };

            fetch('{{ route("dashboard.guest.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || data.id) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('addGuestModal'));
                    modal.hide();
                    const name = formData.first_name + ' ' + formData.last_name;
                    selectGuest(data.id || data.guest?.id, name);
                } else {
                    showToast(data.message || 'Error adding guest', true);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error adding guest', true);
            });
        });

        // Corporate Search Functions
        function openCorporateSearchModal() {
            document.getElementById('corporateSearchInput').value = '';
            var modal = new bootstrap.Modal(document.getElementById('corporateSearchModal'));
            modal.show();
        }

        function searchCorporates() {
            const search = document.getElementById('corporateSearchInput').value;
            fetch(`${receiptRoutes.searchCorporates}?q=${encodeURIComponent(search)}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(parseJsonResponse)
                .then(corporates => {
                    const tbody = document.getElementById('corporateSearchResults');
                    if (corporates.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">{{ __("dashboard.no_records_found") }}</td></tr>';
                    } else {
                        tbody.innerHTML = corporates.map(corporate => `
                            <tr>
                                <td>${corporate.name}</td>
                                <td>${corporate.phone || '-'}</td>
                                <td>${corporate.email || '-'}</td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="selectCorporate(${corporate.id}, '${corporate.name}')">
                                        <i class="fas fa-check"></i> {{ __('dashboard.select') }}
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                    }
                });
        }

        function selectCorporate(id, name) {
            document.getElementById('addReceivedFrom').value = name;
            document.getElementById('addCorporateId').value = id;
            document.getElementById('addGuestId').value = '';
            var modal = bootstrap.Modal.getInstance(document.getElementById('corporateSearchModal'));
            modal.hide();
        }

        function openAddCorporateModal() {
            var modal = bootstrap.Modal.getInstance(document.getElementById('corporateSearchModal'));
            modal.hide();
            document.getElementById('addCorporateForm').reset();
            var addModal = new bootstrap.Modal(document.getElementById('addCorporateModal'));
            addModal.show();
        }

        document.getElementById('addCorporateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = {
                name: document.getElementById('corporateName').value,
                phone: document.getElementById('corporatePhone').value,
                email: document.getElementById('corporateEmail').value,
                contact_person: document.getElementById('corporateContactPerson').value,
                address: document.getElementById('corporateAddress').value,
            };

            fetch('{{ route("dashboard.corporate.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || data.id) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('addCorporateModal'));
                    modal.hide();
                    selectCorporate(data.id || data.corporate?.id, formData.name);
                } else {
                    showToast(data.message || 'Error adding corporate', true);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error adding corporate', true);
            });
        });

        // Enter key search
        document.getElementById('guestSearchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchGuests();
            }
        });

        document.getElementById('corporateSearchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchCorporates();
            }
        });

        // Edit Modal Guest/Corporate Search Functions
        function openEditGuestSearchModal() {
            document.getElementById('editGuestSearchInput').value = '';
            var modal = new bootstrap.Modal(document.getElementById('editGuestSearchModal'));
            modal.show();
        }

        function searchEditGuests() {
            const search = document.getElementById('editGuestSearchInput').value;
            fetch(`${receiptRoutes.searchGuests}?q=${encodeURIComponent(search)}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(parseJsonResponse)
                .then(guests => {
                    const tbody = document.getElementById('editGuestSearchResults');
                    if (guests.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">{{ __("dashboard.no_records_found") }}</td></tr>';
                    } else {
                        tbody.innerHTML = guests.map(guest => `
                            <tr>
                                <td>${guest.first_name} ${guest.last_name || ''}</td>
                                <td>${guest.mobile_number || '-'}</td>
                                <td>${guest.email || '-'}</td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="selectEditGuest(${guest.id}, '${guest.first_name} ${guest.last_name || ''}')">
                                        <i class="fas fa-check"></i> {{ __('dashboard.select') }}
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                    }
                });
        }

        function selectEditGuest(id, name) {
            document.getElementById('editReceivedFrom').value = name;
            document.getElementById('editGuestId').value = id;
            document.getElementById('editCorporateId').value = '';
            document.getElementById('editGuestNameDisplay').textContent = name;
            var modal = bootstrap.Modal.getInstance(document.getElementById('editGuestSearchModal'));
            modal.hide();
        }

        function openEditCorporateSearchModal() {
            document.getElementById('editCorporateSearchInput').value = '';
            var modal = new bootstrap.Modal(document.getElementById('editCorporateSearchModal'));
            modal.show();
        }

        function searchEditCorporates() {
            const search = document.getElementById('editCorporateSearchInput').value;
            fetch(`${receiptRoutes.searchCorporates}?q=${encodeURIComponent(search)}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(parseJsonResponse)
                .then(corporates => {
                    const tbody = document.getElementById('editCorporateSearchResults');
                    if (corporates.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">{{ __("dashboard.no_records_found") }}</td></tr>';
                    } else {
                        tbody.innerHTML = corporates.map(corporate => `
                            <tr>
                                <td>${corporate.name}</td>
                                <td>${corporate.phone || '-'}</td>
                                <td>${corporate.email || '-'}</td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="selectEditCorporate(${corporate.id}, '${corporate.name}')">
                                        <i class="fas fa-check"></i> {{ __('dashboard.select') }}
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                    }
                });
        }

        function selectEditCorporate(id, name) {
            document.getElementById('editReceivedFrom').value = name;
            document.getElementById('editCorporateId').value = id;
            document.getElementById('editGuestId').value = '';
            document.getElementById('editGuestNameDisplay').textContent = name;
            var modal = bootstrap.Modal.getInstance(document.getElementById('editCorporateSearchModal'));
            modal.hide();
        }

        document.getElementById('editGuestSearchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchEditGuests();
            }
        });

        document.getElementById('editCorporateSearchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchEditCorporates();
            }
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

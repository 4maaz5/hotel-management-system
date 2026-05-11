@extends('layouts.app')

@section('title', __('dashboard.promissory_notes'))
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
    .n-button--green { background-color: #2335da; color: white; border-color: #190cd8; }
    .n-button--green:hover { background-color: #3759f1; border-color: #292ce9; }
    .filter-form__container { background-color: #343a40; border-radius: 0.5rem; margin-bottom: 1.5rem; overflow: hidden; }
    .filter-form { padding: 1.5rem; }
    .filter-form--dark label { color: #e9ecef; font-weight: 500; margin-bottom: 0.5rem; display: block; font-size: 0.875rem; }
    .filter-form--dark .form-control { background-color: #495057; border: 1px solid #6c757d; color: white; width: 100%; padding: 0.5rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; }
    .filter-form--dark .form-select { background-color: #495057; border: 1px solid #6c757d; color: white; }
    .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-partial { background: #cce5ff; color: #004085; }
    .status-collected { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
</style>
@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">
        <div class="page-category">{{ __('dashboard.vouchers') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.promissory_notes') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" id="toggleFilterBtn">{{ __('dashboard.filter') }}</button>
                @can('promissory_note.add')
                      <button class="n-button n-button--green" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> {{ __('dashboard.add_promissory_note') }}
                </button>
                @endcan

            </div>
        </div>

        <form method="GET" action="{{ route('dashboard.promissory.index') }}">
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
                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('dashboard.pending') }}</option>
                                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>{{ __('dashboard.partial') }}</option>
                                    <option value="collected" {{ request('status') === 'collected' ? 'selected' : '' }}>{{ __('dashboard.collected') }}</option>
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
                                <a href="{{ route('dashboard.promissory.index') }}" class="btn btn-outline-secondary">{{ __('dashboard.reset') }}</a>
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
                            <th class="text-nowrap">{{ __('dashboard.voucher_number') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.total_amount') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.collected_amount') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.remaining') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.guest') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.reservation_no') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.maturity_date') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.payment_method') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.date_time') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.status') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $voucher)
                        <tr>

                            <td>{{ $voucher->voucher_number }}</td>
                            <td>{{ number_format($voucher->amount, 2) }} SAR</td>
                            <td>{{ number_format($voucher->collected_amount, 2) }} SAR</td>
                            <td>{{ number_format($voucher->amount - $voucher->collected_amount, 2) }} SAR</td>
                            <td>{{ $voucher->guest->first_name ?? '' }} {{ $voucher->guest->last_name ?? '' }}</td>
                            <td>{{ $voucher->reservation->reservation_number ?? '-' }}</td>
                            <td>{{ $voucher->maturity_date }}</td>
                            <td>{{ $voucher->paymentMethod->paymentMethod->name ?? $voucher->paymentMethod->name ?? '-' }}</td>
                            <td>{{ $voucher->date }}</td>
                            <td>
                                @php
                                    $statusClass = match($voucher->status) {
                                        'pending' => 'status-pending',
                                        'partial' => 'status-partial',
                                        'collected' => 'status-collected',
                                        'cancelled' => 'status-cancelled',
                                        default => 'status-pending'
                                    };
                                    $statusText = match($voucher->status) {
                                        'pending' => __('dashboard.pending'),
                                        'partial' => __('dashboard.partial'),
                                        'collected' => __('dashboard.collected'),
                                        'cancelled' => __('dashboard.cancelled'),
                                        default => __('dashboard.pending')
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td class="text-center">
                                @if($voucher->status !== 'cancelled')
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-secondary py-1 px-2" data-bs-toggle="dropdown" title="Actions">
                                        <i class="fas fa-ellipsis-v small"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @can('promissory_note.print')
                                                                                    <li><a class="dropdown-item" href="#" onclick="printVoucher({{ $voucher->id }}); return false;"><i class="fas fa-print me-2"></i> {{__('dashboard.print')}}</a></li>

                                        @endcan
                                        @can('promissory_note.link')
                                                                                    <li><a class="dropdown-item" href="#" onclick="linkReservation({{ $voucher->id }}); return false;"><i class="fas fa-link me-2"></i> Link to Reservation</a></li>

                                        @endcan
                                        @if($voucher->status !== 'collected')
                                        @can('promissory_note.collect')
                                                                                    <li><a class="dropdown-item" href="#" onclick="collectVoucher({{ $voucher->id }}); return false;"><i class="fas fa-money-bill-wave me-2"></i> {{__('dashboard.collect')}}</a></li>

                                        @endcan
                                        @endif
                                        @can('promissory_note.edit')
                                                                                    <li><a class="dropdown-item" href="#" onclick="editVoucher({{ $voucher->id }}); return false;"><i class="fas fa-edit me-2"></i> {{__('dashboard.edit')}}</a></li>
                                        @endcan
                                        @can('promissory_note.cancel')
                                        <li><a class="dropdown-item text-danger" href="#" onclick="cancelVoucher({{ $voucher->id }}); return false;"><i class="fas fa-times me-2"></i> Cancel</a></li>
                                    @endcan
                                    </ul>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center">{{ __('dashboard.no_data_found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $vouchers->links() }}
            </div>
        </div>
    </main>

    <!-- Add Voucher Modal -->
    <div class="modal fade" id="addVoucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <div>
                        <h5 class="modal-title">{{ __('dashboard.add_promissory_note') }}</h5>
                        <p class="mb-0 small text-white-50">You can add Promissory Note from here</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addVoucherForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.date') }} <span class="text-danger">*</span></label>
                                <input type="date" id="addDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.time') }} <span class="text-danger">*</span></label>
                                <input type="time" id="addTime" class="form-control" value="{{ date('H:i') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.reserved_to') }} <span class="text-danger">*</span></label>
                                <input type="text" id="addReservedTo" class="form-control" value="{{ $property->property_name_en ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="guest-name-block">
                                    <label class="form-label">{{ __('dashboard.guest') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="addGuestName" class="form-control" placeholder="{{ __('dashboard.select_guest') }}" disabled>
                                        <input type="hidden" id="addGuestId">
                                        <button class="btn btn-outline-secondary" type="button" onclick="openGuestSearchModal()">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.purpose') }} <span class="text-danger">*</span></label>
                                <input type="text" id="addPurpose" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.maturity_place') }} <span class="text-danger">*</span></label>
                                <input type="text" id="addMaturityPlace" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.maturity_date') }} <span class="text-danger">*</span></label>
                                <input type="date" id="addMaturityDate" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.amount') }} <span class="text-danger">*</span></label>
                                <input type="number" id="addAmount" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('dashboard.comment') }}</label>
                                <textarea id="addComment" class="form-control" rows="3" placeholder="Comment" maxlength="500"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('dashboard.discard') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('dashboard.add_promissory_note') }}</button>
                    </div>
                </form>
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
                            <input type="text" id="guestSearchInput" class="form-control" placeholder="{{ __('dashboard.search_by_name_phone') }}">
                            <button class="btn btn-primary" type="button" onclick="searchGuests()">
                                <i class="fas fa-search"></i> {{ __('dashboard.search') }}
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('dashboard.guest_name') }}</th>
                                    <th>{{ __('dashboard.phone') }}</th>
                                    <th>{{ __('dashboard.email') }}</th>
                                    <th>{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="guestSearchResults">
                                @foreach($guests as $guest)
                                <tr>
                                    <td>{{ $guest->first_name }} {{ $guest->last_name }}</td>
                                    <td>{{ $guest->phone ?? '-' }}</td>
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
            </div>
        </div>
    </div>

    <!-- Edit Voucher Modal -->
    <div class="modal fade" id="editVoucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <div>
                        <h5 class="modal-title">{{ __('dashboard.edit_promissory_note') }}</h5>
                        <p class="mb-0 small text-white-50">You can edit Promissory Note from here</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editVoucherForm">
                    <div class="modal-body">
                        <input type="hidden" id="editVoucherId">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted">Voucher No.</small>
                                    <div class="fw-bold" id="editVoucherNumberDisplay">-</div>
                                </div>
                            </div>
                            <div class="col-md-4" id="editReservationDiv" style="display: none;">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted">Res. No.</small>
                                    <div class="fw-bold" id="editReservationNumberDisplay">-</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted">Guest Name</small>
                                    <div class="fw-bold" id="editGuestNameDisplay">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted">Date: Gregorian</small>
                                    <div class="fw-bold" id="editDateDisplay">-</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted">Time</small>
                                    <div class="fw-bold" id="editTimeDisplay">-</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted">Collected Amount</small>
                                    <div class="fw-bold" id="editCollectedAmountDisplay">0</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted">Remaining Amount</small>
                                    <div class="fw-bold" id="editRemainingAmountDisplay">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.reserved_to') }} <span class="text-danger">*</span></label>
                                <input type="text" id="editReservedTo" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.guest') }}</label>
                                <input type="text" id="editGuestName" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.purpose') }} <span class="text-danger">*</span></label>
                                <input type="text" id="editPurpose" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.maturity_place') }} <span class="text-danger">*</span></label>
                                <input type="text" id="editMaturityPlace" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.maturity_date') }} <span class="text-danger">*</span></label>
                                <input type="date" id="editMaturityDate" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.amount') }}: SAR <span class="text-danger">*</span></label>
                                <input type="number" id="editAmount" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('dashboard.comment') }}</label>
                                <textarea id="editComment" class="form-control" rows="3" placeholder="Comment" maxlength="500"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('dashboard.discard') }}</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Collect Voucher Modal -->
    <div class="modal fade" id="collectVoucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.collect_promissory_note') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="collectVoucherForm">
                    <div class="modal-body">
                        <input type="hidden" id="collectVoucherId">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="bg-light p-3 rounded">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">{{ __('dashboard.total_amount') }}</small>
                                            <div class="fw-bold" id="collectTotalAmount">-</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">{{ __('dashboard.already_collected') }}</small>
                                            <div class="fw-bold text-success" id="collectAlreadyCollected">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.collect_amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" id="collectAmount" class="form-control" step="0.01" min="0" required>
                                    <span class="input-group-text">SAR</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('dashboard.payment_method') }} <span class="text-danger">*</span></label>
                                <select id="collectPaymentMethod" class="form-select" required>
                                    <option value="">{{ __('dashboard.select_payment_method') }}</option>
                                    @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}">{{ $method->paymentMethod->name ?? 'N/A' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('dashboard.comment') }}</label>
                                <textarea id="collectComment" class="form-control" rows="2" placeholder="{{ __('dashboard.enter_comment') }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('dashboard.collect') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Voucher Modal -->
    <div class="modal fade" id="cancelVoucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.cancel_promissory_note') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="cancelVoucherForm">
                    <div class="modal-body">
                        <input type="hidden" id="cancelVoucherId">
                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.cancel_reason') }} <span class="text-danger">*</span></label>
                            <textarea id="cancelReason" class="form-control" rows="3" required placeholder="{{ __('dashboard.enter_cancel_reason') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.close') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('dashboard.confirm_cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Link Reservation Modal -->
    <div class="modal fade" id="linkReservationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white;">
                    <h5 class="modal-title">Link to Reservation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="linkReservationForm">
                    <div class="modal-body">
                        <input type="hidden" id="linkVoucherId">
                        <div class="mb-3">
                            <label class="form-label">Search Reservation</label>
                            <input type="text" id="reservationSearchInput" class="form-control" placeholder="Search by reservation number or guest name...">
                        </div>
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Res. No.</th>
                                        <th>Guest Name</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="reservationListBody">
                                    @foreach($allReservations as $res)
                                    <tr>
                                        <td>{{ $res->reservation_number }}</td>
                                        <td>{{ $res->guest->first_name ?? '' }} {{ $res->guest->last_name ?? '' }}</td>
                                        <td>{{ $res->status }}</td>
                                        <td>{{ number_format($res->total_rent, 2) }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success" onclick="selectReservation({{ $res->id }}, '{{ $res->reservation_number }}')">
                                                Select
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <strong>Selected Reservation:</strong>
                            <span id="selectedReservationDisplay" class="text-danger">None</span>
                            <input type="hidden" id="selectedReservationId">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info">Link Reservation</button>
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
    const reservations = @json($reservations);
    const guests = @json($guests);
    const promissoryBaseUrl = @json(route('dashboard.promissory.index'));

    async function parseJsonResponse(response) {
        const payload = await response.text();
        let data = {};

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

    document.getElementById('toggleFilterBtn').addEventListener('click', function() {
        const filterContainer = document.querySelector('.filter-form__container');
        filterContainer.style.display = filterContainer.style.display === 'none' ? 'block' : 'none';
    });

    function showToast(message, isError = false) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white ${isError ? 'bg-danger' : 'bg-success'} border-0 position-fixed top-0 end-0 m-3`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
        document.body.appendChild(toast);
        new bootstrap.Toast(toast).show();
        setTimeout(() => toast.remove(), 5000);
    }

    function togglePaymentFields() {
        const sel = document.getElementById('addPaymentMethod');
        const pname = sel.options[sel.selectedIndex]?.getAttribute('data-name')?.toLowerCase() || '';
        document.getElementById('madaFields').style.display = (pname.includes('mada') || pname.includes('مدى')) ? 'block' : 'none';
        document.getElementById('chequeFields').style.display = (pname.includes('cheque') || pname.includes('شيك')) ? 'block' : 'none';
    }

    function toggleEditPaymentFields() {
        const sel = document.getElementById('editPaymentMethod');
        const pname = sel.options[sel.selectedIndex]?.getAttribute('data-name')?.toLowerCase() || '';
        document.getElementById('editMadaFields').style.display = (pname.includes('mada') || pname.includes('مدى')) ? 'block' : 'none';
        document.getElementById('editChequeFields').style.display = (pname.includes('cheque') || pname.includes('شيك')) ? 'block' : 'none';
    }

    function loadReservations() {
        const guestId = document.getElementById('addGuest').value;
        const reservationSelect = document.getElementById('addReservation');
        reservationSelect.innerHTML = '<option value="">{{ __("dashboard.select_reservation") }}</option>';

        if (guestId) {
            const filtered = reservations.filter(r => r.guest_id == guestId && ['confirmed', 'checked_in'].includes(r.status));
            filtered.forEach(r => {
                const option = document.createElement('option');
                option.value = r.id;
                option.textContent = r.reservation_number;
                reservationSelect.appendChild(option);
            });
        }
    }

    function loadEditReservations() {
    }

    function openAddModal() {
        document.getElementById('addVoucherForm').reset();
        document.getElementById('addDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('addTime').value = new Date().toTimeString().slice(0, 5);
        document.getElementById('addMaturityDate').value = new Date(Date.now() + 30*24*60*60*1000).toISOString().split('T')[0];
        document.getElementById('addGuestId').value = '';
        document.getElementById('addGuestName').value = '';
        document.getElementById('addReservedTo').value = '{{ $property->property_name_en ?? "" }}';
        new bootstrap.Modal(document.getElementById('addVoucherModal')).show();
    }

    function openGuestSearchModal() {
        new bootstrap.Modal(document.getElementById('guestSearchModal')).show();
    }

    function selectGuest(id, name) {
        document.getElementById('addGuestId').value = id;
        document.getElementById('addGuestName').value = name;
        bootstrap.Modal.getInstance(document.getElementById('guestSearchModal')).hide();
    }

    function searchGuests() {
        const query = document.getElementById('guestSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#guestSearchResults tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }

    document.getElementById('addVoucherForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const guestId = document.getElementById('addGuestId').value;
        if (!guestId) {
            showToast('Please select a guest', true);
            return;
        }

        fetch('{{ route("dashboard.promissory.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                guest_id: guestId,
                date: document.getElementById('addDate').value,
                time: document.getElementById('addTime').value,
                maturity_date: document.getElementById('addMaturityDate').value,
                maturity_place: document.getElementById('addMaturityPlace').value,
                reserved_to: document.getElementById('addReservedTo').value,
                purpose: document.getElementById('addPurpose').value,
                amount: document.getElementById('addAmount').value,
                comment: document.getElementById('addComment').value,
            })
        }).then(parseJsonResponse).then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addVoucherModal')).hide();
                showToast(data.message || 'Promissory note created successfully!');
                location.reload();
            } else {
                showToast(data.message || 'Error creating promissory note', true);
            }
        }).catch(err => {
            console.error(err);
            showToast('Error creating promissory note', true);
        });
    });

    function editVoucher(id) {
        fetch(`${promissoryBaseUrl}/${id}`)
            .then(parseJsonResponse)
            .then(data => {
                const v = data.voucher;
                document.getElementById('editVoucherId').value = v.id;
                document.getElementById('editVoucherNumberDisplay').textContent = v.voucher_number || '-';

                if (v.reservation_id) {
                    document.getElementById('editReservationDiv').style.display = 'block';
                    document.getElementById('editReservationNumberDisplay').textContent = v.reservation?.reservation_number || '-';
                } else {
                    document.getElementById('editReservationDiv').style.display = 'none';
                }

                const guestName = v.guest ? v.guest.name : '-';
                document.getElementById('editGuestNameDisplay').textContent = guestName;
                document.getElementById('editGuestName').value = guestName;
                document.getElementById('editDateDisplay').textContent = v.date || '-';
                document.getElementById('editTimeDisplay').textContent = v.time ? v.time.substring(0, 5) : '-';
                document.getElementById('editCollectedAmountDisplay').textContent = parseFloat(v.collected_amount || 0).toFixed(2);
                const remaining = (parseFloat(v.amount || 0) - parseFloat(v.collected_amount || 0)).toFixed(2);
                document.getElementById('editRemainingAmountDisplay').textContent = remaining;

                document.getElementById('editReservedTo').value = v.reserved_to || '';
                document.getElementById('editPurpose').value = v.purpose || '';
                document.getElementById('editMaturityPlace').value = v.maturity_place || '';
                document.getElementById('editMaturityDate').value = v.maturity_date || '';
                document.getElementById('editAmount').value = v.amount;
                document.getElementById('editComment').value = v.comment || '';

                new bootstrap.Modal(document.getElementById('editVoucherModal')).show();
            });
    }

    document.getElementById('editVoucherForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('editVoucherId').value;

        fetch(`${promissoryBaseUrl}/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                reserved_to: document.getElementById('editReservedTo').value,
                purpose: document.getElementById('editPurpose').value,
                maturity_place: document.getElementById('editMaturityPlace').value,
                maturity_date: document.getElementById('editMaturityDate').value,
                amount: document.getElementById('editAmount').value,
                comment: document.getElementById('editComment').value,
            })
        }).then(parseJsonResponse).then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editVoucherModal')).hide();
                showToast(data.message || 'Promissory note updated successfully!');
                location.reload();
            } else {
                showToast(data.message || 'Error updating promissory note', true);
            }
        }).catch(err => {
            console.error(err);
            showToast('Error updating promissory note', true);
        });
    });

    function collectVoucher(id) {
        fetch(`${promissoryBaseUrl}/${id}`)
            .then(parseJsonResponse)
            .then(data => {
                const v = data.voucher;
                document.getElementById('collectVoucherId').value = v.id;
                document.getElementById('collectTotalAmount').textContent = parseFloat(v.amount).toFixed(2) + ' SAR';
                document.getElementById('collectAlreadyCollected').textContent = parseFloat(v.collected_amount).toFixed(2) + ' SAR';
                document.getElementById('collectAmount').value = (v.amount - v.collected_amount).toFixed(2);
                document.getElementById('collectComment').value = '';
                document.getElementById('collectPaymentMethod').value = '';
                new bootstrap.Modal(document.getElementById('collectVoucherModal')).show();
            });
    }

    document.getElementById('collectVoucherForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('collectVoucherId').value;

        fetch(`${promissoryBaseUrl}/${id}/collect`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                amount: document.getElementById('collectAmount').value,
                payment_method_id: document.getElementById('collectPaymentMethod').value,
                comment: document.getElementById('collectComment').value,
            })
        }).then(parseJsonResponse).then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('collectVoucherModal')).hide();
                showToast(data.message || 'Amount collected successfully!');
                location.reload();
            } else {
                showToast(data.message || 'Error collecting amount', true);
            }
        }).catch(err => { console.error(err); showToast('Error collecting amount', true); });
    });

    function cancelVoucher(id) {
        document.getElementById('cancelVoucherId').value = id;
        document.getElementById('cancelReason').value = '';
        new bootstrap.Modal(document.getElementById('cancelVoucherModal')).show();
    }

    document.getElementById('cancelVoucherForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('cancelVoucherId').value;

        fetch(`${promissoryBaseUrl}/${id}/cancel`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                cancel_reason: document.getElementById('cancelReason').value,
            })
        }).then(parseJsonResponse).then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('cancelVoucherModal')).hide();
                showToast(data.message || 'Promissory note cancelled successfully!');
                location.reload();
            } else {
                showToast(data.message || 'Error cancelling promissory note', true);
            }
        }).catch(err => { console.error(err); showToast('Error cancelling promissory note', true); });
    });

    const allReservations = @json($allReservations);

    function linkReservation(id) {
        document.getElementById('linkVoucherId').value = id;
        document.getElementById('selectedReservationId').value = '';
        document.getElementById('selectedReservationDisplay').textContent = 'None';
        document.getElementById('reservationSearchInput').value = '';
        showAllReservations();
        new bootstrap.Modal(document.getElementById('linkReservationModal')).show();
    }

    function showAllReservations() {
        const tbody = document.getElementById('reservationListBody');
        tbody.innerHTML = '';
        allReservations.forEach(res => {
            const guestName = res.guest ? res.guest.first_name + ' ' + res.guest.last_name : '-';
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${res.reservation_number}</td>
                <td>${guestName}</td>
                <td>${res.status}</td>
                <td>${parseFloat(res.total_rent || 0).toFixed(2)}</td>
                <td><button type="button" class="btn btn-sm btn-success" onclick="selectReservation(${res.id}, '${res.reservation_number}')">Select</button></td>
            `;
            tbody.appendChild(row);
        });
    }

    document.getElementById('reservationSearchInput').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const tbody = document.getElementById('reservationListBody');
        tbody.innerHTML = '';
        allReservations.forEach(res => {
            const guestName = res.guest ? (res.guest.first_name + ' ' + res.guest.last_name).toLowerCase() : '';
            const resNum = res.reservation_number ? res.reservation_number.toLowerCase() : '';
            if (guestName.includes(query) || resNum.includes(query)) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${res.reservation_number}</td>
                    <td>${res.guest ? res.guest.first_name + ' ' + res.guest.last_name : '-'}</td>
                    <td>${res.status}</td>
                    <td>${parseFloat(res.total_rent || 0).toFixed(2)}</td>
                    <td><button type="button" class="btn btn-sm btn-success" onclick="selectReservation(${res.id}, '${res.reservation_number}')">Select</button></td>
                `;
                tbody.appendChild(row);
            }
        });
    });

    function selectReservation(id, number) {
        document.getElementById('selectedReservationId').value = id;
        document.getElementById('selectedReservationDisplay').textContent = number;
    }

    document.getElementById('linkReservationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('linkVoucherId').value;
        const reservationId = document.getElementById('selectedReservationId').value;

        if (!reservationId) {
            showToast('Please select a reservation', true);
            return;
        }

        fetch(`${promissoryBaseUrl}/${id}/link-reservation`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                reservation_id: reservationId,
            })
        }).then(parseJsonResponse).then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('linkReservationModal')).hide();
                showToast('Reservation linked successfully!');
                location.reload();
            } else {
                showToast(data.message || 'Error linking reservation', true);
            }
        }).catch(err => { console.error(err); showToast('Error linking reservation', true); });
    });

    @if(request()->hasAny(['voucher_number', 'guest_name', 'status', 'date_from', 'date_to']))
    document.querySelector('.filter-form__container').style.display = 'block';
    @endif

    function printVoucher(id) {
        document.getElementById('printIframe').src = `${promissoryBaseUrl}/${id}/print`;
        new bootstrap.Modal(document.getElementById('printModal')).show();
        document.getElementById('printIframe').onload = function() { setTimeout(function() { switchPrintLang('en'); }, 500); };
    }

    function switchPrintLang(lang) {
        const iframe = document.getElementById('printIframe');
        try { if (iframe?.contentWindow?.switchLanguage) iframe.contentWindow.switchLanguage(lang); }
        catch(e) { setTimeout(function() { if (iframe?.contentWindow?.switchLanguage) iframe.contentWindow.switchLanguage(lang); }, 500); }
    }

    function printPage() { document.querySelector('#printIframe')?.contentWindow?.print(); }
        
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

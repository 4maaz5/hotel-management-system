@extends('layouts.app')

@section('title', __('dashboard.drop_cash'))
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
    .modal-header { border-bottom: 2px solid #4a6cf7; }
    .modal-title { color: #4a6cf7; font-weight: 600; }
    .arabic-text { direction: rtl; unicode-bidi: embed; text-align: right; display: none; }
    .english-text { direction: ltr; unicode-bidi: embed; text-align: left; }
    .lang-ar .arabic-text { display: inline; }
    .lang-ar .english-text { display: none; }
</style>
@section('content')
<main class="u-white-bg bg-white p-3" style="border-radius:15px;">
    @foreach (['success', 'danger', 'warning', 'info'] as $msg)
        @if (session($msg))
            <div class="alert m-3 alert-{{ $msg }} alert-dismissible fade show mt-3" role="alert" id="flash-message">
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach

    <div class="page-category">{{ __('dashboard.vouchers') }}</div>
    <div class="page-header">
        <div>
            <h2 class="page-header__title">{{ __('dashboard.drop_cash') }}</h2>
        </div>
        <div class="n-table__top-btns">
            <button class="n-button n-button--primary" id="toggleFilterBtn">
                <i class="fas fa-filter"></i> {{ __('dashboard.filter') }}
            </button>
            @can('drop_cash.add')
                 <button class="n-button n-button--green" onclick="openAddModal()">
                <i class="fas fa-plus"></i> {{ __('dashboard.add_drop_cash') }}
            </button>
            @endcan

        </div>
    </div>

    <form method="GET" action="{{ route('dashboard.drop_cash.index') }}">
        <div class="filter-form__container mb-4" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">{{ __('dashboard.voucher_no') }}</label>
                            <input type="text" name="voucher_number" value="{{ request('voucher_number') }}" class="form-control" placeholder="000001">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">{{ __('dashboard.drop_method') }}</label>
                            <select name="drop_method" class="form-select">
                                <option value="">--</option>
                                @foreach($dropMethods as $key => $method)
                                    <option value="{{ $key }}" {{ request('drop_method') == $key ? 'selected' : '' }}>{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">{{ __('dashboard.amount') }} Min</label>
                            <input type="number" name="amount_min" value="{{ request('amount_min') }}" class="form-control" placeholder="0">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">{{ __('dashboard.amount') }} Max</label>
                            <input type="number" name="amount_max" value="{{ request('amount_max') }}" class="form-control" placeholder="999999">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">{{ __('dashboard.date_time_from') }}</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">{{ __('dashboard.date_time_to') }}</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                        </div>
                        <div class="col-lg-2 col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">{{ __('dashboard.search') }}</button>
                            <a href="{{ route('dashboard.drop_cash.index') }}" class="btn btn-outline-secondary">{{ __('dashboard.reset') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap">{{ __('dashboard.voucher_no') }}</th>
                        <th class="text-nowrap">{{ __('dashboard.amount') }}</th>
                        <th class="text-nowrap">{{ __('dashboard.date_time_from') }}</th>
                        <th class="text-nowrap">{{ __('dashboard.date_time_to') }}</th>
                        <th class="text-nowrap">{{ __('dashboard.drop_method') }}</th>
                        <th class="text-nowrap">{{ __('dashboard.paid_to') }}</th>
                        <th class="text-nowrap">{{ __('dashboard.purpose') }}</th>
                        <th class="text-nowrap">{{ __('dashboard.created_date_time') }}</th>
                        <th class="text-nowrap">{{ __('dashboard.user') }}</th>
                        <th class="text-nowrap">{{ __('dashboard.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                    <tr>
                        <td>{{ $voucher->voucher_number }}</td>
                        <td>{{ number_format($voucher->amount, 2) }} SAR</td>
                        <td>{{ $voucher->date_from ? $voucher->date_from->format('d-m-Y H:i') : '-' }}</td>
                        <td>{{ $voucher->date_to ? $voucher->date_to->format('d-m-Y H:i') : '-' }}</td>
                        <td>
                            @if($voucher->drop_method == 'cash')
                                <span class="badge bg-primary">Cash</span>
                            @elseif($voucher->drop_method == 'bank_transfer')
                                <span class="badge bg-success">Bank Transfer</span>
                            @else
                                <span class="badge bg-secondary">Other</span>
                            @endif
                        </td>
                        <td>{{ $voucher->paid_to }}</td>
                        <td>{{ $voucher->purpose }}</td>
                        <td>{{ $voucher->created_at ? $voucher->created_at->format('d-m-Y H:i') : '-' }}</td>
                        <td>{{ $voucher->user->name ?? '-' }}</td>
                        <td class="text-center">
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-sm btn-secondary py-1 px-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @can('drop_cash.view')
                                        <li><a class="dropdown-item" href="#" onclick="viewVoucher({{ $voucher->id }}); return false;">
                                        <i class="fas fa-eye me-2"></i>{{__('dashboard.view')}}</a></li>
                                    @endcan
                                    @can('drop_cash.print')
                                        <li><a class="dropdown-item" href="#" onclick="printVoucher({{ $voucher->id }}); return false;">
                                        <i class="fas fa-print me-2"></i>{{__('dashboard.print')}}</a></li>
                                    @endcan
                                    @can('drop_cash.edit')
                                        <li><a class="dropdown-item" href="#" onclick="editVoucher({{ $voucher->id }}); return false;">
                                        <i class="fas fa-edit me-2"></i>{{__('dashboard.edit')}}</a></li>
                                    @endcan

                                    <li><hr class="dropdown-divider"></li>
                                    @can('drop_cash.delete')
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteVoucher({{ $voucher->id }}); return false;">
                                        <i class="fas fa-trash me-2"></i>{{__('dashboard.delete')}}</a></li>
                                    @endcan

                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p class="mb-0">{{ __('dashboard.no_data_found') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {{ $vouchers->links() }}
            </div>
        </div>
    </div>
</main>

<!-- Add/Edit Modal -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">{{ __('dashboard.add_drop_cash') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="voucherForm">
                @csrf
                <input type="hidden" id="voucher_id" name="id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('dashboard.date_time_from') }} *</label>
                            <input type="datetime-local" class="form-control" id="date_from" name="date_from" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('dashboard.date_time_to') }} *</label>
                            <input type="datetime-local" class="form-control" id="date_to" name="date_to" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('dashboard.amount') }} *</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.drop_method') }} *</label>
                            <select class="form-select" id="drop_method" name="drop_method" required onchange="toggleBankDropdown()">
                                <option value="">--</option>
                                @foreach($dropMethods as $key => $method)
                                    <option value="{{ $key }}">{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="bank_dropdown" style="display: none;">
                            <label class="form-label">{{ __('dashboard.select_bank') }} *</label>
                            <select class="form-select" id="bank_id" name="bank_id">
                                <option value="">--</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }} ({{ $bank->account_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.paid_to') }} *</label>
                            <input type="text" class="form-control" id="paid_to" name="paid_to" maxlength="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.purpose') }} *</label>
                            <input type="text" class="form-control" id="purpose" name="purpose" maxlength="200" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.comment') }}</label>
                            <input type="text" class="form-control" id="comment" name="comment" maxlength="300">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.discard') }}</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">{{ __('dashboard.save_changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('dashboard.view_drop_cash') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('dashboard.voucher_no') }}</label>
                        <p id="view_voucher_number">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('dashboard.dropped_by') }}</label>
                        <p id="view_dropped_by">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('dashboard.amount') }}</label>
                        <p id="view_amount">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('dashboard.date_time_from') }}</label>
                        <p id="view_date_from">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('dashboard.date_time_to') }}</label>
                        <p id="view_date_to">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('dashboard.drop_method') }}</label>
                        <p id="view_drop_method">-</p>
                    </div>
                    <div class="col-md-4" id="view_bank_section" style="display: none;">
                        <label class="form-label fw-bold">{{ __('dashboard.select_bank') }}</label>
                        <p id="view_bank">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('dashboard.paid_to') }}</label>
                        <p id="view_paid_to">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('dashboard.purpose') }}</label>
                        <p id="view_purpose">-</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">{{ __('dashboard.comment') }}</label>
                        <p id="view_comment">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.close') }}</button>
            </div>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('dashboard.confirm_delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('dashboard.are_you_sure_delete_voucher') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ __('dashboard.delete') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.getElementById('toggleFilterBtn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            var filterContainer = document.querySelector('.filter-form__container');
            if (filterContainer) {
                filterContainer.style.display = filterContainer.style.display === 'none' ? 'block' : 'none';
            }
        });
    }

    // Initialize all dropdowns
    var dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    dropdownTriggerList.map(function (dropdownTriggerEl) {
        return new bootstrap.Dropdown(dropdownTriggerEl);
    });
});

const dropCashBaseUrl = @json(url('/app/dashboard/vouchers-drop'));

function openAddModal() {
    document.getElementById('voucherForm').reset();
    document.getElementById('voucher_id').value = '';
    document.getElementById('modalTitle').textContent = '{{ __("dashboard.add_drop_cash") }}';
    var modal = new bootstrap.Modal(document.getElementById('voucherModal'));
    modal.show();
}

function editVoucher(id) {
    fetch(`${dropCashBaseUrl}/${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const v = data.voucher;
                document.getElementById('voucher_id').value = v.id;
                document.getElementById('date_from').value = v.date_from ? v.date_from.replace(' ', 'T') : '';
                document.getElementById('date_to').value = v.date_to ? v.date_to.replace(' ', 'T') : '';
                document.getElementById('amount').value = v.amount;
                document.getElementById('drop_method').value = v.drop_method;
                document.getElementById('paid_to').value = v.paid_to;
                document.getElementById('purpose').value = v.purpose;
                document.getElementById('comment').value = v.comment || '';

                if (v.drop_method === 'bank_transfer') {
                    document.getElementById('bank_dropdown').style.display = 'block';
                    document.getElementById('bank_id').value = v.bank_id || '';
                } else {
                    document.getElementById('bank_dropdown').style.display = 'none';
                    document.getElementById('bank_id').value = '';
                }

                document.getElementById('modalTitle').textContent = '{{ __("dashboard.edit_drop_cash") }}';
                var modal = new bootstrap.Modal(document.getElementById('voucherModal'));
                modal.show();
            }
        });
}

function toggleBankDropdown() {
    const dropMethod = document.getElementById('drop_method').value;
    const bankDropdown = document.getElementById('bank_dropdown');

    if (dropMethod === 'bank_transfer') {
        bankDropdown.style.display = 'block';
    } else {
        bankDropdown.style.display = 'none';
        document.getElementById('bank_id').value = '';
    }
}

function viewVoucher(id) {
    fetch(`${dropCashBaseUrl}/${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const v = data.voucher;
                document.getElementById('view_voucher_number').textContent = v.voucher_number;
                document.getElementById('view_dropped_by').textContent = v.user ? v.user.name : '-';
                document.getElementById('view_amount').textContent = parseFloat(v.amount).toFixed(2) + ' SAR';
                document.getElementById('view_date_from').textContent = v.date_from || '-';
                document.getElementById('view_date_to').textContent = v.date_to || '-';
                document.getElementById('view_drop_method').textContent = v.drop_method.replace('_', ' ').charAt(0).toUpperCase() + v.drop_method.replace('_', ' ').slice(1);
                document.getElementById('view_paid_to').textContent = v.paid_to;
                document.getElementById('view_purpose').textContent = v.purpose;
                document.getElementById('view_comment').textContent = v.comment || '-';

                if (v.drop_method === 'bank_transfer' && v.bank) {
                    document.getElementById('view_bank_section').style.display = 'block';
                    document.getElementById('view_bank').textContent = v.bank.name;
                } else {
                    document.getElementById('view_bank_section').style.display = 'none';
                }

                var modal = new bootstrap.Modal(document.getElementById('viewModal'));
                modal.show();
            }
        });
}

function printVoucher(id) {
    document.getElementById('printIframe').src = `${dropCashBaseUrl}/${id}/print`;
    new bootstrap.Modal(document.getElementById('printModal')).show();
    document.getElementById('printIframe').onload = function() { setTimeout(function() { switchPrintLang('en'); }, 500); };
}

function switchPrintLang(lang) {
    const iframe = document.getElementById('printIframe');
    try { if (iframe?.contentWindow?.switchLanguage) iframe.contentWindow.switchLanguage(lang); }
    catch(e) { setTimeout(function() { if (iframe?.contentWindow?.switchLanguage) iframe.contentWindow.switchLanguage(lang); }, 500); }
}

function printPage() { document.querySelector('#printIframe')?.contentWindow?.print(); }

document.getElementById('voucherForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const id = document.getElementById('voucher_id').value;
    const url = id ? `${dropCashBaseUrl}/${id}` : dropCashBaseUrl;

    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');
    if (id) {
        formData.append('_method', 'PUT');
    }

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('voucherModal')).hide();
            showToast(data.message || 'Operation successful');
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Error occurred', true);
        }
    });
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

let deleteId = null;

function deleteVoucher(id) {
    deleteId = id;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!deleteId) return;

    fetch(`${dropCashBaseUrl}/${deleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            showToast(data.message || 'Voucher deleted successfully');
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Error occurred', true);
        }
    })
    .finally(function() {
        deleteId = null;
    });
});
</script>
@endpush

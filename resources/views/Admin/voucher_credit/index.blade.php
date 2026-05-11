@extends('layouts.app')

@section('title', __('dashboard.credit_notes'))
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
    .filter-form--dark .form-select { background-color: #495057; border: 1px solid #6c757d; color: white; }
</style>
@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">
        <div class="page-category">{{ __('dashboard.vouchers') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.credit_notes') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" id="toggleFilterBtn">{{ __('dashboard.filter') }}</button>
            </div>
        </div>

        <form method="GET" action="{{ route('dashboard.credit.index') }}">
            <div class="filter-form__container mb-4" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.credit_note_no') }}</label>
                                <input type="text" name="credit_note_number" value="{{ request('credit_note_number') }}" class="form-control" placeholder="{{ __('dashboard.enter_credit_note_no') }}">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.invoice_type') }}</label>
                                <select name="invoice_type" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="B2B" {{ request('invoice_type') === 'B2B' ? 'selected' : '' }}>B2B</option>
                                    <option value="B2C" {{ request('invoice_type') === 'B2C' ? 'selected' : '' }}>B2C</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.guest_name') }}</label>
                                <input type="text" name="guest_name" value="{{ request('guest_name') }}" class="form-control" placeholder="{{ __('dashboard.enter_guest_name') }}">
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
                                <a href="{{ route('dashboard.credit.index') }}" class="btn btn-outline-secondary">{{ __('dashboard.reset') }}</a>
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
                            <th class="text-nowrap">{{ __('dashboard.invoice_type') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.credit_note_no') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.reservation_no_order_no') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.outlet') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.cn_date') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.guest') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.cn_period_from') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.cn_period_to') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.amount') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.invoice_no') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.created_datetime') }}</th>
                            <th class="text-nowrap">{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($creditNotes as $note)
                        <tr>
                            <td>{{ $note->invoice_type }}</td>
                            <td>{{ $note->credit_note_number }}</td>
                            <td>{{ $note->reservation->reservation_number ?? '-' }}</td>
                            <td>{{ $note->outlet->name ?? '-' }}</td>
                            <td>{{ $note->cn_date ? $note->cn_date->format('d-m-Y') : '-' }}</td>
                            <td>{{ $note->guest->first_name ?? '' }} {{ $note->guest->last_name ?? '' }}</td>
                            <td>{{ $note->period_from ? $note->period_from->format('d-m-Y') : '-' }}</td>
                            <td>{{ $note->period_to ? $note->period_to->format('d-m-Y') : '-' }}</td>
                            <td>{{ number_format($note->amount, 2) }} SAR</td>
                            <td>{{ $note->invoice_number ?? '-' }}</td>
                            <td>{{ $note->created_at ? $note->created_at->format('d-m-Y H:i') : '-' }}</td>
                            <td class="text-center">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-secondary py-1 px-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @can('credit_notes.print')
                                                                                    <li><a class="dropdown-item" href="#" onclick="printCreditNote({{ $note->id }}); return false;"><i class="fas fa-print me-2"></i> {{__('dashboard.print')}}</a></li>
                                        @endcan
                                        @can('credit_notes.whatsapp')
                                                                                    <li><a class="dropdown-item" href="#" onclick="sendWhatsApp({{ $note->id }}); return false;"><i class="fab fa-whatsapp me-2"></i> {{__('dashboard.send_via_whatsapp')}}</a></li>
                                        @endcan
                                        @can('credit_notes.sms')
                                                                                    <li><a class="dropdown-item" href="#" onclick="sendSms({{ $note->id }}); return false;"><i class="fas fa-sms me-2"></i> {{__('dashboard.send_via_sms')}}</a></li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center">{{ __('dashboard.no_data_found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $creditNotes->links() }}
            </div>
        </div>
    </main>

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
    var currentCreditNoteId = null;

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

    const creditNoteBaseUrl = @json(url('/app/dashboard/vouchers-credit'));

    function printCreditNote(id) {
        currentCreditNoteId = id;
        document.getElementById('printIframe').src = `${creditNoteBaseUrl}/${id}/print`;
        new bootstrap.Modal(document.getElementById('printModal')).show();
        document.getElementById('printIframe').onload = function() { setTimeout(function() { switchPrintLang('en'); }, 500); };
    }

    function switchPrintLang(lang) {
        const iframe = document.getElementById('printIframe');
        try { if (iframe?.contentWindow?.switchLanguage) iframe.contentWindow.switchLanguage(lang); }
        catch(e) { setTimeout(function() { if (iframe?.contentWindow?.switchLanguage) iframe.contentWindow.switchLanguage(lang); }, 500); }
    }

    function printPage() { document.querySelector('#printIframe')?.contentWindow?.print(); }

    function sendWhatsApp(id) {
        fetch(`${creditNoteBaseUrl}/${id}/whatsapp`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(r => r.json()).then(data => {
            showToast(data.message || 'WhatsApp feature coming soon');
        }).catch(err => { console.error(err); showToast('Error sending WhatsApp', true); });
    }

    function sendSms(id) {
        fetch(`${creditNoteBaseUrl}/${id}/sms`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(r => r.json()).then(data => {
            showToast(data.message || 'SMS feature coming soon');
        }).catch(err => { console.error(err); showToast('Error sending SMS', true); });
    }

    function sendViaWhatsApp() {
        if (currentCreditNoteId) {
            sendWhatsApp(currentCreditNoteId);
        }
    }

    function sendViaSms() {
        if (currentCreditNoteId) {
            sendSms(currentCreditNoteId);
        }
    }

    @if(request()->hasAny(['credit_note_number', 'invoice_type', 'guest_name', 'date_from', 'date_to']))
    document.querySelector('.filter-form__container').style.display = 'block';
    @endif
</script>
@endpush

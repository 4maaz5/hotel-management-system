@extends('layouts.app')

@section('title', 'Vouchers Payment')
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

    /* Page Header */
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

    /* Table Top Buttons */
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

    .n-button--green {
        background-color: #2335da;
        color: white;
        border-color: #190cd8;
    }

    .n-button--green:hover {
        background-color: #3759f1;
        border-color: #292ce9;
    }

    /* Filter Form */
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

    .filter-form--dark .form-control::placeholder {
        color: #adb5bd;
    }

    .form__input-msg {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        min-height: 1rem;
        color: #6c757d;
    }

    /* Overlay hidden by default */
    .unit-card .card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        /* semi-transparent overlay */
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
        z-index: 10;
    }

    /* Show overlay on hover */
    .unit-card:hover .card-overlay {
        opacity: 1;
    }

    /* Style buttons */
    .unit-card .card-overlay .btn {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .unit-card .card-overlay .btn i {
        font-size: 16px;
    }
</style>
@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.vouchers') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.invoices') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary">
                    {{ __('dashboard.filter') }}
                </button>

            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('dashboard.invoice.index') }}">
            <div class="filter-form__container mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">

                            <!-- Invoice Number Filter -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.invoice_number') }}</label>
                                <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" class="form-control"
                                    placeholder="{{ __('dashboard.enter_invoice_number') }}">
                            </div>

                            <!-- Guest Name Filter -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.guest_name') }}</label>
                                <input type="text" name="guest_name" value="{{ request('guest_name') }}" class="form-control"
                                    placeholder="{{ __('dashboard.enter_guest_name') }}">
                            </div>

                            <!-- Status Filter -->
                            <div class="col-lg-2 col-md-4">
                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                        {{ __('dashboard.pending') }}
                                    </option>
                                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>
                                        {{ __('dashboard.paid') }}
                                    </option>
                                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>
                                        {{ __('dashboard.partial') }}
                                    </option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                        {{ __('dashboard.cancelled') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-lg-2 col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">{{ __('dashboard.search') }}</button>
                                <a href="{{ route('dashboard.invoice.index') }}" class="btn btn-outline-secondary">
                                    {{ __('dashboard.reset') }}
                                </a>
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
                            <th>{{ __('dashboard.invoice_type') }}</th>
                            <th>{{ __('dashboard.no') }}.</th>
                            <th>{{ __('dashboard.status') }}</th>
                            <th>{{ __('dashboard.amount') }}</th>
                            <th>{{ __('dashboard.invoice_period_from') }}</th>
                            <th>{{ __('dashboard.invoice_period_to') }}</th>
                            <th>{{ __('dashboard.invoice_date') }}</th>
                            <th>{{ __('dashboard.created_date_time') }}</th>
                            <th>{{ __('dashboard.guest') }}</th>
                            <th>{{ __('dashboard.order_no') }}.</th>
                            <th>{{ __('dashboard.outlet') }}</th>
                            <th class="text-center">{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>Reservation Invoice</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>
                                    @if($invoice->status == 'paid')
                                        <span class="badge bg-success">{{ __('dashboard.paid') }}</span>
                                    @elseif($invoice->status == 'partial')
                                        <span class="badge bg-info">{{ __('dashboard.partial') }}</span>
                                    @elseif($invoice->status == 'cancelled')
                                        <span class="badge bg-danger">{{ __('dashboard.cancelled') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ __('dashboard.pending') }}</span>
                                    @endif
                                </td>
                                <td>{{ number_format($invoice->total, 2) }} SAR</td>
                                <td>{{ $invoice->reservation->check_in_date ?? '-' }}</td>
                                <td>{{ $invoice->reservation->check_out_date ?? '-' }}</td>
                                <td>{{ $invoice->issue_date->format('Y/m/d') }}</td>
                                <td>{{ $invoice->created_at->format('Y/m/d h:i A') }}</td>
                                <td>{{ $invoice->reservation->guest->first_name ?? '' }} {{ $invoice->reservation->guest->last_name ?? '' }}</td>
                                <td>{{ $invoice->reservation->reservation_number ?? '-' }}</td>
                                <td>{{ $invoice->reservation->property->property_name_en ?? '-' }}</td>
                                <td class="text-center">
    <div class="d-flex justify-content-center align-items-center gap-1">
@can('invoice.view')
        <button class="btn btn-info btn-sm py-1 px-2" title="View"
            onclick="viewInvoice({{ $invoice->id }})">
            <i class="fas fa-eye small"></i>
        </button>
@endcan

        <div class="dropdown">
            <button class="btn btn-secondary btn-sm py-1 px-2 dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-ellipsis-v small"></i>
            </button>

            <ul class="dropdown-menu">
                @can('invoice.print')
                   <li>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="printInvoice({{ $invoice->id }})">
                        <i class="fas fa-print me-2"></i> {{__('dashboard.print')}}
                    </a>
                </li>
                @endcan

@can('invoice.edit')
    <li>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="editInvoice({{ $invoice->id }})">
                        <i class="fas fa-edit me-2"></i> {{__('dashboard.edit')}}
                    </a>
                </li>
@endcan

@can('invoice.email')
      <li>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="sendInvoice({{ $invoice->id }})">
                        <i class="fas fa-envelope me-2"></i> {{__('dashboard.send_email')}}
                    </a>
                </li>
@endcan

            </ul>
        </div>

    </div>
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox me-2"></i>
                                    {{ __('dashboard.no_records_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $invoices->appends(request()->query())->links() }}
            </div>
        </div>
    </main>

    <!-- View Invoice Modal -->
    <div class="modal fade" id="viewInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.invoice_details') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="invoiceDetailsContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Invoice Modal -->
    <div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.edit_invoice') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editInvoiceForm">
                    <div class="modal-body">
                        <input type="hidden" id="editInvoiceId">
                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.invoice_number') }}</label>
                            <input type="text" id="editInvoiceNumber" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.status') }}</label>
                            <select id="editInvoiceStatus" class="form-select" name="status">
                                <option value="pending">{{ __('dashboard.pending') }}</option>
                                <option value="paid">{{ __('dashboard.paid') }}</option>
                                <option value="partial">{{ __('dashboard.partial') }}</option>
                                <option value="cancelled">{{ __('dashboard.cancelled') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>
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
                        <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('en')">
                            English
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('ar')">
                            العربية
                        </button>
                        @if(optional($printingOption)->contract_template_type == 'double')
                        <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('both')">
                            Both
                        </button>
                        @endif
                    </div>
                    <div class="btn-group ms-3" role="group">
                        <button type="button" class="btn btn-primary" id="printBtn" onclick="printPage()">
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
@endsection
@push('scripts')
    <script>
        const toggleBtn = document.querySelector('.n-button.n-button--primary');
        const filterContainer = document.querySelector('.filter-form__container');

        filterContainer.style.display = 'none';

        toggleBtn.addEventListener('click', function() {
            if (filterContainer.style.display === 'none') {
                filterContainer.style.display = 'block';
            } else {
                filterContainer.style.display = 'none';
            }
        });

        const invoiceShowUrlTemplate = @json(route('dashboard.invoice.show', ['id' => '__INVOICE__']));
        const invoicePrintUrlTemplate = @json(route('dashboard.invoice.print', ['id' => '__INVOICE__']));
        const invoiceUpdateUrlTemplate = @json(route('dashboard.invoice.update', ['id' => '__INVOICE__']));
        const invoiceSendUrlTemplate = @json(route('dashboard.invoice.send', ['id' => '__INVOICE__']));

        function invoiceUrl(template, id) {
            return template.replace('__INVOICE__', encodeURIComponent(id));
        }

        function requestJson(url, options = {}) {
            return fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(options.headers || {}),
                },
                ...options,
            }).then(response => {
                if (!response.ok) {
                    throw new Error(`Request failed (${response.status})`);
                }

                return response.json();
            });
        }

        function viewInvoice(id) {
            requestJson(invoiceUrl(invoiceShowUrlTemplate, id))
                .then(data => {
                    const invoice = data.invoice;
                    let html = `
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>{{ __('dashboard.invoice_number') }}: ${invoice.invoice_number}</h6>
                                <p><strong>{{ __('dashboard.guest') }}:</strong> ${invoice.reservation?.guest?.first_name || ''} ${invoice.reservation?.guest?.last_name || ''}</p>
                                <p><strong>{{ __('dashboard.unit') }}:</strong> ${invoice.reservation?.unit?.unit_number || '-'}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <p><strong>{{ __('dashboard.issue_date') }}:</strong> ${invoice.issue_date ? new Date(invoice.issue_date).toLocaleDateString() : '-'}</p>
                                <p><strong>{{ __('dashboard.due_date') }}:</strong> ${invoice.due_date ? new Date(invoice.due_date).toLocaleDateString() : '-'}</p>
                                <p><strong>{{ __('dashboard.status') }}:</strong>
                                    <span class="badge ${invoice.status === 'paid' ? 'bg-success' : invoice.status === 'partial' ? 'bg-info' : 'bg-warning'}">
                                        ${invoice.status}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <hr>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.description') }}</th>
                                    <th>{{ __('dashboard.quantity') }}</th>
                                    <th>{{ __('dashboard.unit_price') }}</th>
                                    <th>{{ __('dashboard.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    invoice.items.forEach(item => {
                        html += `
                            <tr>
                                <td>${item.description}</td>
                                <td>${item.quantity}</td>
                                <td>${parseFloat(item.unit_price).toFixed(2)}</td>
                                <td>${parseFloat(item.total).toFixed(2)}</td>
                            </tr>
                        `;
                    });

                    html += `
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">{{ __('dashboard.subtotal') }}:</th>
                                    <th>${parseFloat(invoice.subtotal || 0).toFixed(2)}</th>
                                </tr>
                                ${parseFloat(invoice.discount_amount || 0) > 0 ? `
                                <tr>
                                    <th colspan="3" class="text-end">{{ __('dashboard.discount') }}:</th>
                                    <th>-${parseFloat(invoice.discount_amount).toFixed(2)}</th>
                                </tr>
                                ` : ''}
                                ${parseFloat(invoice.tax_amount || 0) > 0 ? `
                                <tr>
                                    <th colspan="3" class="text-end">{{ __('dashboard.taxes') }}:</th>
                                    <th>${parseFloat(invoice.tax_amount).toFixed(2)}</th>
                                </tr>
                                ` : ''}
                                ${parseFloat(invoice.security_deposit || 0) > 0 ? `
                                <tr>
                                    <th colspan="3" class="text-end">{{ __('dashboard.security_deposit') }}:</th>
                                    <th>${parseFloat(invoice.security_deposit).toFixed(2)}</th>
                                </tr>
                                ` : ''}
                                <tr class="table-primary">
                                    <th colspan="3" class="text-end">{{ __('dashboard.total') }}:</th>
                                    <th>${parseFloat(invoice.total || 0).toFixed(2)} SAR</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">{{ __('dashboard.paid') }}:</th>
                                    <th>${parseFloat(invoice.paid_amount || 0).toFixed(2)} SAR</th>
                                </tr>
                                <tr class="${parseFloat(invoice.balance || 0) > 0 ? 'table-warning' : 'table-success'}">
                                    <th colspan="3" class="text-end">{{ __('dashboard.balance') }}:</th>
                                    <th>${parseFloat(invoice.balance || 0).toFixed(2)} SAR</th>
                                </tr>
                            </tfoot>
                        </table>
                    `;

                    if (invoice.qr_code) {
                        html += `
                            <div class="text-center mt-3 p-3 border rounded bg-light">
                                <div id="zatca_qr_container" class="mt-2 d-inline-block"></div>
                            </div>
                        `;
                    }

                    document.getElementById('invoiceDetailsContent').innerHTML = html;

                    // Generate QR code after modal content is rendered
                    if (invoice.qr_code) {
                        setTimeout(function() {
                            document.getElementById('zatca_qr_container').innerHTML = '';
                            new QRCode(document.getElementById('zatca_qr_container'), {
                                text: invoice.qr_code,
                                width: 128,
                                height: 128
                            });
                        }, 100);
                    }

                    var modal = new bootstrap.Modal(document.getElementById('viewInvoiceModal'));
                    modal.show();
                });
        }

        function printInvoice(id) {
            const printUrl = invoiceUrl(invoicePrintUrlTemplate, id);
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

        function editInvoice(id) {
            requestJson(invoiceUrl(invoiceShowUrlTemplate, id))
                .then(data => {
                    const invoice = data.invoice;

                    document.getElementById('editInvoiceId').value = invoice.id;
                    document.getElementById('editInvoiceNumber').value = invoice.invoice_number;
                    document.getElementById('editInvoiceStatus').value = invoice.status;

                    var modal = new bootstrap.Modal(document.getElementById('editInvoiceModal'));
                    modal.show();
                });
        }

        // Handle edit form submission
        document.getElementById('editInvoiceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const id = document.getElementById('editInvoiceId').value;
            const formData = {
                status: document.getElementById('editInvoiceStatus').value,
            };

            requestJson(invoiceUrl(invoiceUpdateUrlTemplate, id), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(data => {
                if (data.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('editInvoiceModal'));
                    modal.hide();
                    alert('Invoice status updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating invoice');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating invoice');
            });
        });

        function sendInvoice(id) {
            // First get the invoice to get guest or corporate email
            requestJson(invoiceUrl(invoiceShowUrlTemplate, id))
                .then(data => {
                    const invoice = data.invoice;

                    // Try guest email first, then corporate email
                    let email = invoice.reservation?.guest?.email;

                    if (!email && invoice.reservation?.corporate?.email) {
                        email = invoice.reservation.corporate.email;
                    }

                    if (!email) {
                        alert('No email address found for this guest or corporate');
                        return;
                    }

                    // Send directly without prompt
                    sendEmailRequest(id, email);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching invoice details');
                });
        }

        function sendEmailRequest(id, email) {
            requestJson(invoiceUrl(invoiceSendUrlTemplate, id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: email })
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                } else {
                    alert(data.message || 'Failed to send email');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending email: ' + error.message);
            });
        }

        function deleteInvoice(id) {
            if (confirm('Are you sure you want to delete this invoice?')) {
                alert('Delete functionality coming soon!');
            }
        }
        
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

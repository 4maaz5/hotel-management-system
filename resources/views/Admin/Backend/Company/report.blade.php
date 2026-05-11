@extends('layout.master')
@section('title', 'Dashboard | Company')
@section('main')
    <div class="main-content">
        <h2 class="text-center all-branches-title">{{ __('dashboard.all_companies') }}</h2>
        <div class="row" id="companyCardContainer">

            @forelse ($companyCards as $company)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-4">
                    <div class="card shadow-sm h-100 position-relative">

                        <!-- View icon (centered) -->
                        <button class="btn btn-sm btn-primary view-company-report" data-company-id="{{ $company->id }}"
                            style="position:absolute; top:12px; right:12px; z-index:10;">
                            <i class="fas fa-eye"></i>
                        </button>

                        <!-- Logo -->
                        <div class="text-center p-3">
                            @if ($company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" class="img-fluid rounded"
                                    style="height:70px; object-fit:cover;">
                            @else
                                <span class="badge badge-secondary">No Logo</span>
                            @endif
                        </div>

                        <div class="card-body text-center">
                            <h6 class="fw-bold mb-1">{{ $company->legal_name }}</h6>
                            <p class="text-muted small mb-0">{{ $company->address }}</p>
                            <p class="text-muted small mb-0">{{ $company->email }}</p>
                            <p class="text-muted small">{{ $company->phone }}</p>
                        </div>

                    </div>
                </div>

            @empty
            @endforelse

        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $companyCards->links('pagination::bootstrap-4') }}
        </div>
    </div>
    <!-- Company Reports Modal -->
    <div class="modal fade" id="companyReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="companyReportModalTitle" class="modal-title">{{ __('dashboard.company_report') }}</h5>
                </div>

                <div class="modal-body">
                    <div id="companyReportContent" class="p-0">
                        <!-- Server will inject partial HTML here -->
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status"><span class="visually-hidden">Loading.../تحميل</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">

                    <button id="printReport" class="btn btn-primary">{{ __('dashboard.print') }}</button>

                    <button type="button" class="btn btn-primary" id="downloadReportPdf">
                        {{ __('dashboard.pdf') }}
                    </button>



                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).on('click', '.view-company-report', function(e) {
            e.preventDefault();

            var companyId = $(this).data('company-id');
            var url = "{{ url('companies') }}/" + companyId + "/reports";

            $('#companyReportModalTitle').text('Loading...');
            $('#companyReportContent').html(
                '<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>');
            var modal = new bootstrap.Modal(document.getElementById('companyReportModal'));
            modal.show();

            $.ajax({
                url: url,
                method: 'GET',
                success: function(html) {
                    $('#companyReportModalTitle').text('Company Reports');
                    $('#companyReportContent').html(html);
                },
                error: function(xhr) {
                    var message = 'Failed to load data.';
                    if (xhr.responseJSON && xhr.responseJSON.message) message = xhr.responseJSON
                        .message;

                    $('#companyReportContent').html('<div class="alert alert-danger">' + message +
                        '</div>');
                }
            });
        });



        document.getElementById('downloadReportPdf').addEventListener('click', function() {

            const element = document.getElementById('companyReportContent');

            const opt = {
                margin: 0.5,
                filename: 'company-report.pdf',
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                },
                jsPDF: {
                    unit: 'in',
                    format: 'a4',
                    orientation: 'portrait',
                }
            };

            // Generate PDF
            html2pdf()
                .set(opt)
                .from(element)
                .save();
        });


        document.getElementById('printReport').addEventListener('click', function() {
            let printContent = document.getElementById('companyReportContent').innerHTML;

            let printWindow = window.open('', '', 'width=900,height=650');

            printWindow.document.write(`
        <html>
        <head>
            <title>Print Report</title>

            <!-- Bootstrap Styles (so print looks exact) -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

            <style>
                body {
                    padding: 20px;
                }
                img {
                    max-width: 100%;
                    height: auto !important;
                }
            </style>
        </head>
        <body>
            ${printContent}
        </body>
        </html>
    `);

            printWindow.document.close();

            // Wait for images to load before printing
            printWindow.onload = function() {
                printWindow.print();
                printWindow.close();
            };
        });
    </script>

@endsection

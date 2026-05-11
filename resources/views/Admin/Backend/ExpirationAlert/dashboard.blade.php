@extends('layout.master')
@section('title', 'Dashboard | Expiration')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="row ">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.expired_soon') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $expiringSoonEmployee + $expiringSoonCompany }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/2.jpg') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.expire_document') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $expiredEmployee + $expiredCompany }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/3.jpg') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.employee_doc_expire') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $expiredEmployee }}</h2>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/6.jpg') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.company_docs_expire') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $expiredCompany }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/1.jpg') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>{{ __('dashboard.expiration_alert') }}</h4>
                        </div>
                        <div class="mb-3 d-flex justify-content-center align-items-center gap-2">
                            <button class="btn btn-outline-primary doc-filter active ml-4 mr-1"
                                data-status="all">{{ __('dashboard.all') }}</button>
                            <button class="btn btn-outline-danger doc-filter ml-4"
                                data-status="expired">{{ __('dashboard.expired') }}</button>
                            <button class="btn btn-outline-warning doc-filter ml-4"
                                data-status="expiring_soon">{{ __('dashboard.expiring_soon') }}</button>
                            <button class="btn btn-outline-success doc-filter ml-4"
                                data-status="active">{{ __('dashboard.active') }}</button>
                        </div>



                        <div class="card-body">
                            <div class="table-responsive">

                                <table class="table table-striped table-hover" id="tableExport">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('dashboard.document_name') }}</th>
                                            <th>{{ __('dashboard.employee_company') }}</th>
                                            <th>{{ __('dashboard.branch') }}</th>
                                            <th>{{ __('dashboard.document_type') }}</th>
                                            <th>{{ __('dashboard.issue_date') }}</th>
                                            <th>{{ __('dashboard.expiry_date') }}</th>
                                            <th>{{ __('dashboard.status') }}</th>
                                            <th>{{ __('dashboard.days_left') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div id="docPagination" class="d-flex justify-content-center mt-3"></div>

                            </div>
                        </div>
                    </div>
                </div>

        </section>

    </div>

    </div>
    </div>


@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.querySelector('#tableExport tbody');
        const buttons = document.querySelectorAll('.doc-filter');
        const paginationDiv = document.getElementById('docPagination');
        let documents = [];
        let currentPage = 1;
        let currentFilter = 'all';
        const perPage = 10;

        // Fetch documents from server
        fetch("{{ route('dashboard.expiration.filtered') }}")
            .then(res => res.json())
            .then(data => {
                documents = data;
                renderTable();
            });

        function getFiltered() {
            return currentFilter === 'all'
                ? documents
                : documents.filter(d => d.status === currentFilter);
        }

        function renderTable() {
            let filtered = getFiltered();
            let totalPages = Math.ceil(filtered.length / perPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;

            let start = (currentPage - 1) * perPage;
            let pageItems = filtered.slice(start, start + perPage);

            tableBody.innerHTML = '';
            pageItems.forEach((doc, i) => {
                tableBody.innerHTML += `
                <tr data-status="${doc.status}">
                    <td>${start + i + 1}</td>
                    <td>${doc.name}</td>
                    <td>${doc.owner} / ${doc.doc_type}</td>
                    <td>${doc.branch}</td>
                    <td>${doc.type}</td>
                    <td>${doc.issue_date}</td>
                    <td>${doc.expiration_date}</td>
                    <td>
                        ${doc.status === 'expired' ? '<span class="badge badge-danger">Expired</span>' :
                        doc.status === 'expiring_soon' ? '<span class="badge badge-warning">Expiring Soon</span>' :
                        '<span class="badge badge-success">Active</span>'}
                    </td>
                    <td>${doc.days_left}</td>
                </tr>`;
            });

            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            paginationDiv.innerHTML = '';
            if (totalPages <= 1) return;

            let html = '<nav><ul class="pagination pagination-sm">';

            // Previous
            html += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">&laquo; Previous</a></li>`;

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            }

            // Next
            html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">Next &raquo;</a></li>`;

            html += '</ul></nav>';
            paginationDiv.innerHTML = html;

            // Bind click events
            paginationDiv.querySelectorAll('.page-link').forEach(a => {
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    let page = parseInt(this.dataset.page);
                    if (page >= 1 && page <= totalPages) {
                        currentPage = page;
                        renderTable();
                    }
                });
            });
        }

        // Button click filter
        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                buttons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.status;
                currentPage = 1;
                renderTable();
            });
        });
    });
</script>

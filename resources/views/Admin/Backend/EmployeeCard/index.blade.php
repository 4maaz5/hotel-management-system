@extends('layout.master')
@section('title', 'Dashboard | Employee')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center mb-4">{{ __('dashboard.employee_id_cards') }}</h1>
        <div class="employee-cards-grid">
            @forelse($employeeCards as $employee)
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-2">{{ $employee->first_name }} {{ $employee->last_name }}</h6>
                            <p class="mb-1"><strong>{{ __('dashboard.employee_id') }}:</strong>
                                {{ $employee->employee_id }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.email') }}:</strong> {{ $employee->email ?? '-' }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.phone') }}:</strong> {{ $employee->phone ?? '-' }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.branch') }}:</strong>
                                {{ $employee->branch->name ?? '-' }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.residence_expiry') }}:</strong>
                                {{ $employee->residence_expiry_date ?? '-' }}</p>
                        </div>
                        <div class="mt-2 text-center">
                            <a href="#" class="text-info" title="View Card" data-toggle="modal"
                                data-target="#viewCardModal__{{ $employee->id }}">
                                <i class="fas fa-id-card" style="font-size: 24px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>


        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $employeeCards->links('pagination::bootstrap-5') }}
        </div>


        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.id_card_generator') }}</h4>

                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <!-- Filters -->
                                    <form id="employeeFilterForm"
                                        class="mb-3 d-flex flex-wrap align-items-center gap-2 p-3 bg-light rounded shadow-sm">

                                        <select name="branch_id" class="form-control " style="width: 220px;">
                                            <option value="all">{{ __('dashboard.all_branches') }}</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="search" class="form-control ml-5"
                                            placeholder="{{ __('dashboard.search_by_name_or') }}" style="width: 260px;">
                                        <input type="phone" name="phone" class="form-control ml-5"
                                            placeholder="{{ __('dashboard.search_by_phone_number') }}"
                                            style="width: 260px;">

                                        <button type="submit"
                                            class="btn btn-primary px-4 ml-2">{{ __('dashboard.filter') }}</button>
                                    </form>
                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.full_name') }}</th>
                                                <th>{{ __('dashboard.employee_id') }}</th>
                                                <th>{{ __('dashboard.email') }}</th>
                                                <th>{{ __('dashboard.phone') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.residence_expiry') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="employeeTableBody">
                                            @forelse($tableEmployees as $employee)
                                                @include('Admin.Backend.partials.card_row', ['employee' => $employee])
                                            @empty
                                                <tr><td colspan="7" class="text-center">{{ __('dashboard.no_employees_found') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div id="employeePagination" class="d-flex justify-content-center mt-3">
                                        {{ $tableEmployees->links('pagination::bootstrap-5') }}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @foreach ($employees as $employee)
            <div class="modal fade" id="viewCardModal__{{ $employee->id }}" tabindex="-1" role="dialog"
                aria-labelledby="viewCardModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">

                        <!-- Modal Body -->
                        <div class="modal-body" style="padding: 2rem; background: #f8f9fc;">
                            <div class="card mx-auto"
                                style="max-width: 450px; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.12); border: none; background: white;">

                                <!-- Brand Logo -->
                                <div class="text-center mt-3 brand-logo">
                                    <img src="{{ $employee->brand->logo ? asset('storage/' . $employee->brand->logo) : asset('img/default-logo.png') }}"
                                        alt="{{ $employee->brand->name }}"
                                        style="width:190px; height:auto; object-fit:cover;">
                                </div>

                                <!-- Profile Image -->
                                <div style="position: relative; margin-top: 15px; text-align:center;">
                                    <div id="logo-wrapper"
                                        style="display:inline-block; padding:5px; background:white; border-radius:50%; box-shadow:0 8px 20px rgba(0,0,0,0.15);">
                                        <img id="logo-img"
                                            src="{{ $employee->image ? asset('storage/' . $employee->image) : 'https://randomuser.me/api/portraits/men/75.jpg' }}"
                                            alt="{{ $employee->full_name }}"
                                            style="width:200px; height:200px; border-radius:50%; object-fit:cover;">
                                    </div>

                                </div>

                                <!-- Employee Name & ID -->
                                <div class="text-center mt-3 employeeId">
                                    <h4 class="font-weight-bold mb-1" style="color: #3182ce; font-weight:900;">
                                        {{ $employee->first_name . ' ' . $employee->last_name }}
                                        ({{ $employee->department->name }})
                                    </h4>

                                    <span class="badge"
                                        style="display:inline-block; text-align:center; background: #222; color:white; padding:5px 20px; border-radius:15px; font-size:1rem; font-weight:800;">
                                        ID: {{ $employee->employee_id ?? '-' }}
                                    </span>
                                </div>


                                <!-- QR Code -->
                                <div class="mt-4" style="display:flex; justify-content:center; margin-top:20px;">
                                    {!! QrCode::size(160)->generate($employee->qr_code) !!}
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer" style="background:#f8f9fc; border:none; padding:1rem 2rem;">
                            <button type="button" class="btn btn-light" data-dismiss="modal"
                                style="border-radius:10px; padding:0.5rem 1.5rem; font-weight:600; border:2px solid #e2e8f0;">
                                <i class="fas fa-times mr-2"></i>{{ __('dashboard.close') }}
                            </button>
                            <button type="button" class="btn" onclick="printEmployeeCard({{ $employee->id }})"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; border:none; border-radius:10px; padding:0.5rem 1.5rem; font-weight:600; box-shadow:0 4px 12px rgba(102,126,234,0.4);">
                                <i class="fas fa-print mr-2"></i>{{ __('dashboard.print_preview') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <script>
        function printEmployeeCard(employeeId) {
            const modal = document.querySelector(`#viewCardModal__${employeeId}`);
            if (!modal) return alert("Employee card not found.");

            const card = modal.querySelector(".card");
            if (!card) return alert("Card element missing.");

            const clone = card.cloneNode(true);

            // Fix image sources
            card.querySelectorAll("img").forEach((img, i) => {
                clone.querySelectorAll("img")[i].src = img.src;
            });

            // Create iframe
            const iframe = document.createElement('iframe');
            iframe.style.width = "500px"; // smaller preview width
            iframe.style.height = "130px"; // proportional height (card aspect ratio)
            iframe.style.position = "fixed";
            iframe.style.right = "10px";
            iframe.style.bottom = "10px";
            iframe.style.border = "1px solid #ccc"; // optional, for debug

            document.body.appendChild(iframe);

            const doc = iframe.contentWindow.document;

            doc.open();
            doc.write(`
<html>
<head>
<style>

    /* Center container */
    #logo-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Bigger profile image */
    #logo-img {
        width: 220px !important;
        height: 220px !important;
        object-fit: cover;
        border-radius: 50%;
    }

    /* Center text */
    .brand-logo, .employeeId {
        text-align: center;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

/* EMPLOYEE NAME — larger and bolder */
.employeeId h4 {
    font-size: 30px !important;
    font-weight: 900 !important; /* slightly bolder than 900 */
    letter-spacing: 0.5px;       /* optional, adds more visual weight */
    margin-top: 5px !important;
    margin-bottom: 5px !important;
    line-height: 1.1 !important;
    color: #222;
}



  /* EMPLOYEE ID BADGE — bigger + printer-friendly */
.employeeId .badge {
    font-size: 24px !important;
    padding: 10px 30px !important;
    border-radius: 20px !important;

    background-color: #222 !important;   /* dark gray (better than pure black) */
    color: #fff !important;

    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}



    @page {
        size: auto;
        margin: 0;
    }

    html, body {
        margin: 0;
        padding: 0;
    }

    .card {
        width: 100%;
        height: 100%;
        padding: 8px;
        box-sizing: border-box;
    }

   @media print {

    /* Force browsers to print colors */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Branch / Department / Designation → TEXT ONLY */
    #branch,
    #department,
    #designation,
    #branch div,
    #department div,
    #designation div {
        color: #222 !important;
    }



    /* Profile image border */
    #logo-img {
        border: 2px solid #000 !important;
    }
            /* VALUES (Actual names) */
    #branch div:last-child,
    #department div:last-child,
    #designation div:last-child {
        font-size: 1.15rem !important;   /* bigger value text */
        font-weight: 900 !important;
    }
         #logo-wrapper {
        background-color: transparent !important; /* instead of black */
        padding: 6px !important;
    }
}



</style>
</head>

<body>
    ${clone.outerHTML}
</body>
</html>
`);

            doc.close();

            // Give iframe time to load before printing
            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                document.body.removeChild(iframe);
            }, 500);
        }




        $(document).ready(function() {

            $('#employeeFilterForm').on('submit', function(e) {
                e.preventDefault();
                fetchEmployees(1);
            });

            $(document).on('click', '#employeePagination a', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                if (page) fetchEmployees(page);
            });

            function fetchEmployees(page) {
                var data = $('#employeeFilterForm').serialize();
                if (page) data += '&page=' + page;

                $.ajax({
                    url: "{{ route('employee.filter') }}",
                    type: 'GET',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        $('#employeeTableBody').html(response.html);
                        $('#employeePagination').html(response.pagination);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }

        });
    </script>



@endsection

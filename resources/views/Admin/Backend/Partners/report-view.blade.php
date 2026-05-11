@extends('layout.master')
@section('title', 'Dashboard | report-view')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_partners') }}</h1>


        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.partners') }}</h4>

                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.company_name') }}</th>
                                                <th>{{ __('dashboard.partner_name') }}</th>
                                                <th>{{ __('dashboard.type') }}</th>
                                                <th>{{ __('dashboard.email') }}</th>
                                                <th>{{ __('dashboard.phone') }}</th>
                                                <th>{{ __('dashboard.nationality') }}</th>
                                                <th>{{ __('dashboard.share') }} %</th>
                                                <th>{{ __('dashboard.share_quantity') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($partners as $partner)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $partner->company->name }}</td>
                                                    <td>{{ $partner->full_name }}</td>
                                                    <td>{{ ucfirst($partner->partner_type) }}</td>
                                                    <td>{{ $partner->email ?? '-' }}</td>
                                                    <td>{{ $partner->phone ?? '-' }}</td>
                                                    <td>{{ $partner->nationality }}</td>
                                                    <td>{{ $partner->share_percentage }}</td>
                                                    <td>{{ $partner->share_quantity }}</td>

                                                    <td>


                                                        <a href="{{ route('dashboard.finance.partner.reportView', $partner->id) }}"
                                                            class="text-secondary" ">
                                                                                                                            <i class="fas fa-eye"></i>
                                                                                                                        </a>
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
            </div>
        </section>
    </div>
@endsection

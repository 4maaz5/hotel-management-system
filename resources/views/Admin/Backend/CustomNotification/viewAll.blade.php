@extends('layout.master')
@section('title', 'Dashboard | Notification')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="row ">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>{{ __('dashboard.all_notifications') }}</h4>

                        </div>

                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-bell"></i> {{ __('dashboard.all_notifications') }}</h5>
                                <span class="badge bg-light text-dark">{{ $notifications->count() }}
                                    {{ __('dashbaord.total') }}</span>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.type') }}</th>
                                                <th>{{ __('dashboard.message_preview') }}</th>

                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($notifications as $index => $n)
                                                <tr id="notificationRow{{ $n->id }}">
                                                    <td>{{ $index + 1 }}</td>

                                                    <td>
                                                        {{ optional(optional(\App\Models\User::find($n->recipient_id))->branch)->name ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-info text-dark">{{ strtoupper($n->type) }}</span>
                                                    </td>
                                                    <td style="max-width: 250px;">
                                                        {{ Str::limit($n->message, 60) }}
                                                    </td>




                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted py-4">
                                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                                        {{-- <div>No notifications found</div> --}}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

        </section>
    </div>

    </div>
    </div>

@endsection

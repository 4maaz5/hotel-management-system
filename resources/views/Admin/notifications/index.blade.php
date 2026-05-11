@extends('layouts.app')

@section('content')
<div class="container-fluid p-4 bg-white p-3" style="border-radius:15px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary">{{ __('dashboard.all_notifications') }}</h3>
            <p class="text-muted mb-0">{{ __('dashboard.manage_your_notifications') }}</p>
        </div>
        <div>
            <form action="{{ route('dashboard.reservation.notifications.mark_all_read') }}" method="POST" class="mark-all-form">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-check-double me-2"></i>{{ __('dashboard.mark_all_read') }}
                </button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @forelse($notifications as $notification)
                @php
                    $iconClass = match($notification->type) {
                        'arrival' => 'sign-in-alt',
                        'departure' => 'sign-out-alt',
                        'payment' => 'dollar-sign',
                        'check_in' => 'user-check',
                        'check_out' => 'user-times',
                        'new_reservation' => 'calendar-plus',
                        default => 'bell'
                    };
                    $iconColor = match($notification->type) {
                        'arrival' => '#10b981',
                        'departure' => '#f59e0b',
                        'payment' => '#6366f1',
                        default => '#6b7280'
                    };
                @endphp
                <div class="border-bottom p-3 {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                    <div class="d-flex gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 48px; height: 48px; background-color: {{ $iconColor }}20;">
                            <i class="fas fa-{{ $iconClass }}" style="color: {{ $iconColor }}; font-size: 18px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ app()->getLocale() == 'ar' ? $notification->title_ar : $notification->title }}</h6>
                                    <p class="mb-1 text-muted">{{ app()->getLocale() == 'ar' ? $notification->message_ar : $notification->message }}</p>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                @if(is_null($notification->read_at))
                                    <span class="badge bg-primary">{{ __('dashboard.new') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-3">{{ __('dashboard.no_notifications') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.querySelector('.mark-all-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => {
            location.reload();
        });
    });
</script>
@endpush

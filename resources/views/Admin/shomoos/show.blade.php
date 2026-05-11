@extends('layouts.app')

@section('title', 'Shomoos Submission Details')

@php
    $requestPayload = $submission->request_payload ?? [];
    $responsePayload = $submission->response_payload ?? [];
@endphp

@section('content')
    <div class="container-fluid py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <p class="text-uppercase text-muted small mb-1">Government Integration</p>
                <h3 class="mb-1">Shomoos Submission Details</h3>
                <p class="text-muted mb-0">Inspect the exact simulated payload and response for this reservation event.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('setup-sidebar.shomoos.index') }}" class="btn btn-outline-secondary">
                    Back to Shomoos
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Submission Summary</h5>

                        <dl class="row mb-0">
                            <dt class="col-sm-5 text-muted">Reservation</dt>
                            <dd class="col-sm-7">{{ $submission->reservation?->reservation_number ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 text-muted">Event</dt>
                            <dd class="col-sm-7">{{ strtoupper(str_replace('_', ' ', $submission->event_type)) }}</dd>

                            <dt class="col-sm-5 text-muted">Status</dt>
                            <dd class="col-sm-7">{{ ucfirst($submission->status) }}</dd>

                            <dt class="col-sm-5 text-muted">Guest</dt>
                            <dd class="col-sm-7">{{ $submission->guest?->full_name ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 text-muted">Property</dt>
                            <dd class="col-sm-7">{{ $submission->reservation?->property?->property_name_en ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 text-muted">Unit</dt>
                            <dd class="col-sm-7">{{ $submission->reservation?->unit?->unit_number ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 text-muted">External Ref</dt>
                            <dd class="col-sm-7">{{ $submission->external_reference ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 text-muted">Attempted</dt>
                            <dd class="col-sm-7">{{ optional($submission->attempted_at)->format('Y-m-d H:i:s') ?? 'Pending' }}</dd>

                            <dt class="col-sm-5 text-muted">Created</dt>
                            <dd class="col-sm-7">{{ optional($submission->created_at)->format('Y-m-d H:i:s') }}</dd>
                        </dl>

                        @if ($submission->error_message)
                            <div class="alert alert-danger mt-4 mb-0">
                                <strong>Error:</strong> {{ $submission->error_message }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Request Payload</h5>
                            <span class="badge bg-light text-dark border">{{ count($requestPayload['occupants'] ?? []) }} occupants</span>
                        </div>
                        <pre class="bg-light border rounded p-3 small mb-0" style="max-height: 420px; overflow:auto;">{{ json_encode($requestPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Response Payload</h5>
                        <pre class="bg-light border rounded p-3 small mb-0" style="max-height: 320px; overflow:auto;">{{ json_encode($responsePayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

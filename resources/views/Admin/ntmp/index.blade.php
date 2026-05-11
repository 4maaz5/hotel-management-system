@extends('layouts.app')

@section('title', 'Saudi NTMP Settings')

@section('content')
    <div class="container-fluid py-3">
        <div class="row g-4">
            <div class="col-12 col-xl-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <p class="text-uppercase text-muted small mb-1">Government Integration</p>
                                <h4 class="mb-1">Saudi NTMP Settings</h4>
                                <p class="text-muted mb-0">Keep this property in simulation mode until official Saudi NTMP credentials are approved.</p>
                            </div>
                            <span class="badge bg-light text-dark border">{{ strtoupper($setting->connection_status) }}</span>
                        </div>

                        <form method="POST" action="{{ route('setup-sidebar.ntmp.update') }}" class="row g-3">
                            @csrf

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enabled" name="enabled" value="1"
                                        @checked(old('enabled', $setting->enabled))>
                                    <label class="form-check-label fw-semibold" for="enabled">Enable Saudi NTMP for this property</label>
                                </div>
                                <small class="text-muted">Leave disabled if the hotel is not yet approved for NTMP integration.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mode</label>
                                <select name="mode" class="form-select">
                                    @foreach (['simulation', 'test'] as $mode)
                                        <option value="{{ $mode }}" @selected(old('mode', $setting->mode) === $mode)>{{ ucfirst($mode) }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Live mode will be enabled after a real Saudi NTMP driver is configured.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Driver</label>
                                <select name="driver" class="form-select">
                                    <option value="fake" @selected(old('driver', $setting->driver) === 'fake')>Fake / Simulation</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Provider Name</label>
                                <input type="text" name="provider_name" class="form-control"
                                    value="{{ old('provider_name', $setting->provider_name) }}" placeholder="Saudi NTMP / Partner provider">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Endpoint</label>
                                <input type="text" name="endpoint" class="form-control"
                                    value="{{ old('endpoint', $setting->endpoint) }}" placeholder="Private endpoint when approved">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">API Key</label>
                                <input type="password" name="api_key" class="form-control"
                                    value="" autocomplete="new-password" placeholder="{{ $setting->api_key ? 'Leave blank to keep current API key' : 'Provided by NTMP' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control"
                                    value="{{ old('username', $setting->username) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control"
                                    value="" autocomplete="new-password" placeholder="{{ $setting->password ? 'Leave blank to keep current password' : '' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Branch Reference</label>
                                <input type="text" name="branch_reference" class="form-control"
                                    value="{{ old('branch_reference', $setting->branch_reference) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">License Reference</label>
                                <input type="text" name="license_reference" class="form-control"
                                    value="{{ old('license_reference', $setting->license_reference) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Establishment Reference</label>
                                <input type="text" name="establishment_reference" class="form-control"
                                    value="{{ old('establishment_reference', $setting->establishment_reference) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" rows="4" class="form-control"
                                    placeholder="Approval notes, provider contacts, VPN / whitelist requirements">{{ old('notes', $setting->notes) }}</textarea>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Save Saudi NTMP Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1">Recent Saudi NTMP Activity</h5>
                                <p class="text-muted mb-0">Simulation and future live submissions will appear here.</p>
                            </div>
                            @if ($setting->last_synced_at)
                                <span class="text-muted small">Last sync: {{ $setting->last_synced_at->format('Y-m-d H:i') }}</span>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Reservation</th>
                                        <th>Event</th>
                                        <th>Status</th>
                                        <th>Guest</th>
                                        <th>Attempted</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($submissions as $submission)
                                        <tr>
                                            <td>{{ $submission->reservation?->reservation_number ?? 'N/A' }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ strtoupper(str_replace('_', ' ', $submission->event_type)) }}</span></td>
                                            <td>{{ ucfirst($submission->status) }}</td>
                                            <td>{{ $submission->guest?->full_name ?? 'N/A' }}</td>
                                            <td>{{ optional($submission->attempted_at)->format('Y-m-d H:i') ?? 'Pending' }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('setup-sidebar.ntmp.show', $submission) }}" class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No Saudi NTMP submissions yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

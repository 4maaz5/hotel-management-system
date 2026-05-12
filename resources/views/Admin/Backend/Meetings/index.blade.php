@extends('layout.master')
@section('title', 'Dashboard | Meetings')
@section('main')

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_meetings') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.meetings') }}</h4>
                                @can('manage_dashboard')
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#createMeetingModal">
                                        {{ __('dashboard.add_meeting') }}
                                    </button>
                                @endcan

                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.meeting_title') }}</th>
                                                <th>{{ __('dashboard.start_time') }}</th>
                                                <th>{{ __('dashboard.duration') }}</th>
                                                <th>{{ __('dashboard.link') }}</th>
                                                <th>{{ __('dashboard.participants') }}</th>
                                                <th>{{ __('dashboard.created_at') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($meetings as $meeting)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>

                                                    <!-- Meeting Title -->
                                                    <td>{{ $meeting->title }}</td>

                                                    <!-- Start Time -->
                                                    <td>{{ $meeting->start_time ?? '-' }}</td>

                                                    <!-- Duration -->
                                                    <td>{{ $meeting->duration ? $meeting->duration . ' min' : '-' }}</td>

                                                    <!-- Meeting Link -->
                                                    <td>
                                                        @if ($meeting->link)
                                                            <a href="{{ route('meetings.join', $meeting->id) }}"
                                                                class="btn btn-sm btn-info">
                                                                {{ __('dashboard.join_meeting') }}
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <!-- Participants -->
                                                    <td>
                                                        @if ($meeting->participants->count())
                                                            @foreach ($meeting->participants as $participant)
                                                                <span
                                                                    class="badge bg-secondary">{{ $participant->user->name }}</span>
                                                            @endforeach
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <!-- Created At -->
                                                    <td>{{ $meeting->created_at?->format('Y-m-d') ?? '-' }}</td>
                                                    <td> <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteMeetingModal_{{ $meeting->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
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
        </section>



        <!-- Modal -->
        <div class="modal fade" id="createMeetingModal" tabindex="-1" role="dialog"
            aria-labelledby="createMeetingModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('dashboard.meetings.store') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="createMeetingModalLabel">{{ __('dashboard.create_meeting') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Meeting Title -->
                            <div class="form-group">
                                <label for="meetingTitle">{{ __('dashboard.meeting_title') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="meetingTitle" name="title"
                                    placeholder="{{ __('dashboard.meeting_title') }}" required>
                            </div>

                            <!-- Start Time -->
                            <div class="form-group">
                                <label for="startTime">{{ __('dashboard.start_time') }}</label>
                                <input type="datetime-local" class="form-control" id="startTime" name="start_time">
                            </div>

                            <!-- Duration -->
                            <div class="form-group">
                                <label for="duration">{{ __('dashboard.duration') }}
                                    ({{ __('dashboard.minutes') }})</label>
                                <input type="number" class="form-control" id="duration" name="duration"
                                    placeholder="{{ __('dashboard.duration') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="participants">{{ __('dashboard.invite_users') }}</label>
                                <select name="participants[]" id="participants" multiple class="form-control custom-select">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    {{ __('dashboard.hold_control_to_select_multiple') }}
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('dashboard.close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.create') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @foreach ($meetings as $meeting)
            <div class="modal fade" id="deleteMeetingModal_{{ $meeting->id }}" tabindex="-1"
                aria-labelledby="deleteAgentModalLabel_{{ $meeting->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteAgentModalLabel_{{ $meeting->id }}">
                                {{ __('dashboard.delete_meeting') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('dashboard.meeting.destroy', $meeting->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $meeting->title }}</strong>?
                                </p>
                            </div>

                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-danger">{{ __('dashboard.yes_delete') }}</button>
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        @endforeach


    </div>
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('createMeetingModal'));
                myModal.show();
            });
        </script>
    @endif
@endsection

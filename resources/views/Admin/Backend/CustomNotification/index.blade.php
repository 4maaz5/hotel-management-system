@extends('layout.master')
@section('title', 'Dashboard | Notification')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.custom_notifications') }}</h1>
        <div class="notifications-grid mb-5">
            @forelse($notifications as $index => $n)
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-2">{{ __('dashboard.recipient') }}: {{ ucfirst($n->recipient_type) }}</h6>

                            <p class="mb-1"><strong>{{ __('dashboard.type') }}:</strong> {{ strtoupper($n->type) }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.message') }}:</strong> {{ $n->message }}</p>
                            <p class="mb-1">
                                <strong>{{ __('dashboard.status') }}:</strong>
                                @if ($n->status === 'sent')
                                    <span class="badge bg-success">{{ __('dashboard.sent') }}</span>
                                @elseif($n->status === 'pending')
                                    <span class="badge bg-warning">{{ __('dashboard.pending') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('dashboard.failed') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center">
                    {{-- <p>No notifications found.</p> --}}
                </div>
            @endforelse
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $notificationCards->links('pagination::bootstrap-5') }}
        </div>
        <section class="section">
            <div class="row ">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>{{ __('dashboard.custom_notification') }}</h4>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAlertModal">
                                {{ __('dashboard.send_notification') }}
                            </button>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">

                                            <!-- Filter by Type -->
                                            <div class="col-md-3">
                                                <label>{{ __('dashboard.type') }}</label>
                                                <select id="typeFilter" class="form-control">
                                                    <option value="">{{ __('dashboard.all') }}</option>
                                                    <option value="sms">{{ __('dashboard.sms') }}</option>
                                                    <option value="email">{{ __('dashboard.email') }}</option>
                                                    <option value="system">{{ __('dashboard.system') }}</option>
                                                </select>
                                            </div>

                                            <!-- Filter by Status -->
                                            <div class="col-md-3">
                                                <label>{{ __('dashboard.status') }}</label>
                                                <select id="statusFilter" class="form-control">
                                                    <option value="">{{ __('dashboard.all') }}</option>
                                                    <option value="pending">{{ __('dashboard.pending') }}</option>
                                                    <option value="sent">{{ __('dashboard.sent') }}</option>
                                                    <option value="failed">{{ __('dashboard.failed') }}</option>
                                                </select>
                                            </div>

                                            <!-- Filter Button -->
                                            <div class="col-md-3">
                                                <label>&nbsp;</label>
                                                <button class="btn btn-primary btn-block" id="filterNotificationsBtn">
                                                    {{ __('dashboard.filter') }}
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('dashboard.recipient') }}</th>
                                            <th>{{ __('dashboard.type') }}</th>
                                            <th>{{ __('dashboard.message_preview') }}</th>
                                            <th>{{ __('dashboard.status') }}</th>
                                            {{-- <th>{{ __('dashboard.sent_date_time') }}</th> --}}
                                            <th>{{ __('dashboard.action') }}</th>

                                        </tr>
                                    </thead>
                                    <tbody id="notificationsTableBody">
                                        @include('Admin.Backend.partials.notification_rows')
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

        </section>

        <!-- Add Alert Modal -->
        <div class="modal fade" id="addAlertModal" tabindex="-1" role="dialog" aria-labelledby="addAlertModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAlertModalLabel">{{ __('dashboard.add_alert') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form id="addAlertForm">
                            <div class="row">



                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.type') }}</label>
                                    <select name="type" class="form-control" required>
                                        <option selected disabled>{{ __('dashboard.select_type') }}</option>
                                        {{-- <option value="SMS">SMS</option> --}}
                                        <option value="email">{{ __('dashboard.email') }}</option>
                                        <option value="system">{{ __('dashboard.system') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="departmentSelect">{{ __('dashboard.select_departments') }}</label>
                                    <select name="department_ids[]" class="form-control" id="departmentSelect" multiple
                                        size="5">
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}">
                                                {{ $dept->name }}
                                                @if ($dept->branch)
                                                    ({{ $dept->branch->name }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">{{ __('dashboard.hold_control') }}</small>
                                </div>

                            </div>


                            <div class="row">

                            </div>


                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.message') }}</label>
                                <textarea name="message" class="form-control" rows="3" placeholder="اكتب رسالتك هنا..." required></textarea>
                            </div>
                            <div class="row">


                                <div class="form-group col-md-12 mb-3">
                                    <label class="form-label">{{ __('dashboard.status') }}</label>
                                    <select name="status" class="form-control" required>
                                        <option selected disabled>{{ __('dashboard.select_status') }}</option>

                                        <option value="pending">{{ __('dashboard.pending') }}</option>
                                        <option value="sent">{{ __('dashboard.sent') }}</option>
                                        <option value="failed">{{ __('dashboard.failed') }}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="text-end">
                                <button type="reset" class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.save_alert') }}</button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>

        <!-- Edit Alert Modal -->
        <div class="modal fade" id="editAlertModal" tabindex="-1" role="dialog" aria-labelledby="editAlertModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAlertModalLabel">{{ __('dashboard.edit_sms_email') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form id="editAlertForm">
                            <input type="hidden" name="id" id="editNotificationId">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.recipient') }}(s)</label>
                                    <input type="text" class="form-control" name="recipient_type"
                                        id="editRecipientType" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.type') }}</label>
                                    <select class="form-control" name="type" id="editType">
                                        {{-- <option value="SMS">SMS</option>
                                        <option value="Email">Email</option> --}}
                                        <option value="System">{{ __('dashboard.system') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.message') }}</label>
                                <textarea name="message" id="editMessage" class="form-control" rows="3" required></textarea>
                            </div>



                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                <select class="form-control" name="status" id="editStatus">

                                    <option value="pending">{{ __('dashboard.pending') }}</option>
                                    <option value="sent">{{ __('dashboard.sent') }}</option>
                                    <option value="failed">{{ __('dashboard.failed') }}</option>
                                </select>
                            </div>

                            <div class="text-end">
                                <button type="reset"
                                    class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.update_alert') }}</button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>

        <!-- Delete Alert Modal -->
        <div class="modal fade" id="deleteAlertModal" tabindex="-1" role="dialog"
            aria-labelledby="deleteAlertModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteAlertModalLabel">{{ __('dashboard.delete_sms') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.confirm_delete_modal') }}</p>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="button" class="btn btn-danger">{{ __('dashboard.delete') }}</button>
                    </div>

                </div>
            </div>
        </div>

    </div>

    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $('#addAlertForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '/notifications',
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });

                    $('#addAlertModal').modal('hide');
                    $('#addAlertForm')[0].reset();
                },

                error: function(xhr) {

                    let message = 'Error sending notification';

                    if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'Attention',
                        text: message
                    });
                }
            });
        });


        $(document).on('click', '.editAlertBtn', function() {

            if ($(this).hasClass('disabled')) {
                e.preventDefault();
                return false; // Prevent modal from opening
            }
            const row = $(this).closest('tr');
            const id = row.attr('id').replace('notificationRow', '');
            const recipient_type = row.find('td:nth-child(2)').text().trim().toLowerCase();
            const typeText = row.find('td:nth-child(3)').text().trim().toLowerCase();

            let type = '';
            if (typeText === 'sms') type = 'SMS';
            else if (typeText === 'email') type = 'Email';
            else if (typeText === 'system') type = 'System';

            $('#editType').val(type);

            const message = row.find('td:nth-child(4)').text().trim();
            const status = row.find('td:nth-child(5) .badge').text().toLowerCase();
            const scheduled_at = row.find('td:nth-child(6)').text().trim();

            // let scheduled_date = '';
            // let scheduled_time = '';
            // if (scheduled_at !== '-') {
            //     const dt = new Date(scheduled_at);
            //     scheduled_date = dt.toISOString().split('T')[0];
            //     scheduled_time = dt.toTimeString().split(' ')[0].slice(0, 5);
            // }

            $('#editNotificationId').val(id);
            $('#editRecipientType').val(recipient_type);
            $('#editType').val(type);
            $('#editMessage').val(message);
            $('#editStatus').val(status);
            // $('#editScheduledDate').val(scheduled_date);
            // $('#editScheduledTime').val(scheduled_time);

            $('#editAlertModal').modal('show');
        });

        // $('#editAlertForm').on('submit', function(e) {
        //     e.preventDefault();

        //     const id = $('#editNotificationId').val();
        //     const formData = $(this).serialize();

        //     $.ajax({
        //         url: '/notifications/' + id, // RESTful update route
        //         type: 'PUT',
        //         data: formData,
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         success: function(res) {
        //             Swal.fire({
        //                 icon: 'success',
        //                 title: 'Updated!',
        //                 text: res.message,
        //                 timer: 2000,
        //                 showConfirmButton: false
        //             });

        //             $('#editAlertModal').modal('hide');
        //             loadNotifications(); // refresh notifications dropdown

        //             // Update the table row dynamically
        //             const row = $('#notificationRow' + id);
        //             row.find('td:nth-child(3)').text(res.notification.type.toUpperCase());
        //             row.find('td:nth-child(4)').text(res.notification.message);
        //             row.find('td:nth-child(5) .badge')
        //                 .removeClass('bg-success bg-warning bg-danger')
        //                 .addClass(res.notification.status === 'sent' ? 'bg-success' : res.notification
        //                     .status === 'pending' ? 'bg-warning' : 'bg-danger')
        //                 .text(res.notification.status.charAt(0).toUpperCase() + res.notification.status
        //                     .slice(1));
        //             // row.find('td:nth-child(6)').text(res.notification.scheduled_at ? res.notification
        //             //     .scheduled_at : '-');
        //         },
        //         error: function(err) {
        //             console.error(err.responseJSON);
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'Error!',
        //                 text: 'Failed to update notification'
        //             });
        //         }
        //     });
        // });

        $('#editAlertForm').on('submit', function(e) {
            e.preventDefault();

            const id = $('#editNotificationId').val();
            const formData = $(this).serialize();

            $.ajax({
                url: '/notifications/' + id,
                type: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    $('#editAlertModal').modal('hide');

                    // Update table row dynamically
                    const row = $('#notificationRow' + id);

                    row.find('td:nth-child(3)').text(res.notification.type.toUpperCase());
                    row.find('td:nth-child(4)').text(res.notification.message);

                    const badge = row.find('td:nth-child(5) .badge');
                    badge
                        .removeClass('bg-success bg-warning bg-danger')
                        .addClass(
                            res.notification.status === 'sent' ?
                            'bg-success' :
                            res.notification.status === 'pending' ?
                            'bg-warning' :
                            'bg-danger'
                        )
                        .text(
                            res.notification.status.charAt(0).toUpperCase() +
                            res.notification.status.slice(1)
                        );
                },

                error: function(xhr) {

                    let message = 'Failed to update notification';

                    if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'Attention',
                        text: message
                    });
                }
            });
        });


        let deleteNotificationId = null;

        // Open Delete Modal
        $(document).on('click', '.deleteAlertBtn', function(e) {
            deleteNotificationId = $(this).data('id');
            $('#deleteAlertModal').modal('show');
        });

        // Confirm Delete
        $('#deleteAlertModal .btn-danger').on('click', function() {
            if (!deleteNotificationId) return;

            $.ajax({
                url: '/notifications/' + deleteNotificationId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    $('#deleteAlertModal').modal('hide');
                    $('#notificationRow' + deleteNotificationId).remove();
                    deleteNotificationId = null;
                },
                error: function(err) {
                    console.error(err.responseJSON);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to delete notification'
                    });
                }
            });
        });

        $('#filterNotificationsBtn').on('click', function() {

            let type = $('#typeFilter').val();
            let recipient_type = $('#recipientTypeFilter').val();
            let status = $('#statusFilter').val();

            $.ajax({
                url: "{{ route('notifications.filter') }}",
                type: "GET",
                data: {
                    type: type,
                    recipient_type: recipient_type,
                    status: status
                },
                success: function(res) {
                    $('#notificationsTableBody').html(res.html);
                },
                error: function(err) {
                    console.log(err);
                }
            });
        });
    </script>
@endsection

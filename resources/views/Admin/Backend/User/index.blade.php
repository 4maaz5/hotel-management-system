@extends('layout.master')
@section('title', 'Dashboard | User')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.users') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
                                    {{ __('dashboard.create_user') }}
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.user_name') }}</th>
                                                <th>{{ __('dashboard.email') }}</th>
                                                <th>{{ __('dashboard.role') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th class="text-center">{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($users as $user)
                                                <tr id="user-row-{{ $user->id }}">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->role }}</td>
                                                    <td>{{ $user->branch->name ?? '' }}</td>
                                                    <td class="text-center">

                                                        <a href="#" class="text-secondary me-2 edit-user-btn"
                                                            data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                            data-email="{{ $user->email }}"
                                                            data-role="{{ $user->role }}"
                                                            data-branch="{{ $user->branch_id }}"
                                                            data-status="{{ $user->status }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <a href="#" class="text-danger delete-user-btn"
                                                            data-id="{{ $user->id }}" data-name="{{ $user->name }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>

                                                    </td>
                                                </tr>
                                            @empty
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

        <!-- Create User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">{{ __('dashboard.create_new_user') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form id="createUserForm">
                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.employee_name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control"
                                        placeholder="{{ __('dashboard.enter_name') }}" name="name" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.email') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control"
                                        placeholder="{{ __('dashboard.enter_email_address') }}" name="email" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.password') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control"
                                        placeholder="{{ __('dashboard.enter_password') }}" name="password" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.confirm_password') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control"
                                        placeholder="{{ __('dashboard.re_enter') }}" name="password_confirmation" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.select_role') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select form-control" name="role" required>
                                        <option value="" disabled>-- {{ __('dashboard.choose_roll') }} --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ $user->getRoleNames()->first() == $role->name ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.select_branch') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="branch" class="form-select form-control" required>
                                        <option value="" selected disabled>-- {{ __('dashboard.select_branch') }} --
                                        </option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ ucfirst($branch->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.create_user') }}</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">{{ __('dashboard.edit_user') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form id="editUserForm">
                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.full_name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="editUserName"
                                        placeholder="{{ __('dashboard.enter_full_name') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.email') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="editUserEmail"
                                        placeholder="{{ __('dashboard.enter_email_address') }}" required>
                                </div>

                                <!-- Role Dropdown -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.role') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select form-control" id="edit_role" required>
                                        <option value="" disabled>-- {{ __('dashboard.choose_roll') }} --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ $user->getRoleNames()->first() == $role->name ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>


                                </div>

                                <!-- Branch Dropdown -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.assign_branch') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select form-control" id="editUserBranch" required>
                                        <option value="" disabled>-- {{ __('dashboard.select_branch') }} --</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ ucfirst($branch->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Toggle Password Change -->
                                <div class="col-md-12 mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="changePasswordToggle">
                                        <label class="form-check-label"
                                            for="changePasswordToggle">{{ __('dashboard.change_password') }}</label>
                                    </div>
                                </div>

                                <!-- Password Fields (Hidden Initially) -->
                                <div class="col-md-6 change-password-section d-none mt-2">
                                    <label class="form-label">{{ __('dashboard.new_password') }}</label>
                                    <input type="password" class="form-control" id="editUserPassword"
                                        placeholder="{{ __('dashboard.enter_new_password') }}">
                                </div>

                                <div class="col-md-6 change-password-section d-none mt-2">
                                    <label class="form-label">{{ __('dashboard.confirm_new_password') }}</label>
                                    <input type="password" class="form-control" id="editUserConfirmPassword"
                                        placeholder="{{ __('dashboard.confirm_new_password') }}">
                                </div>

                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.update_user') }}</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <script>
            // Show/hide password fields on toggle
            $(document).on('change', '#changePasswordToggle', function() {
                $('.change-password-section').toggleClass('d-none');
                if ($(this).is(':checked')) {
                    $('#editUserPassword, #editUserConfirmPassword').attr('required', true);
                } else {
                    $('#editUserPassword, #editUserConfirmPassword').attr('required', false).val('');
                }
            });
        </script>
        <!-- Delete Branch Modal -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteBranchModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteBranchModalLabel">{{ __('dashboard.delete') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.confirm_delete_modal') }}?</p>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="button" class="btn btn-danger">{{ __('dashboard.delete') }}</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/jszip.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('js/page/datatables.js') }}"></script>
    <script>
        $(document).ready(function() {
            // =============================
            // CREATE USER
            // =============================
            $(document).on('submit', '#createUserForm', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $btn = $form.find('button[type="submit"]');
                $btn.prop('disabled', true).text('Creating...');

                $.ajax({
                    url: "{{ route('dashboard.setting.user.store') }}",
                    method: "POST",
                    data: $form.serialize(),
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#addUserModal').modal('hide');
                            $form[0].reset();
                            appendUserRow(res.data);

                            Swal.fire({
                                icon: 'success',
                                title: 'User Created!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorText = Object.values(errors).flat().join('<br>');
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validation Error',
                                html: errorText
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Server Error'
                            });
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Create User');
                    }
                });
            });


            // OPEN EDIT MODAL (Dynamic)
            // =============================
            $(document).on('click', '.edit-user-btn', function(e) {
                e.preventDefault();

                let btn = $(this);

                $('#editUserForm').attr('data-id', btn.data('id'));

                // Fill other fields
                $('#editUserName').val(btn.data('name'));
                $('#editUserEmail').val(btn.data('email'));
                $('#edit_role').val(btn.data('role')).trigger('change');
                $('#editUserBranch').val(btn.data('branch')).trigger('change');

                // Always clear password fields
                $('#editUserPassword, #editUserConfirmPassword').val('');
                $('#changePasswordToggle').prop('checked', false);
                $('.change-password-section').addClass('d-none');

                $('#editUserModal').modal('show');
            });

            // =============================
            // UPDATE USER
            // =============================
            $(document).on('submit', '#editUserForm', function(e) {
                e.preventDefault();

                let id = $(this).data('id');
                $.ajax({
                    url: "/dashboard/setting/user/update/" + id,
                    method: "POST",
                    data: {
                        _method: "PUT",
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        name: $('#editUserName').val(),
                        email: $('#editUserEmail').val(),
                        role: $('#edit_role').val(),
                        branch: $('#editUserBranch').val(),
                        password: $('#editUserPassword').val(),
                        password_confirmation: $('#editUserConfirmPassword').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#editUserModal').modal('hide');
                            updateUserRow(response.data);
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorText = Object.values(errors).flat().join('<br>');
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validation Error',
                                html: errorText
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Error updating user'
                            });
                        }
                    }
                });
            });



            // =============================
            // OPEN DELETE MODAL
            // =============================
            $(document).on('click', '.delete-user-btn', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');

                $('#deleteUserModal').attr('data-id', id);
                $('#deleteUserModal .modal-body strong').text(name);
                $('#deleteUserModal').modal('show');
            });


            // =============================
            // CONFIRM DELETE
            // =============================
            $('#deleteUserModal .btn-danger').on('click', function() {
                let id = $('#deleteUserModal').attr('data-id');

                $.ajax({
                    url: "{{ route('dashboard.setting.user.delete') }}",
                    method: "POST",
                    data: {
                        id: id,
                        _method: "DELETE",
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#deleteUserModal').modal('hide');
                            $('#user-row-' + id).remove();
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Error deleting user'
                        });
                    }
                });

            });


            // =============================
            // HELPER FUNCTIONS
            // =============================
            function appendUserRow(user) {
                const html = `
        <tr id="user-row-${user.id}">
            <td>${user.index}</td>
            <td>${user.name}</td>
            <td>${user.email}</td>
            <td>${user.role}</td>
            <td>${user.branch_name}</td>
            <td class="text-center">
                <a href="#" class="text-secondary me-2 edit-user-btn"
                    data-id="${user.id}"
                    data-name="${user.name}"
                    data-email="${user.email}"
                    data-role="${user.role}"
                    data-branch="${user.branch_id}">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="#" class="text-danger delete-user-btn"
                    data-id="${user.id}"
                    data-name="${user.name}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>
    `;
                $('#tableExport tbody').prepend(html);
            }


            function updateUserRow(user) {
                let row = $('#user-row-' + user.id);

                if (row.length) {
                    // Update visible columns
                    row.find('td:nth-child(2)').text(user.name);
                    row.find('td:nth-child(3)').text(user.email);
                    row.find('td:nth-child(4)').text(user.role);
                    row.find('td:nth-child(5)').text(user.branch_name);

                    // Update data attributes for next edit
                    row.find('.edit-user-btn')
                        .data('name', user.name)
                        .data('email', user.email)
                        .data('role', user.role)
                        .data('branch', user.branch_id);

                    row.find('.delete-user-btn').data('name', user.name);
                }
            }


        });
    </script>


@endsection

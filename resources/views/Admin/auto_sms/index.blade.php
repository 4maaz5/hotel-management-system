@extends('layouts.app')

@section('title', 'SMS Auto Send')

<style>
    .switch {
        position: relative;
        width: 46px;
        height: 22px;
    }

    .switch input {
        opacity: 0;
    }

    .slider {
        position: absolute;
        inset: 0;
        background: #ccc;
        border-radius: 30px;
        transition: .3s;
    }

    .slider:before {
        content: "";
        position: absolute;
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: .3s;
    }

    .switch input:checked+.slider {
        background: #0d6efd;
    }

    .switch input:checked+.slider:before {
        transform: translateX(24px);
    }
</style>

@section('content')
    <div class="bg-white p-4 rounded shadow-sm">

        {{-- Header --}}
        <div class="mb-3">
            <div class="text-muted">{{ __('dashboard.general_settings') }}</div>
            <h4>{{ __('dashboard.auto_send_settings') }}</h4>
        </div>

        {{-- FORM STARTS HERE --}}
        <form method="POST" action="{{ route('setup-sidebar.auto_sms.update') }}">
            @csrf

            {{-- Static Form Fields --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">{{ __('dashboard.property_name') }}</label>
                    <input type="text" name="property_name" class="form-control" value="">
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('dashboard.default_language') }}</label>
                    <select name="default_language" class="form-select">
                        <option value="en" selected>{{ __('dashboard.english') }}</option>
                        <option value="ar">{{ __('dashboard.arabic') }}</option>
                    </select>
                </div>
            </div>



            {{-- Tabs --}}
            <ul class="nav nav-tabs mb-3" id="smsTab">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#guestTab" type="button">
                        {{ __('dashboard.for_guests') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#userTab" type="button">
                        {{ __('dashboard.for_users') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                {{-- GUEST TAB --}}
                <div class="tab-pane fade show active" id="guestTab">
                    @php
                        $guestAccordionSettings = [
                            [
                                'title' => __('dashboard.guest_reservation_confirmation_title'),
                                'description' => __('dashboard.guest_reservation_confirmation_description'),
                                'message' => __('dashboard.guest_reservation_confirmation_message'),
                                'id' => 'guest_confirm',
                                'name' => __('dashboard.guest_reservation_confirmation_name'),
                            ],
                            [
                                'title' => __('dashboard.guest_checkin_reminder_title'),
                                'description' => __('dashboard.guest_checkin_reminder_description'),
                                'message' => __('dashboard.guest_checkin_reminder_message'),
                                'id' => 'guest_reminder',
                                'name' => __('dashboard.guest_checkin_reminder_name'),
                            ],
                            [
                                'title' => __('dashboard.guest_checkout_goodbye_title'),
                                'description' => __('dashboard.guest_checkout_goodbye_description'),
                                'message' => __('dashboard.guest_checkout_goodbye_message'),
                                'id' => 'guest_checkout',
                                'name' => __('dashboard.guest_checkout_goodbye_name'),
                            ],
                        ];

                        $guestSimpleSettings = [
                            [
                                'title' => __('dashboard.guest_checkin_welcome_title'),
                                'description' => __('dashboard.guest_checkin_welcome_description'),
                                'message' => __('dashboard.guest_checkin_welcome_message'),
                                'name' => __('dashboard.guest_checkin_welcome_name'),
                            ],
                            [
                                'title' => __('dashboard.guest_new_reservation_title'),
                                'description' => __('dashboard.guest_new_reservation_description'),
                                'message' => __('dashboard.guest_new_reservation_message'),
                                'name' => __('dashboard.guest_new_reservation_name'),
                            ],
                            [
                                'title' => __('dashboard.guest_receipt_title'),
                                'description' => __('dashboard.guest_receipt_description'),
                                'message' => __('dashboard.guest_receipt_message'),
                                'name' => __('dashboard.guest_receipt_name'),
                            ],
                            [
                                'title' => __('dashboard.guest_invoice_title'),
                                'description' => __('dashboard.guest_invoice_description'),
                                'message' => __('dashboard.guest_invoice_message'),
                                'name' => __('dashboard.guest_invoice_name'),
                            ],
                        ];
                    @endphp

                    {{-- Accordion Settings --}}
                    <div class="accordion mb-3" id="guestAccordion">
                        @foreach ($guestAccordionSettings as $item)
                            <div class="accordion-item mb-2 border rounded">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#{{ $item['id'] }}">
                                        <div class="d-flex align-items-center gap-3 w-100">
                                            <label class="switch mb-0">
                                                <input type="checkbox" name="{{ $item['name'] }}[enabled]" value="1"
                                                    checked>
                                                <span class="slider"></span>
                                            </label>
                                            <div>
                                                <div class="fw-semibold">{{ $item['title'] }}</div>
                                                <small class="text-muted">{{ $item['description'] }}</small>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="{{ $item['id'] }}" class="accordion-collapse collapse"
                                    data-bs-parent="#guestAccordion">
                                    <div class="accordion-body">
                                        <label class="form-label d-flex justify-content-between">
                                            <span>{{ __('dashboard.message_body') }}</span>
                                            <span>{{ __('dashboard.letters') }} : {{ strlen($item['message']) }}</span>
                                        </label>
                                        <textarea class="form-control" name="{{ $item['name'] }}[message]" rows="4">{{ $item['message'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @foreach ($guestSimpleSettings as $item)
                        <div class="border rounded p-3 mb-2">
                            <div class="d-flex gap-3 align-items-center mb-2">
                                <label class="switch mb-0">
                                    <input type="checkbox" name="{{ $item['name'] }}[enabled]" value="1" checked>
                                    <span class="slider"></span>
                                </label>
                                <div>
                                    <div class="fw-semibold">{{ $item['title'] }}</div>
                                    <small class="text-muted">{{ $item['description'] }}</small>
                                </div>
                            </div>
                            <input type="hidden" name="{{ $item['name'] }}[message]" value="{{ $item['message'] }}">
                        </div>
                    @endforeach
                </div>
                {{-- CANCEL & SAVE BUTTONS --}}
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>
                </div>
        </form>
        {{-- USER TAB --}}
        <div class="tab-pane fade" id="userTab">


            <form method="POST" action="{{ route('setup-sidebar.auto_sms.append') }}">
                @csrf

                <div class="row mb-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('dashboard.user_name') }}</label>
                        <select name="user_id" class="form-select" required>
                            <option disabled selected>{{ __('dashboard.select_user') }}</option>
                            @foreach ($availableUsers as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            {{ __('dashboard.append') }}
                        </button>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('dashboard.full_name') }}</th>
                        <th>{{ __('dashboard.email') }}</th>
                        <th>{{ __('dashboard.mobile_number') }}</th>
                        <th>{{ __('dashboard.selected_sms_types') }}</th>
                        <th width="80">{{ __('dashboard.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appendedUsers as $smsUser)
                        <tr>
                            <td>{{ $smsUser->user->name }}</td>
                            <td>{{ $smsUser->user->email }}</td>
                            <td>{{ $smsUser->user->employment_data['mobile'] ?? '-' }}</td>
                            <td>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#smsTypeModal"
                                    data-user-id="{{ $smsUser->user->id }}">
                                    {{ $smsUser->smsTypes->count() }} {{ __('dashboard.types') }}
                                </a>
                            </td>
                            <td>
                                <a href="#" data-bs-toggle="modal"
                                    data-bs-target="#deleteCustomRateModal{{ $smsUser->id }}"
                                    class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    </div>

    <div class="modal fade" id="smsTypeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dashboard.user_sms_types') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="smsTypesContainer">
                        @php
                            $templateTranslationMap = [
                                'reservation_confirmation' => 'dashboard.sms_template_reservation_confirmation',
                                'checkin_reminder' => 'dashboard.sms_template_checkin_reminder',
                                'checkout_goodbye' => 'dashboard.sms_template_checkout_goodbye',
                                'checkin_welcome' => 'dashboard.sms_template_checkin_welcome',
                                'new_reservation' => 'dashboard.sms_template_new_reservation',
                                'receipt' => 'dashboard.sms_template_receipt',
                                'invoice' => 'dashboard.sms_template_invoice',
                            ];
                        @endphp

                        @foreach ($templates as $template)
                            @php
                                $translationKey = $templateTranslationMap[$template->type] ?? $template->type;
                            @endphp

                            <div class="border rounded p-3 mb-2 d-flex align-items-center gap-3">

                                <label class="switch mb-0">
                                    <input type="checkbox" name="modal_types[]" value="{{ $template->id }}"
                                        class="modal-type-checkbox">
                                    <span class="slider"></span>
                                </label>

                                <div>
                                    <div class="fw-semibold">
                                        {{ __($translationKey) }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $template->message }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <input type="hidden" id="modalUserId">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.close') }}</button>
                    <button class="btn btn-primary" id="saveUserSmsTypes">{{ __('dashboard.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    @foreach ($appendedUsers as $smsUser)
        <div class="modal fade" id="deleteCustomRateModal{{ $smsUser->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_sms_user') }} – {{ $smsUser->user->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.delete_sms_user_confirmation') }}</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>
                        <form action="{{ route('setup-sidebar.auto_sms.delete', $smsUser->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                {{ __('dashboard.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
@push('scripts')
    <script>
        let selectedUserId = null;

        $('#smsTypeModal').on('show.bs.modal', function(event) {

            let button = $(event.relatedTarget);
            selectedUserId = button.data('user-id');

            $('#modalUserId').val(selectedUserId);

            $('.modal-type-checkbox').prop('checked', false);

            $.get('/sms-user-types/' + selectedUserId, function(data) {

                data.forEach(function(templateId) {
                    $('.modal-type-checkbox[value="' + templateId + '"]')
                        .prop('checked', true);
                });

            });

        });


        $('#saveUserSmsTypes').click(function() {

            let types = [];

            $('.modal-type-checkbox:checked').each(function() {
                types.push($(this).val());
            });

            $.post('/sms-user-types/save', {
                _token: '{{ csrf_token() }}',
                user_id: selectedUserId,
                types: types
            }, function(response) {

                if (response.success) {
                    $('#smsTypeModal').modal('hide');
                    showToast(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }

            });

        });

        function showToast(message) {

            let toast = `
        <div class="alert alert-success alert-dismissible fade show position-fixed"
             style="top:20px; right:20px; z-index:9999;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

            $('body').append(toast);

            setTimeout(function() {
                $('.alert').fadeOut();
            }, 3000);
        }
    </script>
@endpush

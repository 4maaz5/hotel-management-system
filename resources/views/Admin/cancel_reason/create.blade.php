@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

@section('title', 'Cancel no show Reason')

<style>
    .form__label-icon {
        width: 16px;
        height: 16px;
        fill: #6c757d;
        vertical-align: middle;
        margin-left: 5px;
    }

    .u-align-center {
        text-align: center;
    }

    .u-d-flex {
        display: flex;
    }

    .u-mt-10 {
        margin-top: 10px;
    }

    .u-mb-3 {
        margin-bottom: 3px;
    }

    .u-m-start-5 {
        margin-left: 5px;
    }

    .u-m-start-10 {
        margin-left: 10px;
    }

    .u-mb-20 {
        margin-bottom: 20px;
    }

    .u-my-15 {
        margin-top: 15px;
        margin-bottom: 15px;
    }

    .u-align-end {
        display: flex;
        justify-content: flex-end;
        align-items: flex-end;
    }

    .u-m-end-10 {
        margin-right: 10px;
    }

    .u-mt-15 {
        margin-top: 15px;
    }

    .u-pos-relative {
        position: relative;
    }

    .form__star {
        color: #dc3545;
        margin-left: 3px;
    }

    .custom-switch .form-check-input {
        width: 3em;
        height: 1.25em;
        cursor: pointer;
    }

    .informative-msg {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        background: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .informative-msg__icon {
        color: #0d6efd;
        flex-shrink: 0;
    }

    .informative-msg__icon svg {
        width: 18px;
        height: 18px;
    }

    .multi-lang__dropdown {
        min-width: 200px;
    }

    .dropdown-toggle::after {
        display: none;
    }

    .arabic-control {
        direction: rtl;
    }

    .view-btn {
        background: {{ $theme->card_bg_color }};
        border: 1px solid {{ $theme->card_border_color }};
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        margin-right: 8px;
        color: {{ $theme->text_color }};
    }

    .view-btn.active {
        background: {{ $theme->button_primary_bg_color }};
        color: {{ $theme->button_primary_text_color }};
        border-color: {{ $theme->button_primary_bg_color }};
    }

    .penalty-grid {
        background: {{ $theme->card_bg_color }};
        border-radius: 8px;
        padding: 20px;
        border: 1px solid {{ $theme->card_border_color }};
    }
</style>

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">
        <div class="page-category">{{ __('dashboard.rules') }}</div>

        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.cancel_no_show_reason_details') }}</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('setup-sidebar.cancel_reason.store') }}">
            @csrf

            <div class="penalty-grid">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('dashboard.name') }} <span class="form__star">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>

                <div class="row u-mt-10">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('dashboard.description') }}</label>
                        <textarea name="description" class="form-control" rows="5"></textarea>
                    </div>
                </div>

                {{-- <div class="row u-mt-10">
                    <div class="col-md-4">
                        <div class="form-check form-switch custom-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                            <label class="form-check-label" for="is_active">{{ __('dashboard.status') }}</label>
                        </div>
                    </div>
                </div> --}}

                <hr>

                <div class="row u-mb-20">
                    <div class="col-md-3 form__title" style="font-weight: 600; font-size: 16px;">
                        {{ __('dashboard.assign_penalties') }}
                    </div>
                    <div class="informative-msg">
                        <div class="informative-msg__icon">
                            <svg>
                                <use xlink:href="/assets/img/svg-icons.svg#icon-info"></use>
                            </svg>
                        </div>
                        <div>{{ __('dashboard.cancel_penalties_note') }}</div>
                    </div>
                </div>

                <div class="row u-mb-20">
                    <div class="col-lg-3 col-md-4">
                        <label class="form-label">{{ __('dashboard.penalty') }}</label>
                        <select class="form-select" id="penaltySelect">
                            <option value="">{{ __('dashboard.select_penalty') }}</option>
                            @foreach ($penalties as $penalty)
                                <option value="{{ $penalty->id }}">{{ $penalty->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-9 col-md-8 u-align-end">
                        <button type="button" class="btn btn-primary u-mb-20" onclick="appendPenalty()">
                            {{ __('dashboard.append') }}
                        </button>
                    </div>
                </div>

                <table class="table table-bordered" id="penaltyTable">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.penalty') }}</th>
                            <th>{{ __('dashboard.auto_apply') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="penaltyTableBody">
                    </tbody>
                </table>
                <div class="u-mt-15">{{ __('dashboard.total') }}: <span id="penaltyCount">0</span></div>
            </div>

            <div class="u-my-15 u-flex-end" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary u-m-end-10">{{ __('dashboard.save') }}</button>
                <a href="{{ route('setup-sidebar.cancel_reason.index') }}"
                    class="btn btn-secondary">{{ __('dashboard.cancel') }}</a>
            </div>
        </form>
    </main>
@endsection

@push('scripts')
    <script>
        let penaltyIndex = 0;
        const penalties = @json($penalties);

        function appendPenalty() {
            const select = document.getElementById('penaltySelect');
            const penaltyId = select.value;

            if (!penaltyId) return;

            const existingPenalty = document.querySelector(`tr[data-penalty-id="${penaltyId}"]`);
            if (existingPenalty) {
                alert('{{ __('dashboard.penalty_already_added') }}');
                return;
            }

            const penalty = penalties.find(p => p.id == penaltyId);
            if (!penalty) return;

            const tbody = document.getElementById('penaltyTableBody');
            const tr = document.createElement('tr');
            tr.setAttribute('data-penalty-id', penaltyId);
            tr.innerHTML = `
            <td>
                <input type="hidden" name="penalties[]" value="${penaltyId}">
                ${penalty.name}
            </td>
            <td>
                <div class="form-check form-switch custom-switch">
                    <input class="form-check-input" type="checkbox" name="auto_apply_${penaltyId}" value="1">
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removePenalty(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
            tbody.appendChild(tr);
            updatePenaltyCount();
            select.value = '';
        }

        function removePenalty(btn) {
            btn.closest('tr').remove();
            updatePenaltyCount();
        }

        function updatePenaltyCount() {
            document.getElementById('penaltyCount').textContent = document.querySelectorAll('#penaltyTableBody tr').length;
        }
    </script>
@endpush

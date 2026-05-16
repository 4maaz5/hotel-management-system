@extends('layouts.app')

@section('title', 'Theme Customization')

@section('content')
    <div class="container mt-4 bg-white p-3" style="border-radius:15px;">

        <div class="text-muted fw-semibold mb-2">
            {{ __('dashboard.general_settings') }}
        </div>

        <div class="mb-4">
            <h2 class="fw-bold">
                {{ __('dashboard.theme_customization') }}
            </h2>
            <p class="mb-0 text-muted">
                {{ __('dashboard.customize_colors_fonts_and_background') }}
            </p>
        </div>

        <form method="POST" action="{{ route('setup-sidebar.theme_customization.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-lg-6">
                    <!-- Sidebar Colors -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('dashboard.sidebar_colors') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.background_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="sidebar_bg_color" class="form-control form-control-color"
                                        value="{{ $theme->sidebar_bg_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->sidebar_bg_color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.text_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="sidebar_text_color" class="form-control form-control-color"
                                        value="{{ $theme->sidebar_text_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->sidebar_text_color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.active_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="sidebar_active_color"
                                        class="form-control form-control-color" value="{{ $theme->sidebar_active_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->sidebar_active_color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.hover_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="sidebar_hover_color" class="form-control form-control-color"
                                        value="{{ $theme->sidebar_hover_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->sidebar_hover_color }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Topbar Colors -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('dashboard.topbar_colors') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.background_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="topbar_bg_color" class="form-control form-control-color"
                                        value="{{ $theme->topbar_bg_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->topbar_bg_color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.text_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="topbar_text_color" class="form-control form-control-color"
                                        value="{{ $theme->topbar_text_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->topbar_text_color }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- General Colors -->
                    {{-- <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('dashboard.general_colors') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.text_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="text_color" class="form-control form-control-color"
                                        value="{{ $theme->text_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->text_color }}">
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Login Page Colors -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('dashboard.login_page_colors') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.background_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="login_bg_color" class="form-control form-control-color"
                                        value="{{ $theme->login_bg_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->login_bg_color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.text_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="login_text_color" class="form-control form-control-color"
                                        value="{{ $theme->login_text_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->login_text_color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.card_background') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="login_card_bg" class="form-control form-control-color"
                                        value="{{ $theme->login_card_bg }}">
                                    <input type="text" class="form-control" value="{{ $theme->login_card_bg }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <!-- Button Colors -->
                    {{-- <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('dashboard.button_colors') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.primary_button') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="button_primary_color"
                                        class="form-control form-control-color"
                                        value="{{ $theme->button_primary_color }}">
                                    <input type="text" class="form-control"
                                        value="{{ $theme->button_primary_color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.secondary_button') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="button_secondary_color"
                                        class="form-control form-control-color"
                                        value="{{ $theme->button_secondary_color }}">
                                    <input type="text" class="form-control"
                                        value="{{ $theme->button_secondary_color }}">
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Card Styles -->
                    {{-- <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('dashboard.card_styles') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.background_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="card_bg_color" class="form-control form-control-color"
                                        value="{{ $theme->card_bg_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->card_bg_color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.border_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="card_border_color"
                                        class="form-control form-control-color" value="{{ $theme->card_border_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->card_border_color }}">
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Table Styles -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('dashboard.table_styles') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.table_header_background') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="table_header_bg" class="form-control form-control-color"
                                        value="{{ $theme->table_header_bg }}">
                                    <input type="text" class="form-control" value="{{ $theme->table_header_bg }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.table_header_text') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="table_header_text"
                                        class="form-control form-control-color" value="{{ $theme->table_header_text }}">
                                    <input type="text" class="form-control" value="{{ $theme->table_header_text }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.table_row_even') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="table_row_even" class="form-control form-control-color"
                                        value="{{ $theme->table_row_even }}">
                                    <input type="text" class="form-control" value="{{ $theme->table_row_even }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.table_row_odd') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="table_row_odd" class="form-control form-control-color"
                                        value="{{ $theme->table_row_odd }}">
                                    <input type="text" class="form-control" value="{{ $theme->table_row_odd }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Field Styles -->
                    {{-- <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('dashboard.input_fields') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.background_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="input_bg_color" class="form-control form-control-color"
                                        value="{{ $theme->input_bg_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->input_bg_color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.border_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="input_border_color"
                                        class="form-control form-control-color" value="{{ $theme->input_border_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->input_border_color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.text_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="input_text_color" class="form-control form-control-color"
                                        value="{{ $theme->input_text_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->input_text_color }}">
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Dashboard Card Styles -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('dashboard.dashboard_card_colors') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.background_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="dashboard_card_bg" class="form-control form-control-color"
                                        value="{{ $theme->dashboard_card_bg }}">
                                    <input type="text" class="form-control" value="{{ $theme->dashboard_card_bg }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.border_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="dashboard_card_border" class="form-control form-control-color"
                                        value="{{ $theme->dashboard_card_border }}">
                                    <input type="text" class="form-control" value="{{ $theme->dashboard_card_border }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.icon_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="dashboard_icon_color" class="form-control form-control-color"
                                        value="{{ $theme->dashboard_icon_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->dashboard_icon_color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.card_title_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="dashboard_card_title_color" class="form-control form-control-color"
                                        value="{{ $theme->dashboard_card_title_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->dashboard_card_title_color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.card_text_color') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="dashboard_card_text_color" class="form-control form-control-color"
                                        value="{{ $theme->dashboard_card_text_color }}">
                                    <input type="text" class="form-control" value="{{ $theme->dashboard_card_text_color }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fonts -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('dashboard.fonts') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.font_family') }}</label>
                                <select class="form-select" name="font_family">
                                    <option value="Segoe UI, Tahoma, Geneva, Verdana, sans-serif"
                                        {{ $theme->font_family == 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ? 'selected' : '' }}>
                                        Segoe UI
                                    </option>
                                    <option value="Arial, Helvetica, sans-serif"
                                        {{ $theme->font_family == 'Arial, Helvetica, sans-serif' ? 'selected' : '' }}>
                                        Arial
                                    </option>
                                    <option value="Georgia, serif"
                                        {{ $theme->font_family == 'Georgia, serif' ? 'selected' : '' }}>
                                        Georgia
                                    </option>
                                    <option value="Times New Roman, Times, serif"
                                        {{ $theme->font_family == 'Times New Roman, Times, serif' ? 'selected' : '' }}>
                                        Times New Roman
                                    </option>
                                    <option value="Courier New, Courier, monospace"
                                        {{ $theme->font_family == 'Courier New, Courier, monospace' ? 'selected' : '' }}>
                                        Courier New
                                    </option>
                                    <option value="Verdana, Geneva, sans-serif"
                                        {{ $theme->font_family == 'Verdana, Geneva, sans-serif' ? 'selected' : '' }}>
                                        Verdana
                                    </option>
                                    <option value="Cairo, sans-serif"
                                        {{ $theme->font_family == 'Cairo, sans-serif' ? 'selected' : '' }}>
                                        Cairo
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('dashboard.images') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.background_image') }}</label>
                                <input type="file" name="background_image" class="form-control" accept="image/*">
                                @if ($theme->background_image)
                                    <div class="mt-2">
                                        <img src="{{ asset($theme->background_image) }}" alt="Background"
                                            style="max-height: 100px;">
                                        <small class="d-block text-muted">{{ __('dashboard.current_background') }}:
                                            {{ $theme->background_image }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.logo') }}</label>
                                <input type="file" name="logo" class="form-control" accept="image/*">
                                @if ($theme->logo)
                                    <div class="mt-2">
                                        <img src="{{ asset($theme->logo) }}" alt="Logo" style="max-height: 60px;">
                                        <small class="d-block text-muted">{{ __('dashboard.current_logo') }}:
                                            {{ $theme->logo }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3">
                @can('theme_customization.edit')
                    <button type="submit" class="btn btn-primary"
                        style="background-color: {{ $theme->button_primary_color }}; border-color: {{ $theme->button_primary_color }}">
                        {{ __('dashboard.save_changes') }}
                    </button>
                @endcan

            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="color"].form-control-color').forEach(function(colorInput) {
                const textInput = colorInput.parentElement?.querySelector('input[type="text"]');

                if (!textInput) {
                    return;
                }

                const syncText = function() {
                    textInput.value = colorInput.value;
                };

                const syncColor = function() {
                    const value = textInput.value.trim();

                    if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                        colorInput.value = value;
                    }
                };

                syncText();
                colorInput.addEventListener('input', syncText);
                textInput.addEventListener('input', syncColor);
                textInput.addEventListener('change', syncColor);
            });
        });
    </script>
@endpush

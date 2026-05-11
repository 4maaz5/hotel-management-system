@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
    $isArabic = app()->getLocale() === 'ar';
    $t = fn(string $ar, string $en): string => $isArabic ? $ar : $en;
    $segmentLabels = [
        'all' => $t('كل الضيوف', 'All Guests'),
        'vip' => 'VIP',
        'in_house' => $t('المقيمون حاليا', 'In House'),
        'today_arrivals' => $t('وصول اليوم', 'Today Arrivals'),
    ];
    $activeTab = old('sms_type', 'general');
    $selectedCount = count($selectedGuestIds ?? []);
    $deliveryModeLabel = $deliveryMode === 'gateway'
        ? $t('إرسال حي', 'Live Gateway')
        : $t('محاكاة آمنة', 'Safe Simulation');
@endphp

@section('title', 'SMS | Manual SMS')

<style>
    .manual-sms-page .hero-card {
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 22px;
        background:
            radial-gradient(circle at top right, rgba(14, 165, 233, 0.16), transparent 34%),
            linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(15, 23, 42, 0.02));
    }

    .manual-sms-page .summary-card,
    .manual-sms-page .content-card {
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.05);
    }

    .manual-sms-page .summary-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 18px;
    }

    .manual-sms-page .sms-tabs .nav-link {
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 999px;
        color: #0f172a;
        font-weight: 600;
        padding: 10px 18px;
        background: #fff;
    }

    .manual-sms-page .sms-tabs .nav-link.active {
        background: {{ $theme->button_primary_color }};
        border-color: {{ $theme->button_primary_color }};
        color: #fff;
    }

    .manual-sms-page .segment-chip {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 999px;
        padding: 10px 16px;
        background: #f8fafc;
        color: #0f172a;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .manual-sms-page .segment-chip:hover,
    .manual-sms-page .segment-chip.active {
        background: rgba(37, 99, 235, 0.1);
        border-color: rgba(37, 99, 235, 0.35);
        color: #1d4ed8;
    }

    .manual-sms-page .editor-shell {
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 18px;
        background: #f8fafc;
    }

    .manual-sms-page .soft-note {
        border: 1px solid rgba(37, 99, 235, 0.16);
        border-radius: 16px;
        background: rgba(37, 99, 235, 0.08);
        color: #1e3a8a;
    }

    .manual-sms-page .warning-note {
        border: 1px solid rgba(245, 158, 11, 0.25);
        border-radius: 16px;
        background: rgba(245, 158, 11, 0.12);
        color: #92400e;
    }

    .manual-sms-page .recipient-list {
        display: grid;
        gap: 12px;
        max-height: 500px;
        overflow-y: auto;
        padding-right: 2px;
    }

    .manual-sms-page .recipient-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px;
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 16px;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .manual-sms-page .recipient-row:hover {
        border-color: rgba(37, 99, 235, 0.35);
        background: rgba(248, 250, 252, 0.9);
    }

    .manual-sms-page .recipient-row input {
        margin-top: 3px;
    }

    .manual-sms-page .empty-state {
        border: 1px dashed {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 18px;
        background: #f8fafc;
        padding: 24px;
        text-align: center;
        color: #64748b;
    }

    .manual-sms-page .result-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .manual-sms-page .result-pill.failed {
        background: #fef2f2;
        color: #b91c1c;
    }

    .manual-sms-page .metric-label {
        font-size: 12px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #64748b;
    }
</style>

@section('content')
    <main class="bg-white p-3 p-lg-4" style="border-radius: 15px;">
        <section class="manual-sms-page">
            <div class="text-muted small text-uppercase mb-2">{{ __('dashboard.sms') }}</div>

            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="hero-card p-4 h-100">
                        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-start">
                            <div>
                                <h2 class="mb-2">{{ __('dashboard.send_manual_sms') }}</h2>
                                <p class="mb-3 text-muted">
                                    {{ $t('هذه الصفحة تعمل بشكل مستقل تماما عن صفحة الاشتراكات. يمكنك اختيار ضيوف من النظام أو كتابة أرقام جوال مباشرة ثم إرسال رسالة عامة أو تنبيه سريع.', 'This page now works completely separately from subscriptions. You can target guests from the system or type phone numbers directly, then send either a general SMS or a quick alert.') }}
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="result-pill">{{ $deliveryModeLabel }}</span>
                                    <span class="result-pill {{ $gatewayConfigured ? '' : 'failed' }}">
                                        {{ $gatewayConfigured ? $t('المزود الحي جاهز', 'Live gateway configured') : $t('لا يوجد مزود حي', 'No live gateway configured') }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-md-end">
                                <div class="metric-label mb-1">{{ $t('آخر محاولة', 'Last Attempt') }}</div>
                                <div class="fw-semibold">
                                    {{ $lastResult['timestamp'] ?? $t('لا يوجد بعد', 'No send yet') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="summary-card p-4 h-100">
                        <div class="small text-muted mb-2">{{ $t('جاهزية الإرسال', 'Send Readiness') }}</div>
                        <div class="h5 mb-3">{{ $deliveryModeLabel }}</div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ $t('ضيوف لديهم جوال', 'Guests with mobile') }}</span>
                            <strong>{{ $stats['all'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ $t('مطابقون للفلاتر', 'Matching current filter') }}</span>
                            <strong>{{ $stats['matching'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ $t('محددون حاليا', 'Currently selected') }}</span>
                            <strong id="selectedGuestCount">{{ $selectedCount }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">{{ $t('الوضع الحالي', 'Current mode') }}</span>
                            <strong>{{ $gatewayConfigured ? $t('حي', 'Live') : $t('محاكاة', 'Simulation') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif

            @if (session('danger'))
                <div class="alert alert-danger mb-4">{{ session('danger') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="content-card p-4 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h5 class="mb-1">{{ $t('فلترة الجمهور', 'Audience Filter') }}</h5>
                        <p class="text-muted small mb-0">
                            {{ $t('اختر شريحة جاهزة أو ابحث بالاسم أو الجوال ثم حدد من تريد مراسلته.', 'Choose a segment or search by guest name/mobile, then select who should receive the message.') }}
                        </p>
                    </div>
                    <div class="soft-note px-3 py-2 small">
                        {{ $t('يمكنك أيضا اختبار الإرسال مباشرة بكتابة أرقام جوال في الحقل المخصص.', 'You can also test sending immediately by typing phone numbers in the direct numbers field.') }}
                    </div>
                </div>

                <form method="GET" action="{{ route('dashboard.manual_sms.index') }}" class="row g-3 align-items-end">
                    <div class="col-lg-7">
                        <label for="search" class="form-label">{{ $t('بحث عن الضيف', 'Search Guest') }}</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ $search }}"
                            placeholder="{{ __('dashboard.search_by_name_phone') }}">
                    </div>
                    <div class="col-lg-5 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-search me-2"></i>{{ $t('تطبيق الفلتر', 'Apply Filter') }}
                        </button>
                        <a href="{{ route('dashboard.manual_sms.index') }}" class="btn btn-outline-secondary">
                            {{ $t('إعادة ضبط', 'Reset') }}
                        </a>
                    </div>
                </form>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    @foreach ($segmentLabels as $key => $label)
                        <a href="{{ route('dashboard.manual_sms.index', array_filter(['segment' => $key !== 'all' ? $key : null, 'search' => $search ?: null])) }}"
                            class="segment-chip {{ $segment === $key ? 'active' : '' }}">
                            <span>{{ $label }}</span>
                            <span>{{ $stats[$key] ?? 0 }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.manual_sms.send') }}">
                @csrf
                <input type="hidden" name="segment" value="{{ $segment }}">
                <input type="hidden" name="search" value="{{ $search }}">

                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="content-card p-4 h-100">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <h5 class="mb-1">{{ $t('المستلمون', 'Recipients') }}</h5>
                                    <p class="text-muted small mb-0">
                                        {{ $t('اجمع بين ضيوف محددين وأرقام مكتوبة يدويا في نفس الرسالة إذا أردت.', 'You can mix selected guests and manually typed phone numbers in the same send if needed.') }}
                                    </p>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleVisibleGuests">
                                    {{ $t('تحديد المعروض', 'Select Visible') }}
                                </button>
                            </div>

                            <div class="editor-shell p-3 mb-4">
                                <label for="phone_numbers" class="form-label">{{ $t('أرقام جوال مباشرة', 'Direct Phone Numbers') }}</label>
                                <textarea class="form-control" id="phone_numbers" name="phone_numbers" rows="4"
                                    placeholder="{{ $t('اكتب كل رقم في سطر مستقل أو افصل بينهم بفاصلة. مثال: +966500000000', 'Type each number on a new line or separate them with commas. Example: +966500000000') }}">{{ old('phone_numbers') }}</textarea>
                                <div class="small text-muted mt-2">
                                    {{ $t('تستخدم هذه الخانة لاختبار الإرسال حتى لو لم يكن لديك ضيوف مسجلون بعد.', 'Use this field to test sending even if you do not have guest records yet.') }}
                                </div>
                            </div>

                            @if ($guests->isEmpty())
                                <div class="empty-state">
                                    <div class="fw-semibold mb-2">{{ $t('لا توجد نتائج حالية', 'No Guest Results Right Now') }}</div>
                                    <div class="small">
                                        {{ $stats['all'] > 0 ? $t('غيّر الفلتر أو البحث لعرض ضيوف آخرين.', 'Try another filter or search term to load matching guests.') : $t('لا يوجد ضيوف لديهم أرقام جوال في قاعدة البيانات حاليا، لكن يمكنك استخدام الحقل أعلاه للإرسال إلى أرقام مباشرة.', 'There are no guests with mobile numbers in the database yet, but you can use the field above to send to direct numbers.') }}
                                    </div>
                                </div>
                            @else
                                <div class="recipient-list">
                                    @foreach ($guests as $guest)
                                        @php
                                            $className = $guest->guestClass?->class_name;
                                            if (is_array($className)) {
                                                $className = $className[app()->getLocale()] ?? $className['en'] ?? $className['ar'] ?? null;
                                            }
                                        @endphp
                                        <label class="recipient-row">
                                            <input type="checkbox" class="form-check-input recipient-checkbox"
                                                name="guest_ids[]" value="{{ $guest->id }}"
                                                {{ in_array($guest->id, $selectedGuestIds, true) ? 'checked' : '' }}>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">{{ $guest->full_name ?: $t('ضيف بدون اسم', 'Unnamed Guest') }}</div>
                                                <div class="small text-muted">{{ $guest->mobile }}</div>
                                                <div class="small text-muted">
                                                    {{ $className ?: $t('بدون تصنيف', 'No class assigned') }}
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            <div class="editor-shell p-3 mt-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">{{ $t('المعروض حاليا', 'Visible now') }}</span>
                                    <strong>{{ $guests->count() }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">{{ $t('مطابقون للفلاتر', 'Matching filter') }}</span>
                                    <strong>{{ $stats['matching'] }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">{{ $t('محددون للإرسال', 'Selected for send') }}</span>
                                    <strong id="selectedGuestCountBottom">{{ $selectedCount }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="content-card p-4 h-100">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <h5 class="mb-1">{{ $t('محرر الرسالة', 'Message Composer') }}</h5>
                                    <p class="text-muted small mb-0">
                                        {{ $t('اختر نوع الرسالة ثم أرسلها مباشرة من نفس الصفحة.', 'Choose the message type, then send it directly from the same page.') }}
                                    </p>
                                </div>
                                <div class="warning-note px-3 py-2 small">
                                    {{ $gatewayConfigured ? $t('سيتم استخدام المزود الحي الحالي.', 'The configured live gateway will be used.') : $t('الإرسال سيتم كمحاكاة فقط حتى تضيف SMS_API_URL في البيئة.', 'Sending will run in simulation mode until you configure SMS_API_URL in the environment.') }}
                                </div>
                            </div>

                            <ul class="nav sms-tabs border-0 gap-2 mb-4" id="smsTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $activeTab === 'alert' ? '' : 'active' }}" id="general-tab"
                                        data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab"
                                        aria-controls="general" aria-selected="{{ $activeTab === 'alert' ? 'false' : 'true' }}">
                                        {{ __('dashboard.general_sms') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $activeTab === 'alert' ? 'active' : '' }}" id="alert-tab"
                                        data-bs-toggle="tab" data-bs-target="#alert" type="button" role="tab"
                                        aria-controls="alert" aria-selected="{{ $activeTab === 'alert' ? 'true' : 'false' }}">
                                        {{ __('dashboard.alert_sms') }}
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="smsTabContent">
                                <div class="tab-pane fade {{ $activeTab === 'alert' ? '' : 'show active' }}" id="general"
                                    role="tabpanel" aria-labelledby="general-tab">
                                    <div class="editor-shell p-3 p-lg-4">
                                        <label for="message" class="form-label">{{ __('dashboard.message_body') }}</label>
                                        <textarea class="form-control" id="message" name="message" rows="10" maxlength="2000"
                                            placeholder="{{ __('dashboard.type_your_message_here') }}" data-character-target="generalCounter">{{ old('message') }}</textarea>

                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3">
                                            <div class="small text-muted">
                                                {{ $t('عدد الأحرف', 'Characters') }}:
                                                <strong id="generalCounter">0 / 2000</strong>
                                            </div>
                                            <button type="submit" class="btn btn-primary" name="sms_type" value="general">
                                                <i class="fas fa-paper-plane me-2"></i>{{ __('dashboard.send_message') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade {{ $activeTab === 'alert' ? 'show active' : '' }}" id="alert"
                                    role="tabpanel" aria-labelledby="alert-tab">
                                    <div class="editor-shell p-3 p-lg-4">
                                        <div class="row g-3">
                                            <div class="col-md-5">
                                                <label for="alert_title" class="form-label">{{ __('dashboard.alert_title') }}</label>
                                                <input type="text" class="form-control" id="alert_title"
                                                    name="alert_title" maxlength="120"
                                                    value="{{ old('alert_title') }}"
                                                    placeholder="{{ __('dashboard.enter_alert_title') }}">
                                            </div>
                                            <div class="col-md-7">
                                                <label for="alert_message" class="form-label">{{ __('dashboard.alert_message') }}</label>
                                                <textarea class="form-control" id="alert_message" name="alert_message" rows="8" maxlength="2000"
                                                    placeholder="{{ __('dashboard.type_your_alert_message') }}" data-character-target="alertCounter">{{ old('alert_message') }}</textarea>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3">
                                            <div class="small text-muted">
                                                {{ $t('عدد الأحرف', 'Characters') }}:
                                                <strong id="alertCounter">0 / 2000</strong>
                                            </div>
                                            <button type="submit" class="btn btn-warning text-white" name="sms_type"
                                                value="alert">
                                                <i class="fas fa-bell me-2"></i>{{ __('dashboard.send_alert_sms') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($lastResult)
                                <div class="content-card p-3 mt-4" style="border-radius: 18px;">
                                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                                        <div>
                                            <h6 class="mb-1">{{ $t('ملخص آخر محاولة', 'Last Send Summary') }}</h6>
                                            <div class="small text-muted">{{ $lastResult['message_preview'] ?? '' }}</div>
                                        </div>
                                        <span class="result-pill {{ ($lastResult['failed'] ?? 0) > 0 ? 'failed' : '' }}">
                                            {{ strtoupper($lastResult['delivery_mode'] ?? $deliveryMode) }}
                                        </span>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-4">
                                            <div class="editor-shell p-3 text-center">
                                                <div class="metric-label mb-1">{{ $t('مطلوب', 'Requested') }}</div>
                                                <div class="h5 mb-0">{{ $lastResult['requested'] ?? 0 }}</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="editor-shell p-3 text-center">
                                                <div class="metric-label mb-1">{{ $t('تم بنجاح', 'Succeeded') }}</div>
                                                <div class="h5 mb-0">{{ $lastResult['sent'] ?? 0 }}</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="editor-shell p-3 text-center">
                                                <div class="metric-label mb-1">{{ $t('فشل', 'Failed') }}</div>
                                                <div class="h5 mb-0">{{ $lastResult['failed'] ?? 0 }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    @if (! empty($lastResult['recipients']))
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>{{ $t('الجوال', 'Phone') }}</th>
                                                        <th>{{ $t('الحالة', 'Status') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($lastResult['recipients'] as $recipient)
                                                        <tr>
                                                            <td>{{ $recipient['phone'] }}</td>
                                                            <td>{{ ucfirst($recipient['status']) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageFields = document.querySelectorAll('[data-character-target]');
            const guestCheckboxes = Array.from(document.querySelectorAll('.recipient-checkbox'));
            const selectedGuestCount = document.getElementById('selectedGuestCount');
            const selectedGuestCountBottom = document.getElementById('selectedGuestCountBottom');
            const toggleVisibleGuestsButton = document.getElementById('toggleVisibleGuests');
            const preferredTab = @json($activeTab);

            messageFields.forEach(function(field) {
                const counter = document.getElementById(field.getAttribute('data-character-target'));

                const updateCounter = function() {
                    if (counter) {
                        counter.textContent = field.value.length + ' / ' + field.getAttribute('maxlength');
                    }
                };

                field.addEventListener('input', updateCounter);
                updateCounter();
            });

            const updateSelectedCount = function() {
                const totalSelected = guestCheckboxes.filter(function(checkbox) {
                    return checkbox.checked;
                }).length;

                if (selectedGuestCount) {
                    selectedGuestCount.textContent = totalSelected;
                }

                if (selectedGuestCountBottom) {
                    selectedGuestCountBottom.textContent = totalSelected;
                }
            };

            guestCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            if (toggleVisibleGuestsButton) {
                toggleVisibleGuestsButton.addEventListener('click', function() {
                    const shouldSelectAll = guestCheckboxes.some(function(checkbox) {
                        return !checkbox.checked;
                    });

                    guestCheckboxes.forEach(function(checkbox) {
                        checkbox.checked = shouldSelectAll;
                    });

                    updateSelectedCount();
                });
            }

            updateSelectedCount();

            if (preferredTab === 'alert') {
                const alertTab = document.getElementById('alert-tab');

                if (alertTab && window.bootstrap) {
                    new bootstrap.Tab(alertTab).show();
                }
            }
        });
    </script>
@endpush

{{--
@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
    $isArabic = app()->getLocale() === 'ar';
    $t = fn(string $ar, string $en): string => $isArabic ? $ar : $en;
@endphp

@section('title', 'SMS | Manual SMS')

<style>
    .manual-sms-page .hero-card {
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 22px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(14, 165, 233, 0.12));
    }

    .manual-sms-page .summary-card,
    .manual-sms-page .content-card {
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 20px;
        background: #fff;
    }

    .manual-sms-page .summary-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 18px;
    }

    .manual-sms-page .sms-tabs .nav-link {
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 999px;
        color: #0f172a;
        font-weight: 600;
        padding: 10px 18px;
        background: #fff;
    }

    .manual-sms-page .sms-tabs .nav-link.active {
        background: {{ $theme->button_primary_color }};
        border-color: {{ $theme->button_primary_color }};
        color: #fff;
    }

    .manual-sms-page .segment-chip {
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 999px;
        padding: 8px 14px;
        background: #f8fafc;
        font-size: 13px;
    }

    .manual-sms-page .editor-shell {
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 18px;
        background: #f8fafc;
    }

    .manual-sms-page .disabled-note {
        border: 1px solid rgba(245, 158, 11, 0.25);
        border-radius: 16px;
        background: rgba(245, 158, 11, 0.12);
        color: #92400e;
    }

    .manual-sms-page .alert-shell {
        border-color: rgba(239, 68, 68, 0.18);
        background: linear-gradient(135deg, rgba(254, 242, 242, 0.95), rgba(255, 255, 255, 1));
    }
</style>

@section('content')
    <main class="bg-white p-3 p-lg-4" style="border-radius: 15px;">
        <section class="manual-sms-page">
            <div class="text-muted small text-uppercase mb-2">{{ __('dashboard.sms') }}</div>

            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="hero-card p-4 h-100">
                        <h2 class="mb-2">{{ __('dashboard.send_manual_sms') }}</h2>
                        <p class="mb-0 text-muted">
                            {{ $t('تم تحسين هذه الشاشة لتكون أوضح وأكثر انسجاما مع النظام الحالي. ما زالت الواجهة للعرض فقط حاليا، وسيتم ربط الإرسال الفعلي لاحقا.', 'This screen has been refreshed to better match the current system. It is still a UI-only page for now, and the actual sending flow can be connected later.') }}
                        </p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="summary-card p-4 h-100 bg-light">
                        <div class="small text-muted mb-2">{{ $t('جاهزية الرسائل', 'SMS Readiness') }}</div>
                        <div class="h4 mb-3">4,800 SMS</div>
                        <div class="small text-muted">{{ $t('حالة الإرسال', 'Delivery Status') }}</div>
                        <div class="fw-semibold mb-3">{{ $t('واجهة فقط', 'UI Preview') }}</div>
                        <div class="small text-muted">{{ $t('آخر مزامنة', 'Last Sync') }}</div>
                        <div class="fw-semibold">{{ $t('اليوم 09:30 ص', 'Today 09:30 AM') }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6 col-xl-3">
                    <div class="summary-card p-3 h-100 d-flex gap-3 align-items-start">
                        <div class="summary-icon" style="background:#2563eb;"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="small text-muted">{{ __('dashboard.total_guests') }}</div>
                            <div class="h4 mb-1">248</div>
                            <div class="small text-muted">{{ $t('ضيوف متاحون للاستهداف', 'Guests available for targeting') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="summary-card p-3 h-100 d-flex gap-3 align-items-start">
                        <div class="summary-icon" style="background:#0f766e;"><i class="fas fa-user-check"></i></div>
                        <div>
                            <div class="small text-muted">{{ __('dashboard.selected_total_guests') }}</div>
                            <div class="h4 mb-1">0</div>
                            <div class="small text-muted">{{ $t('لا يوجد تحديد حي بعد', 'No live audience selected yet') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="summary-card p-3 h-100 d-flex gap-3 align-items-start">
                        <div class="summary-icon" style="background:#7c3aed;"><i class="fas fa-comment-dots"></i></div>
                        <div>
                            <div class="small text-muted">{{ __('dashboard.general_sms') }}</div>
                            <div class="h4 mb-1">1</div>
                            <div class="small text-muted">{{ $t('محرر رسالة عامة', 'General message composer') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="summary-card p-3 h-100 d-flex gap-3 align-items-start">
                        <div class="summary-icon" style="background:#ea580c;"><i class="fas fa-bell"></i></div>
                        <div>
                            <div class="small text-muted">{{ __('dashboard.alert_sms') }}</div>
                            <div class="h4 mb-1">1</div>
                            <div class="small text-muted">{{ $t('تنبيه تشغيلي سريع', 'Quick operational alert') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="nav sms-tabs border-0 gap-2 mb-4" id="smsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                        type="button" role="tab" aria-controls="general" aria-selected="true">
                        {{ __('dashboard.general_sms') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="alert-tab" data-bs-toggle="tab" data-bs-target="#alert" type="button"
                        role="tab" aria-controls="alert" aria-selected="false">
                        {{ __('dashboard.alert_sms') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="smsTabContent">
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <div class="content-card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1">{{ $t('نطاق المستلمين', 'Recipient Scope') }}</h5>
                                        <p class="text-muted small mb-0">{{ $t('حدد الشريحة أو ابحث عن اسم ضيف بشكل بصري.', 'Review segments or search for a guest name visually.') }}</p>
                                    </div>
                                    <span class="badge text-bg-light">{{ __('dashboard.selected_search_critaria') }}</span>
                                </div>

                                <div class="mb-3">
                                    <label for="guestName" class="form-label">{{ __('dashboard.guest_name') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="guestName"
                                            placeholder="{{ __('dashboard.enter_guest_name') }}">
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <button type="button" class="segment-chip">{{ $t('كل الضيوف', 'All Guests') }}</button>
                                    <button type="button" class="segment-chip">VIP</button>
                                    <button type="button" class="segment-chip">{{ $t('المقيمون حاليا', 'In House') }}</button>
                                    <button type="button" class="segment-chip">{{ $t('وصول اليوم', 'Today Arrivals') }}</button>
                                </div>

                                <div class="editor-shell p-3">
                                    <div class="small text-muted mb-2">{{ __('dashboard.total_guests') }}</div>
                                    <div class="fw-semibold mb-3">248</div>
                                    <div class="small text-muted mb-2">{{ __('dashboard.selected_total_guests') }}</div>
                                    <div class="fw-semibold mb-3">0</div>
                                    <div class="small text-muted mb-2">{{ __('dashboard.selected_search_critaria') }}</div>
                                    <div class="text-muted">{{ $t('لا توجد معايير محددة بعد', 'No criteria selected yet') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="content-card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1">{{ __('dashboard.general_sms') }}</h5>
                                        <p class="text-muted small mb-0">{{ $t('محرر أوضح لكتابة الرسائل العامة مع عداد أحرف مباشر.', 'A clearer composer for general guest messages with a live character counter.') }}</p>
                                    </div>
                                    <span class="badge text-bg-light">{{ __('dashboard.message_body') }}</span>
                                </div>

                                <div class="editor-shell p-3 p-lg-4">
                                    <div class="row g-3">
                                        <div class="col-md-5">
                                            <label class="form-label">{{ __('dashboard.guest_name') }}</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    placeholder="{{ __('dashboard.search_by_guest_name') }}">
                                                <button class="btn btn-outline-secondary" type="button">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <label for="messageBody" class="form-label">{{ __('dashboard.message_body') }}</label>
                                            <textarea class="form-control" id="messageBody" rows="8" maxlength="2000"
                                                placeholder="{{ __('dashboard.type_your_message_here') }}" data-character-target="generalCounter"></textarea>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3">
                                        <div class="small text-muted">
                                            {{ $t('عدد الأحرف', 'Characters') }}:
                                            <strong id="generalCounter">0 / 2000</strong>
                                        </div>
                                        <button type="button" class="btn btn-primary" disabled>
                                            <i class="fas fa-paper-plane me-2"></i>{{ __('dashboard.send_message') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="disabled-note p-3 mt-3">
                                    <i class="fas fa-circle-info me-2"></i>
                                    {{ $t('زر الإرسال معطل حاليا لأن منطق مزود الرسائل لم يتم ربطه بعد. تم تحسين التصميم وتجربة الاستخدام فقط.', 'The send button is intentionally disabled until the SMS provider flow is connected. For now, this change improves only the UI and workflow layout.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="alert" role="tabpanel" aria-labelledby="alert-tab">
                    <div class="content-card p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ __('dashboard.send_alert_sms') }}</h5>
                                <p class="text-muted small mb-0">{{ $t('قسم منظم لإعداد الرسائل التنبيهية والعاجلة قبل ربط الإرسال الفعلي.', 'A cleaner space for urgent and alert-style messages before the live sending flow is connected.') }}</p>
                            </div>
                            <span class="badge text-bg-warning">{{ $t('أولوية عالية', 'High Priority') }}</span>
                        </div>

                        <div class="editor-shell alert-shell p-3 p-lg-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="alertTitle" class="form-label">{{ __('dashboard.alert_title') }}</label>
                                    <input type="text" class="form-control" id="alertTitle"
                                        placeholder="{{ __('dashboard.enter_alert_title') }}">
                                </div>
                                <div class="col-md-8">
                                    <label for="alertMessage" class="form-label">{{ __('dashboard.alert_message') }}</label>
                                    <textarea class="form-control" id="alertMessage" rows="8" maxlength="2000"
                                        placeholder="{{ __('dashboard.type_your_alert_message') }}" data-character-target="alertCounter"></textarea>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3">
                                <div class="small text-muted">
                                    {{ $t('عدد الأحرف', 'Characters') }}:
                                    <strong id="alertCounter">0 / 2000</strong>
                                </div>
                                <button type="button" class="btn btn-warning text-white" disabled>
                                    <i class="fas fa-bell me-2"></i>{{ __('dashboard.send_alert_sms') }}
                                </button>
                            </div>
                        </div>

                        <div class="disabled-note p-3 mt-3">
                            <i class="fas fa-circle-info me-2"></i>
                            {{ $t('واجهة التنبيه أصبحت أوضح بصريا، لكن التنفيذ الفعلي سيضاف لاحقا مع الفلترة وربط الرسائل.', 'The alert layout is now visually cleaner, but the actual delivery logic will be added later with audience filtering and SMS gateway integration.') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card p-4 mt-4">
                <div class="mb-3">
                    <h5 class="mb-1">{{ $t('معاينة السجلات والشرائح', 'Audience & History Preview') }}</h5>
                    <p class="text-muted small mb-0">{{ $t('جدول استرشادي لعرض الشرائح أو الحملات السابقة بشكل أوضح داخل نفس الصفحة.', 'A cleaner reference table for audience selections or previous manual campaigns.') }}</p>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('dashboard.total_guests') }}</th>
                                <th>{{ __('dashboard.selected_total_guests') }}</th>
                                <th>{{ __('dashboard.selected_search_critaria') }}</th>
                                <th class="text-center">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>50</td>
                                <td>25</td>
                                <td>All VIP Guests</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-eye"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>30</td>
                                <td>10</td>
                                <td>Guests from 01/01/2026</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-eye"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    {{ $t('لا توجد سجلات إضافية حاليا', 'No additional records available right now') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageFields = document.querySelectorAll('[data-character-target]');

            messageFields.forEach(function(field) {
                const counter = document.getElementById(field.getAttribute('data-character-target'));

                const updateCounter = function() {
                    if (counter) {
                        counter.textContent = field.value.length + ' / ' + field.getAttribute('maxlength');
                    }
                };

                field.addEventListener('input', updateCounter);
                updateCounter();
            });
        });
    </script>
@endpush
--}}

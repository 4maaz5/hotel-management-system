@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
    $isArabic = app()->getLocale() === 'ar';
    $t = fn(string $ar, string $en): string => $isArabic ? $ar : $en;

    $integrations = [
        [
            'id' => 'zatca',
            'icon' => 'fa-file-invoice-dollar',
            'accent' => '#0f766e',
            'title' => $t('زاتكا المرحلة الثانية', 'ZATCA Phase 2'),
            'subtitle' => $t('الفوترة الإلكترونية والامتثال الضريبي', 'E-invoicing and tax compliance'),
            'price' => $t('ابتداء من 1,499 ريال', 'From SAR 1,499'),
            'billing' => $t('تفعيل + دعم سنوي', 'Activation + annual support'),
            'summary' => $t(
                'ربط الفواتير الإلكترونية، توليد QR، وتهيئة التكامل مع المتطلبات التشغيلية والضريبية.',
                'Electronic invoice integration, QR generation, and operational compliance setup.'
            ),
            'prices' => [
                ['label' => $t('رسوم التفعيل', 'Activation fee'), 'value' => $t('1,499 ريال', 'SAR 1,499')],
                ['label' => $t('الدعم السنوي', 'Annual support'), 'value' => $t('899 ريال', 'SAR 899')],
                ['label' => $t('مدة التنفيذ', 'Lead time'), 'value' => $t('3-5 أيام عمل', '7-10 business days')],
            ],
            'features' => [
                $t('إعداد المتطلبات الأساسية للمرحلة الثانية', 'Phase 2 onboarding and readiness setup'),
                $t('ربط الفاتورة مع بيانات المنشأة', 'Invoice mapping with property identity'),
                $t('اختبار الإصدار قبل التفعيل النهائي', 'Pre-activation testing before go-live'),
            ],
            'steps' => [
                $t('يتواصل مالك المنشأة مع الشركة المزودة للنظام.', 'Property owner contacts the system provider.'),
                $t('يتم اعتماد العرض التجاري والمتطلبات النظامية.', 'Commercial offer and compliance scope are confirmed.'),
                $t('يتم جدولة التفعيل والتجهيز الفني.', 'Activation and technical onboarding are scheduled.'),
            ],
        ],
        [
            'id' => 'booking',
            'icon' => 'fa-bed',
            'accent' => '#1d4ed8',
            'title' => $t('مزامنة Booking.com', 'Booking.com Synchronization'),
            'subtitle' => $t('تحديث الأسعار والتوافر والحجوزات', 'Rates, inventory, and reservation sync'),
            'price' => $t('ابتداء من 999 ريال', 'From SAR 999'),
            'billing' => $t('اشتراك شهري', 'Monthly subscription'),
            'summary' => $t(
                'مزامنة بيانات الغرف والأسعار والتوافر والحجوزات بين النظام وBooking.com.',
                'Two-way sync for rooms, rates, availability, and reservations with Booking.com.'
            ),
            'prices' => [
                ['label' => $t('التهيئة الأولية', 'Initial setup'), 'value' => $t('999 ريال', 'SAR 999')],
                ['label' => $t('الاشتراك الشهري', 'Monthly fee'), 'value' => $t('349 ريال', 'SAR 349')],
                ['label' => $t('مدة الربط', 'Lead time'), 'value' => $t('2-4 أيام عمل', '2-4 business days')],
            ],
            'features' => [
                $t('مزامنة التوافر لمنع التعارض', 'Availability sync to reduce overbooking risk'),
                $t('مزامنة الأسعار والخطط السعرية', 'Rate and rate-plan synchronization'),
                $t('استقبال الحجوزات الواردة تلقائيا', 'Automatic import of incoming bookings'),
            ],
            'steps' => [
                $t('يتم طلب الخدمة من الشركة المطورة للنظام.', 'The service is requested from the system provider.'),
                $t('يتم مراجعة حساب Booking.com ومتطلبات القنوات.', 'The Booking.com account and mapping requirements are reviewed.'),
                $t('يتم تفعيل الربط بعد الموافقة التجارية.', 'Sync is activated after commercial approval.'),
            ],
        ],
        [
            'id' => 'shomoos',
            'icon' => 'fa-id-card-clip',
            'accent' => '#b45309',
            'title' => $t('تكامل شموس السعودي', 'Saudi Shomoos Integration'),
            'subtitle' => $t('رفع بيانات النزلاء حسب المتطلبات', 'Guest data submission compliance'),
            'price' => $t('ابتداء من 1,299 ريال', 'From SAR 1,299'),
            'billing' => $t('تفعيل + متابعة سنوية', 'Activation + annual follow-up'),
            'summary' => $t(
                'تهيئة ورفع بيانات النزلاء وربط النظام مع متطلبات شموس التشغيلية.',
                'Enable guest-data submission workflows and connect the PMS with Shomoos requirements.'
            ),
            'prices' => [
                ['label' => $t('رسوم التفعيل', 'Activation fee'), 'value' => $t('1,299 ريال', 'SAR 1,299')],
                ['label' => $t('المتابعة السنوية', 'Annual follow-up'), 'value' => $t('699 ريال', 'SAR 699')],
                ['label' => $t('مدة التنفيذ', 'Lead time'), 'value' => $t('4-7 أيام عمل', '4-7 business days')],
            ],
            'features' => [
                $t('إعداد الحقول المطلوبة للربط', 'Setup of required guest-data fields'),
                $t('مراجعة جاهزية الهوية والجنسية والمهنة', 'Readiness review for ID, nationality, and occupation data'),
                $t('اختبار الإرسال قبل التفعيل الفعلي', 'Submission testing before activation'),
            ],
            'steps' => [
                $t('يتم التواصل مع الشركة المزودة لطلب خدمة شموس.', 'The property contacts the provider to request Shomoos service.'),
                $t('يتم التأكد من المتطلبات التنظيمية والحسابات ذات العلاقة.', 'Regulatory requirements and related accounts are confirmed.'),
                $t('يتم تنفيذ الربط ثم اختبار الإرسال واعتماده.', 'The integration is implemented, tested, and approved.'),
            ],
        ],
        [
            'id' => 'sms',
            'icon' => 'fa-message',
            'accent' => '#7c3aed',
            'title' => $t('اشتراك الرسائل النصية', 'SMS Subscription'),
            'subtitle' => $t('رسائل الحجز والوصول والتنبيهات', 'Booking, arrival, and notification SMS'),
            'price' => $t('ابتداء من 250 ريال', 'From SAR 250'),
            'billing' => $t('باقات شهرية', 'Monthly bundles'),
            'summary' => $t(
                'شراء باقات رسائل لتفعيل الإشعارات والتواصل مع النزلاء حسب حجم الاستخدام.',
                'Purchase SMS bundles for guest communication, alerts, and operational notifications.'
            ),
            'prices' => [
                ['label' => $t('باقة أساسية', 'Starter bundle'), 'value' => $t('250 ريال / 1,000 رسالة', 'SAR 250 / 1,000 SMS')],
                ['label' => $t('باقة تشغيلية', 'Operational bundle'), 'value' => $t('600 ريال / 3,000 رسالة', 'SAR 600 / 3,000 SMS')],
                ['label' => $t('مدة التفعيل', 'Lead time'), 'value' => $t('يوم عمل واحد', '1 business day')],
            ],
            'features' => [
                $t('رسائل تأكيد الحجز والوصول والمغادرة', 'Booking, arrival, and departure notifications'),
                $t('تنبيهات داخلية ورسائل تشغيلية', 'Internal operational and reminder messages'),
                $t('إمكانية الترقية إلى باقات أعلى حسب الاستهلاك', 'Upgrade path to larger bundles as usage grows'),
            ],
            'steps' => [
                $t('يختار مالك النظام الباقة المناسبة للاستخدام.', 'The owner chooses the SMS bundle that fits usage.'),
                $t('يتم تأكيد السعر النهائي من الشركة المزودة.', 'Final commercial pricing is confirmed by the provider.'),
                $t('يتم تفعيل الرصيد والبدء في استخدام الإشعارات.', 'Credits are activated and SMS sending can begin.'),
            ],
        ],
    ];

    $propertyDisplayName = old(
        'property_name',
        $isArabic
            ? ($property?->property_name_ar ?? $property?->property_name_en ?? '')
            : ($property?->property_name_en ?? $property?->property_name_ar ?? '')
    );
    $contactDisplayName = old('contact_name', $user?->name ?? '');
    $contactDisplayEmail = old('contact_email', $user?->email ?? '');
    $contactDisplayPhone = old('contact_phone', data_get($user?->contact_info ?? [], 'phone', $property?->mobile ?? $property?->phone ?? ''));
    $integrationCatalog = collect($integrations)->keyBy('id')->toArray();
@endphp

@section('title', 'Setup Subscription')

<style>
    .subscription-hub {
        --surface: #ffffff;
        --surface-muted: #f8fafc;
        --border: {{ $theme->card_border_color ?: '#dbe2ea' }};
        --title: #0f172a;
        --text: #334155;
        --text-strong: #111827;
        --primary: {{ $theme->button_primary_color }};
    }

    .subscription-hero {
        background: linear-gradient(135deg, rgba(15, 118, 110, 0.08), rgba(29, 78, 216, 0.1));
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 24px;
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(260px, 0.9fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .subscription-eyebrow {
        font-size: 13px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 8px;
    }

    .subscription-hero h2,
    .integration-card h3,
    .integration-panel h3,
    .panel-block h4 {
        color: var(--title);
    }

    .subscription-hero h2 {
        font-size: 30px;
        margin-bottom: 10px;
    }

    .subscription-hero p,
    .integration-card p,
    .integration-panel p,
    .panel-list li,
    .contact-note,
    .summary-badge {
        color: var(--text);
    }

    .hero-meta {
        background: var(--surface-muted);
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 20px;
        padding: 20px;
        display: grid;
        gap: 14px;
    }

    .summary-badge {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 16px;
    }

    .summary-badge i {
        color: var(--primary);
    }

    .integration-list {
        display: grid;
        gap: 14px;
    }

    .integration-card,
    .integration-panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 22px;
        color: var(--text);
    }

    .integration-card {
        width: 100%;
        padding: 18px;
        display: grid;
        grid-template-columns: 52px minmax(0, 1fr);
        gap: 14px;
        text-align: start;
        transition: 0.2s ease;
        cursor: pointer;
    }

    .integration-card.is-active {
        border-color: var(--accent);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
        transform: translateY(-1px);
    }

    .integration-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        color: #fff;
        background: var(--accent);
        font-size: 18px;
    }

    .integration-card__top,
    .panel-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
    }

    .integration-chip {
        font-size: 12px;
        color: var(--accent);
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid currentColor;
        border-radius: 999px;
        padding: 4px 10px;
        white-space: nowrap;
    }

    .integration-price {
        font-weight: 700;
        color: var(--accent);
    }

    .integration-panel {
        display: none;
        padding: 24px;
    }

    .integration-panel.is-active {
        display: block;
    }

    .panel-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
        margin-top: 20px;
    }

    .panel-block {
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 18px;
        height: 100%;
        background: var(--surface-muted);
    }

    .price-grid {
        display: grid;
        gap: 12px;
    }

    .price-item {
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 14px;
        display: flex;
        justify-content: space-between;
        gap: 12px;
        background: #ffffff;
    }

    .price-item strong {
        color: var(--text-strong);
    }

    .price-item span {
        color: var(--text-strong);
    }

    .panel-list {
        margin: 0;
        padding-inline-start: 18px;
        display: grid;
        gap: 10px;
    }

    .contact-strip {
        margin-top: 18px;
        padding: 16px 18px;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.04), rgba(59, 130, 246, 0.08));
        border-inline-start: 4px solid var(--accent);
    }

    .contact-strip strong {
        display: block;
        margin-bottom: 6px;
        color: var(--title);
    }

    .subscription-request-action {
        margin-top: 14px;
    }

    .subscription-request-action .btn {
        border-radius: 999px;
        padding-inline: 18px;
    }

    .subscription-request-summary {
        background: var(--surface-muted);
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 18px;
        margin-bottom: 18px;
    }

    .subscription-request-summary h5 {
        color: var(--title);
        margin-bottom: 8px;
    }

    .subscription-request-meta {
        display: grid;
        gap: 8px;
        color: var(--text);
    }

    .subscription-request-meta strong {
        color: var(--text-strong);
    }

    @media (max-width: 991px) {
        .subscription-hero,
        .panel-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@section('content')
    <main class="bg-white p-3 p-lg-4" style="border-radius: 15px;">
        <section class="subscription-hub">
            @if (session('subscription_request_success'))
                <div class="alert alert-success mb-4">
                    {{ session('subscription_request_success') }}
                </div>
            @endif

            @if (session('subscription_request_error'))
                <div class="alert alert-danger mb-4">
                    {{ session('subscription_request_error') }}
                </div>
            @endif

            <div class="subscription-hero">
                <div>
                    <div class="subscription-eyebrow">{{ $t('الإعدادات / الاشتراكات', 'Setup / Subscriptions') }}</div>
                    <h2>{{ $t('اشتراكات التكامل والخدمات الإضافية', 'Integration & Add-on Subscriptions') }}</h2>
                    <p class="mb-0">
                        {{ $t('اختر أي تكامل من القائمة لعرض الوصف والسعر وطريقة التفعيل. هذه الشاشة تعريفية فقط حاليا، والتفعيل الفعلي يتم بعد تواصل مالك النظام مع الشركة المزودة واعتماد العرض التجاري.', 'Select any integration to view the description, pricing, and activation path. This screen is informational only for now, and actual activation happens after the system owner contacts the provider and confirms the commercial offer.') }}
                    </p>
                </div>

                <div class="hero-meta">
                    <div class="summary-badge">
                        <i class="fas fa-layer-group"></i>
                        <span>{{ $t('4 خدمات جاهزة للعرض والاشتراك', '4 services ready for subscription review') }}</span>
                    </div>
                    <div class="summary-badge">
                        <i class="fas fa-phone-volume"></i>
                        <span>{{ $t('التفعيل يتم بالتواصل مع الشركة المطورة', 'Activation is handled through the provider team') }}</span>
                    </div>
                    <div class="summary-badge">
                        <i class="fas fa-receipt"></i>
                        <span>{{ $t('الأسعار المعروضة استرشادية وقابلة للتأكيد التجاري', 'Displayed prices are indicative and subject to commercial confirmation') }}</span>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-4">
                    <div class="integration-list">
                        @foreach ($integrations as $integration)
                            <button type="button"
                                class="integration-card {{ $loop->first ? 'is-active' : '' }}"
                                data-subscription-tab="{{ $integration['id'] }}"
                                style="--accent: {{ $integration['accent'] }};">
                                <div class="integration-icon">
                                    <i class="fas {{ $integration['icon'] }}"></i>
                                </div>
                                <div>
                                    <div class="integration-card__top">
                                        <div>
                                            <h3 class="h5 mb-1">{{ $integration['title'] }}</h3>
                                            <p class="mb-2">{{ $integration['subtitle'] }}</p>
                                        </div>
                                        <span class="integration-chip">{{ $integration['billing'] }}</span>
                                    </div>
                                    <div class="integration-price">{{ $integration['price'] }}</div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="col-xl-8">
                    @foreach ($integrations as $integration)
                        <article class="integration-panel {{ $loop->first ? 'is-active' : '' }}"
                            data-subscription-panel="{{ $integration['id'] }}"
                            style="--accent: {{ $integration['accent'] }};">
                            <div class="panel-header">
                                <div>
                                    <h3 class="mb-2">{{ $integration['title'] }}</h3>
                                    <p class="mb-0">{{ $integration['summary'] }}</p>
                                </div>
                                <span class="integration-chip">{{ $integration['billing'] }}</span>
                            </div>

                            <div class="panel-grid">
                                <div class="panel-block">
                                    <h4 class="h6 mb-3">{{ $t('يشمل الخدمة', 'What is included') }}</h4>
                                    <ul class="panel-list">
                                        @foreach ($integration['features'] as $feature)
                                            <li>{{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="panel-block">
                                    <h4 class="h6 mb-3">{{ $t('الأسعار والتفاصيل', 'Pricing and scope') }}</h4>
                                    <div class="price-grid">
                                        @foreach ($integration['prices'] as $price)
                                            <div class="price-item">
                                                <strong>{{ $price['label'] }}</strong>
                                                <span>{{ $price['value'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="panel-block">
                                    <h4 class="h6 mb-3">{{ $t('خطوات التفعيل', 'Activation flow') }}</h4>
                                    <ul class="panel-list">
                                        @foreach ($integration['steps'] as $step)
                                            <li>{{ $step }}</li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="panel-block">
                                    <h4 class="h6 mb-3">{{ $t('ملاحظة مهمة', 'Important note') }}</h4>
                                    <p class="mb-0">
                                        {{ $t('لا يتم تفعيل هذه الخدمة مباشرة من هذه الصفحة حاليا. الغرض منها عرض الخدمة وباقاتها، ثم يتم اعتماد الاشتراك والتجهيز عبر الشركة التي قامت ببناء النظام.', 'This page does not activate the service directly. It presents the integration and subscription options, while purchase and activation are completed through the company that built the system.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="contact-strip">
                                <strong>{{ $t('التواصل التجاري والتنفيذي', 'Commercial and implementation contact') }}</strong>
                                <div class="contact-note">
                                    {{ $t('بعد اختيار الخدمة المناسبة، يتواصل مالك المنشأة مع مزود النظام لاعتماد السعر النهائي، جدول التنفيذ، ومتطلبات التفعيل قبل بدء الربط.', 'After reviewing the relevant service, the property owner contacts the system provider to confirm the final price, delivery timeline, and activation requirements before implementation starts.') }}
                                </div>
                                <div class="subscription-request-action">
                                    <button type="button" class="btn btn-primary subscription-request-btn"
                                        data-bs-toggle="modal" data-bs-target="#subscriptionRequestModal"
                                        data-integration-id="{{ $integration['id'] }}">
                                        {{ $t('Ø·Ù„Ø¨ Ø§Ù„Ø§Ø´ØªØ±Ø§Ùƒ', 'Request Subscription') }}
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="modal fade" id="subscriptionRequestModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('setup-sidebar.setup_subscription.send') }}">
                        @csrf
                        <input type="hidden" name="integration_key" id="subscription_integration_key"
                            value="{{ old('integration_key') }}">

                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title mb-1">{{ $t('طلب اشتراك جديد', 'New Subscription Request') }}</h5>
                                <div class="text-muted small">{{ $t('سيتم إرسال الطلب إلى', 'The request will be sent to') }} info@b-it.co</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="subscription-request-summary">
                                <h5 id="subscription_modal_integration_name">Subscription</h5>
                                <div class="subscription-request-meta">
                                    <div><strong>{{ $t('السعر الاسترشادي', 'Indicative price') }}:</strong> <span id="subscription_modal_price"></span></div>
                                    <div><strong>{{ $t('نوع الاشتراك', 'Billing type') }}:</strong> <span id="subscription_modal_billing"></span></div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ $t('اسم المنشأة', 'Property name') }}</label>
                                    <input type="text" name="property_name"
                                        class="form-control @error('property_name') is-invalid @enderror"
                                        value="{{ $propertyDisplayName }}">
                                    @error('property_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ $t('اسم المسؤول', 'Contact name') }}</label>
                                    <input type="text" name="contact_name"
                                        class="form-control @error('contact_name') is-invalid @enderror"
                                        value="{{ $contactDisplayName }}">
                                    @error('contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ $t('البريد الإلكتروني', 'Contact email') }}</label>
                                    <input type="email" name="contact_email"
                                        class="form-control @error('contact_email') is-invalid @enderror"
                                        value="{{ $contactDisplayEmail }}">
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ $t('رقم الجوال', 'Contact phone') }}</label>
                                    <input type="text" name="contact_phone"
                                        class="form-control @error('contact_phone') is-invalid @enderror"
                                        value="{{ $contactDisplayPhone }}">
                                    @error('contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ $t('الباقة أو الطلب المفضل', 'Preferred plan or request') }}</label>
                                    <input type="text" name="requested_plan"
                                        class="form-control @error('requested_plan') is-invalid @enderror"
                                        value="{{ old('requested_plan') }}"
                                        placeholder="{{ $t('مثال: أحتاج العرض السنوي أو عرض مخصص', 'Example: I need the annual package or a custom commercial offer') }}">
                                    @error('requested_plan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ $t('ملاحظات إضافية', 'Additional notes') }}</label>
                                    <textarea name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror"
                                        placeholder="{{ $t('أضف أي ملاحظات تخص التشغيل أو موعد التواصل أو متطلبات التفعيل', 'Add any operational notes, preferred contact time, or activation requirements') }}">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                {{ $t('إلغاء', 'Cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                {{ $t('إرسال الطلب', 'Send Request') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('[data-subscription-tab]');
            const panels = document.querySelectorAll('[data-subscription-panel]');
            const requestButtons = document.querySelectorAll('.subscription-request-btn');
            const modalElement = document.getElementById('subscriptionRequestModal');
            const integrationCatalog = @json($integrationCatalog);
            const integrationInput = document.getElementById('subscription_integration_key');
            const integrationName = document.getElementById('subscription_modal_integration_name');
            const integrationPrice = document.getElementById('subscription_modal_price');
            const integrationBilling = document.getElementById('subscription_modal_billing');
            const reopenIntegration = @json(session('subscription_modal_key', old('integration_key')));
            const shouldReopenModal = @json($errors->any() || session()->has('subscription_request_error'));
            const requestButtonLabel = document.documentElement.lang === 'ar'
                ? '\u0637\u0644\u0628 \u0627\u0644\u0627\u0634\u062A\u0631\u0627\u0643'
                : 'Request Subscription';

            function activateIntegration(target) {
                if (!target) {
                    return;
                }

                tabs.forEach(function(item) {
                    item.classList.remove('is-active');
                });

                panels.forEach(function(panel) {
                    panel.classList.remove('is-active');
                });

                const activeTab = document.querySelector('[data-subscription-tab="' + target + '"]');
                const activePanel = document.querySelector('[data-subscription-panel="' + target + '"]');

                if (activeTab) {
                    activeTab.classList.add('is-active');
                }

                if (activePanel) {
                    activePanel.classList.add('is-active');
                }
            }

            function fillRequestModal(target) {
                const integration = integrationCatalog[target];

                if (!integration) {
                    return;
                }

                integrationInput.value = target;
                integrationName.textContent = integration.title;
                integrationPrice.textContent = integration.price;
                integrationBilling.textContent = integration.billing;
                activateIntegration(target);
            }

            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    activateIntegration(tab.getAttribute('data-subscription-tab'));
                });
            });

            requestButtons.forEach(function(button) {
                button.textContent = requestButtonLabel;

                button.addEventListener('click', function() {
                    fillRequestModal(button.getAttribute('data-integration-id'));
                });
            });

            if (reopenIntegration) {
                fillRequestModal(reopenIntegration);
            }

            if (shouldReopenModal && reopenIntegration && modalElement && window.bootstrap) {
                const requestModal = new bootstrap.Modal(modalElement);
                requestModal.show();
            }
        });
    </script>
@endpush

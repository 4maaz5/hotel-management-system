<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device=device-width, initial-scale=1.0">
    <title>Housekeeping Status Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-only { display: block !important; }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }

        .print-container {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
        }

        .box {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 18px;
        }

        .box-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 13px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 6px;
        }

        .property-lines {
            line-height: 1.4;
            margin-top: 8px;
        }

        .property-line {
            margin-bottom: 4px;
        }

        .property-meta {
            font-size: 11px;
            color: #555;
        }

        .arabic-text {
            direction: rtl;
            unicode-bidi: embed;
            text-align: right;
            display: none;
        }

        .english-text {
            direction: ltr;
            unicode-bidi: embed;
            text-align: left;
        }

        .lang-ar .arabic-text {
            display: inline;
        }

        .lang-ar .english-text {
            display: none;
        }
        
        /* Both mode - show English then Arabic below */
        .lang-both .english-text,
        .lang-both .arabic-text {
            display: inline;
        }
        
        .lang-both .arabic-text:before {
            content: " - ";
        }
        
        .lang-both {
            direction: ltr;
        }
        
        .lang-both .ar-block {
            display: block;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px dashed #ccc;
        }
        
        .en-block {
            display: block;
        }
        
        .ar-block {
            display: none;
        }

        .details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 12px;
        }

        .details th,
        .details td {
            padding: 8px 6px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }

        .details th {
            background: #f4f4f4;
            width: 120px;
        }

        .badge-clean { background-color: #d1fae5 !important; color: #065f46 !important; }
        .badge-dirty { background-color: #fef3c7 !important; color: #92400e !important; }
        .badge-inspected { background-color: #dbeafe !important; color: #1e40af !important; }
        .badge-out_of_service { background-color: #e5e7eb !important; color: #374151 !important; }

        .status-cell { font-weight: bold; }

        .summary-cards {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-card {
            flex: 1;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
        }

        .summary-card h4 {
            margin: 0;
            font-size: 24px;
        }

        .summary-card small {
            font-size: 11px;
            color: #555;
        }

        .footer-section {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #555;
        }
    </style>
</head>
<body>
    @php
        $letterHead = $printingOption?->letter_head ?? $globalSetting?->letter_head ?? false;
        $blankPaper = $printingOption?->blank_paper ?? $globalSetting?->blank_paper ?? false;
        $showPropertyInfo = $letterHead && !$blankPaper;
    @endphp

    <div class="print-container" id="printContent">
        <!-- Property Info Header -->
        @if($showPropertyInfo)
        <div class="box">
            <div class="box-title" data-en="Property Information" data-ar="معلومات العقار">Property Information</div>
            <div class="property-lines">
                <div class="property-line">
                    <span class="english-text">{{ optional($property)->property_name_en ?? 'Property Name' }}</span>
                    <span class="arabic-text">{{ optional($property)->property_name_ar ?? 'اسم العقار' }}</span>
                </div>
                <div class="property-line">
                    <span class="english-text">{{ optional($property)->address ?? 'Address' }}</span>
                    <span class="arabic-text">{{ optional($property)->address_ar ?? 'العنوان' }}</span>
                </div>
                <div class="property-line">
                    <span class="english-text">{{ optional($property)->property_code ?? '-' }}</span>
                </div>
                <div class="property-meta">
                    <span class="english-text">{{ __('dashboard.date') }}: {{ now()->format('Y-m-d') }}</span>
                    <span class="arabic-text">التاريخ: {{ now()->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Report Title -->
        <div class="box">
            <div class="box-title" data-en="Housekeeping Status Report" data-ar="تقرير حالة التدبير المنزلي">Housekeeping Status Report</div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card" style="background-color: #fee2e2;">
                <h4>{{ $dirtyCount }}</h4>
                <span class="english-text">Dirty</span>
                <span class="arabic-text">متسخ</span>
            </div>
            <div class="summary-card" style="background-color: #d1fae5;">
                <h4>{{ $cleanCount }}</h4>
                <span class="english-text">Clean</span>
                <span class="arabic-text">نظيف</span>
            </div>
            <div class="summary-card" style="background-color: #dbeafe;">
                <h4>{{ $inspectedCount }}</h4>
                <span class="english-text">Inspected</span>
                <span class="arabic-text">مفحوص</span>
            </div>
            <div class="summary-card" style="background-color: #f3f4f6;">
                <h4>{{ $outOfServiceCount }}</h4>
                <span class="english-text">Out of Service</span>
                <span class="arabic-text">خارج الخدمة</span>
            </div>
        </div>

        <!-- Filters Applied -->
        @if($filtersApplied)
        <div class="box">
            <div class="box-title" data-en="Filters Applied" data-ar="الفلاتر المطبقة">Filters Applied</div>
            <p style="font-size: 12px; margin: 0;">
                <span class="english-text">
                    @if(request('housekeeping_status')) HK Status: {{ request('housekeeping_status') }}; @endif
                    @if(request('floor_id')) Floor: {{ $floors->find(request('floor_id'))->name ?? request('floor_id') }}; @endif
                    @if(request('unit_type_id')) Unit Type: {{ $unitTypes->find(request('unit_type_id'))->name ?? request('unit_type_id') }}; @endif
                    @if(request('unit_number')) Unit No: {{ request('unit_number') }}; @endif
                </span>
                <span class="arabic-text">
                    @if(request('housekeeping_status')) حالة HK: {{ request('housekeeping_status') }}; @endif
                    @if(request('floor_id')) الطابق: {{ $floors->find(request('floor_id'))->name ?? request('floor_id') }}; @endif
                    @if(request('unit_type_id')) نوع الوحدة: {{ $unitTypes->find(request('unit_type_id'))->name ?? request('unit_type_id') }}; @endif
                    @if(request('unit_number')) رقم الوحدة: {{ request('unit_number') }}; @endif
                </span>
            </p>
        </div>
        @endif

        <!-- Housekeeping Table -->
        <table class="details">
            <thead>
                <tr>
                    <th data-en="#" data-ar="#">#</th>
                    <th data-en="Unit No." data-ar="رقم الوحدة">Unit No.</th>
                    <th data-en="Unit Type" data-ar="نوع الوحدة">Unit Type</th>
                    <th data-en="Floor" data-ar="الطابق">Floor</th>
                    <th data-en="HK Status" data-ar="حالة التدبير">HK Status</th>
                    <th data-en="Occupancy" data-ar="الإشغال">Occupancy</th>
                    <th data-en="Guest Name" data-ar="اسم الضيف">Guest Name</th>
                    <th data-en="Check-in" data-ar="تسجيل الدخول">Check-in</th>
                    <th data-en="Check-out" data-ar="تسجيل الخروج">Check-out</th>
                    <th data-en="Notes" data-ar="ملاحظات">Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($unitsWithStatus as $index => $unit)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="status-cell">{{ $unit->unit_number }}</td>
                        <td>
                            <span class="english-text">{{ $unit->unitType->name ?? '-' }}</span>
                            <span class="arabic-text">{{ $unit->unitType->name_ar ?? $unit->unitType->name ?? '-' }}</span>
                        </td>
                        <td>{{ $unit->floor->name ?? '-' }}</td>
                        <td>
                            @if($unit->housekeeping_status == 'clean')
                                <span class="badge badge-clean">
                                    <span class="english-text">Clean</span>
                                    <span class="arabic-text">نظيف</span>
                                </span>
                            @elseif($unit->housekeeping_status == 'dirty')
                                <span class="badge badge-dirty">
                                    <span class="english-text">Dirty</span>
                                    <span class="arabic-text">متسخ</span>
                                </span>
                            @elseif($unit->housekeeping_status == 'inspected')
                                <span class="badge badge-inspected">
                                    <span class="english-text">Inspected</span>
                                    <span class="arabic-text">مفحوص</span>
                                </span>
                            @elseif($unit->housekeeping_status == 'out_of_service')
                                <span class="badge badge-out_of_service">
                                    <span class="english-text">Out of Service</span>
                                    <span class="arabic-text">خارج الخدمة</span>
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="english-text">
                                @if($unit->occupancy_status == 'vacant') Vacant
                                @elseif($unit->occupancy_status == 'occupied') Occupied
                                @elseif($unit->occupancy_status == 'check_out_today') Check-out Today
                                @endif
                            </span>
                            <span class="arabic-text">
                                @if($unit->occupancy_status == 'vacant') شاغر
                                @elseif($unit->occupancy_status == 'occupied') مشغول
                                @elseif($unit->occupancy_status == 'check_out_today') مغادرة اليوم
                                @endif
                            </span>
                        </td>
                        <td>{{ $unit->current_guest ?? '-' }}</td>
                        <td>{{ $unit->check_in_date ?? '-' }}</td>
                        <td>{{ $unit->check_out_date ?? '-' }}</td>
                        <td>{{ $unit->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center;">
                            <span class="english-text">No records found</span>
                            <span class="arabic-text">لا توجد سجلات</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary Footer -->
        <div class="footer-section">
            <div class="row">
                <div class="col-6">
                    <span class="english-text">
                        <strong>Total Units:</strong> {{ $unitsWithStatus->count() }} | 
                        <strong>Generated:</strong> {{ now()->format('Y-m-d H:i:s') }}
                    </span>
                    <span class="arabic-text">
                        <strong>إجمالي الوحدات:</strong> {{ $unitsWithStatus->count() }} | 
                        <strong>تاريخ الإنشاء:</strong> {{ now()->format('Y-m-d H:i:s') }}
                    </span>
                </div>
                <div class="col-6 text-end">
                    <span class="english-text">Housekeeping Report</span>
                    <span class="arabic-text">تقرير التدبير المنزلي</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        var currentLang = 'en';

        function switchLanguage(lang) {
            currentLang = lang;
            const content = document.getElementById('printContent');
            
            if (!content) {
                return;
            }
            
            // Reset all
            content.classList.remove('lang-ar', 'lang-both');
            
            // Hide all blocks first
            document.querySelectorAll('.en-block').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.ar-block').forEach(el => el.style.display = 'none');
            
            if (lang === 'ar') {
                // Arabic only - RTL
                content.classList.add('lang-ar');
                document.documentElement.lang = 'ar';
                document.documentElement.dir = 'rtl';
                
                document.querySelectorAll('.english-text').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.arabic-text').forEach(el => el.style.display = 'inline');
                document.querySelectorAll('.ar-block').forEach(el => el.style.display = 'block');
                
                document.querySelectorAll('.box-title').forEach(el => {
                    if (el.dataset.ar) el.textContent = el.dataset.ar;
                });
                
                document.querySelectorAll('.details th').forEach(el => {
                    if (el.dataset.ar) el.textContent = el.dataset.ar;
                });
                
            } else if (lang === 'both') {
                // Both - English first, then Arabic below
                content.classList.add('lang-both');
                document.documentElement.lang = 'en';
                document.documentElement.dir = 'ltr';
                
                // Show both inline
                document.querySelectorAll('.english-text').forEach(el => el.style.display = 'inline');
                document.querySelectorAll('.arabic-text').forEach(el => el.style.display = 'inline');
                
                document.querySelectorAll('.box-title').forEach(el => {
                    if (el.dataset.en) el.textContent = el.dataset.en + ' / ' + (el.dataset.ar || '');
                });
                
                document.querySelectorAll('.details th').forEach(el => {
                    if (el.dataset.en) el.textContent = el.dataset.en + ' / ' + (el.dataset.ar || '');
                });
                
            } else {
                // English only - LTR
                document.documentElement.lang = 'en';
                document.documentElement.dir = 'ltr';
                
                document.querySelectorAll('.english-text').forEach(el => el.style.display = 'inline');
                document.querySelectorAll('.arabic-text').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.en-block').forEach(el => el.style.display = 'block');
                
                document.querySelectorAll('.box-title').forEach(el => {
                    if (el.dataset.en) el.textContent = el.dataset.en;
                });
                
                document.querySelectorAll('.details th').forEach(el => {
                    if (el.dataset.en) el.textContent = el.dataset.en;
                });
            }
        }

        function printContract() {
            window.print();
        }

        // Initialize with English
        window.onload = function() {
            switchLanguage('en');
        };
    </script>
</body>
</html>

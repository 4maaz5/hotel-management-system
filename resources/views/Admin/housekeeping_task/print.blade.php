<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Housekeeping Tasks Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
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
        .lang-ar .arabic-text { display: inline; }
        .lang-ar .english-text { display: none; }
        
        .details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 11px;
        }
        .details th, .details td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }
        .details th { background: #f4f4f4; }
    </style>
</head>
<body>
    @php
        $letterHead = $printingOption?->letter_head ?? $globalSetting?->letter_head ?? false;
        $blankPaper = $printingOption?->blank_paper ?? $globalSetting?->blank_paper ?? false;
        $showPropertyInfo = $letterHead && !$blankPaper;
    @endphp

    <div class="print-container" id="printContent">
        @if($showPropertyInfo)
        <div class="box">
            <div class="box-title" data-en="Property Information" data-ar="معلومات العقار">Property Information</div>
            <div>
                <span class="english-text">{{ $property->property_name_en ?? 'Property Name' }}</span>
                <span class="arabic-text">{{ $property->property_name_ar ?? 'اسم العقار' }}</span>
                <br>
                <span class="english-text">{{ $property->address_en ?? 'Address' }}</span>
                <span class="arabic-text">{{ $property->address_ar ?? 'العنوان' }}</span>
                <br>
                <span class="english-text">{{ __('dashboard.date') }}: {{ now()->format('Y-m-d') }}</span>
                <span class="arabic-text">التاريخ: {{ now()->format('Y-m-d') }}</span>
            </div>
        </div>
        @endif

        <div class="box">
            <div class="box-title" data-en="Housekeeping Tasks Report" data-ar="تقرير مهام التدبير المنزلي">Housekeeping Tasks Report</div>
        </div>

        @if($filtersApplied)
        <div class="box">
            <div class="box-title" data-en="Filters Applied" data-ar="الفلاتر المطبقة">Filters Applied</div>
        </div>
        @endif

        <table class="details">
            <thead>
                <tr>
                    <th data-en="#" data-ar="#">#</th>
                    <th data-en="Date" data-ar="التاريخ">Date</th>
                    <th data-en="Unit/Facility" data-ar="الوحدة/المرفق">Unit/Facility</th>
                    <th data-en="Task Type" data-ar="نوع المهمة">Task Type</th>
                    <th data-en="Priority" data-ar="الأولوية">Priority</th>
                    <th data-en="Assigned To" data-ar="مخصص لـ">Assigned To</th>
                    <th data-en="Status" data-ar="الحالة">Status</th>
                    <th data-en="Description" data-ar="الوصف">Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $index => $task)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $task->created_at->format('Y-m-d') }}</td>
                        <td>
                            <span class="english-text">
                                @if($task->task_type == 'unit')
                                    {{ $task->unit->unit_number ?? '-' }} - {{ $task->unit->unitType->name ?? '-' }}
                                @else
                                    {{ $task->propertyFacility->name ?? '-' }}
                                @endif
                            </span>
                            <span class="arabic-text">
                                @if($task->task_type == 'unit')
                                    {{ $task->unit->unit_number ?? '-' }} - {{ $task->unit->unitType->name_ar ?? $task->unit->unitType->name ?? '-' }}
                                @else
                                    {{ $task->propertyFacility->name ?? '-' }}
                                @endif
                            </span>
                        </td>
                        <td>
                            <span class="english-text">{{ $task->taskType->name ?? '-' }}</span>
                            <span class="arabic-text">{{ $task->taskType->name ?? '-' }}</span>
                        </td>
                        <td>
                            @if($task->priority == 'high')
                                <span class="english-text">High</span><span class="arabic-text">عالي</span>
                            @elseif($task->priority == 'medium')
                                <span class="english-text">Medium</span><span class="arabic-text">متوسط</span>
                            @else
                                <span class="english-text">Low</span><span class="arabic-text">منخفض</span>
                            @endif
                        </td>
                        <td>{{ $task->housekeeper->user->name ?? '-' }}</td>
                        <td>
                            <span class="english-text">
                                @if($task->status == 'pending') Pending
                                @elseif($task->status == 'in_progress') In Progress
                                @elseif($task->status == 'completed') Completed
                                @else Cancelled
                                @endif
                            </span>
                            <span class="arabic-text">
                                @if($task->status == 'pending') معلق
                                @elseif($task->status == 'in_progress') قيد التنفيذ
                                @elseif($task->status == 'completed') مكتمل
                                @else ملغي
                                @endif
                            </span>
                        </td>
                        <td>{{ $task->description ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">
                            <span class="english-text">No records found</span>
                            <span class="arabic-text">لا توجد سجلات</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 11px;">
            <span class="english-text"><strong>Total Tasks:</strong> {{ $tasks->count() }}</span>
            <span class="arabic-text"><strong>إجمالي المهام:</strong> {{ $tasks->count() }}</span>
            <span class="english-text"> | <strong>Generated:</strong> {{ now()->format('Y-m-d H:i:s') }}</span>
            <span class="arabic-text"> | <strong>تاريخ الإنشاء:</strong> {{ now()->format('Y-m-d H:i:s') }}</span>
        </div>
    </div>

    <script>
        var currentLang = 'en';

        function switchLanguage(lang) {
            currentLang = lang;
            const content = document.getElementById('printContent');
            
            if (!content) return;
            
            content.classList.remove('lang-ar');
            
            if (lang === 'ar') {
                content.classList.add('lang-ar');
                document.documentElement.lang = 'ar';
                document.documentElement.dir = 'rtl';
                document.querySelectorAll('.english-text').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.arabic-text').forEach(el => el.style.display = 'inline');
                document.querySelectorAll('.box-title').forEach(el => {
                    if (el.dataset.ar) el.textContent = el.dataset.ar;
                });
                document.querySelectorAll('.details th').forEach(el => {
                    if (el.dataset.ar) el.textContent = el.dataset.ar;
                });
            } else if (lang === 'both') {
                document.documentElement.lang = 'en';
                document.documentElement.dir = 'ltr';
                document.querySelectorAll('.english-text').forEach(el => el.style.display = 'inline');
                document.querySelectorAll('.arabic-text').forEach(el => el.style.display = 'inline');
                document.querySelectorAll('.box-title').forEach(el => {
                    if (el.dataset.en) el.textContent = el.dataset.en + ' / ' + (el.dataset.ar || '');
                });
                document.querySelectorAll('.details th').forEach(el => {
                    if (el.dataset.en) el.textContent = el.dataset.en + ' / ' + (el.dataset.ar || '');
                });
            } else {
                document.documentElement.lang = 'en';
                document.documentElement.dir = 'ltr';
                document.querySelectorAll('.english-text').forEach(el => el.style.display = 'inline');
                document.querySelectorAll('.arabic-text').forEach(el => el.style.display = 'none');
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

        window.onload = function() {
            switchLanguage('en');
        };
    </script>
</body>
</html>

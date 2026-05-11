<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8" />
    <title>Contract</title>
    <style>
        * {
            box-sizing: border-box;
        }
        
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .print-content { 
                position: static !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
        
        body {
            font-family: "DejaVu Sans", "Arial", sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }

        .contract-container {
            max-width: 800px;
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
        }

        .english-text {
            direction: ltr;
            unicode-bidi: embed;
            text-align: left;
        }

        .details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
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
            width: 170px;
        }

        .terms {
            font-size: 11px;
            margin-top: 12px;
        }

        .terms ol {
            padding-left: 16px;
            padding-right: auto;
        }

        .terms li {
            margin-bottom: 8px;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #555;
        }

        .footer .row {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
        }

        .footer .row div {
            width: 48%;
        }

        .small-text {
            font-size: 10px;
            color: #555;
        }

        .lang-ar .details th,
        .lang-ar .details td {
            text-align: right;
        }
        
        .lang-ar .terms ol {
            padding-left: auto;
            padding-right: 16px;
        }

        .contract-section {
            page-break-after: always;
        }

        .contract-section:last-child {
            page-break-after: auto;
        }
    </style>
</head>
<body>
    @php
        $letterHead = $printingOption?->letter_head ?? false;
        $blankPaper = $printingOption?->blank_paper ?? false;
        $cashierPaper = $printingOption?->cashier_paper ?? false;
        $contractType = $contractType ?? 'double';
        $commercialDetail = $property?->commercialDetail;
        
        $showPropertyInfo = $letterHead && !$blankPaper;
        $isDoubleLang = $contractType === 'double';
    @endphp

    <div class="contract-container" id="contractContent">
        @if($showPropertyInfo)
        <div class="box property-info">
            <div class="box-title" data-en="Property Information" data-ar="معلومات العقار">Property Information</div>

            <div class="property-lines">
                <div class="property-line">
                    <span class="english-text">{{ optional($property)->property_name ?? 'Onara Stay' }}</span>
                    <span class="arabic-text" style="display:none;">{{ optional($property)->property_name_ar ?? 'أونارا استاي' }}</span>
                </div>
                <div class="property-line">
                    <span class="english-text">{{ optional($property)->address ?? 'Madinah - King Fahd' }}</span>
                    <span class="arabic-text" style="display:none;">{{ optional($property)->address_ar ?? 'المدينة المنورة - الملك فهد' }}</span>
                </div>
                <div class="property-line">{{ optional($property)->property_code ?? '1100' }} | {{ optional($property)->report_name_en ?? 'B-IT' }}</div>
                <div class="property-line">{{ optional($property)->phone ?? '+966 14 124585858' }}</div>
                <div class="property-meta">
                    <span class="english-text">VAT No: {{ optional($commercialDetail)->vat_registration_number ?? '333333333338333' }}</span>
                    <span class="arabic-text" style="display:none;">الرقم الضريبي: {{ optional($commercialDetail)->vat_registration_number ?? '333333333338333' }}</span>
                    <br>
                    <span class="english-text">R.C: {{ optional($commercialDetail)->registration_number ?? '13364765869' }}</span>
                    <span class="arabic-text" style="display:none;">السجل التجاري: {{ optional($commercialDetail)->registration_number ?? '13364765869' }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- English Section -->
        <div class="contract-section" data-lang="en">
            @if($isDoubleLang)
            <div class="box">
                <div class="box-title">English</div>
            @endif

            <div class="box">
                <div class="box-title" data-en="Reservation Details" data-ar="تفاصيل الحجز">Reservation Details</div>

                <table class="details">
                    <tr>
                        <th data-en="From" data-ar="من">From</th>
                        <td>{{ $reservation?->check_in_date?->format('Y/m/d') ?? 'DD/MM/YYYY' }}</td>
                        <th data-en="To" data-ar="إلى">To</th>
                        <td>{{ $reservation?->check_out_date?->format('Y/m/d') ?? 'DD/MM/YYYY' }}</td>
                    </tr>
                    <tr>
                        <th data-en="Nights" data-ar="الليالي">Nights</th>
                        <td>{{ $reservation?->nights ?? '0' }}</td>
                        <th data-en="Unit Type" data-ar="نوع الوحدة">Unit Type</th>
                        <td>{{ $reservation?->unit?->unitType->name ?? '---' }}</td>
                    </tr>
                    <tr>
                        <th data-en="Block" data-ar="القطاع">Block</th>
                        <td>{{ $reservation?->unit?->block?->name ?? '---' }}</td>
                        <th data-en="Contract" data-ar="العقد">Contract</th>
                        <td>{{ $reservation?->reservation_number ?? '---' }}</td>
                    </tr>
                    <tr>
                        <th data-en="Total Amount" data-ar="المبلغ الإجمالي">Total Amount</th>
                        <td>{{ number_format($reservation?->total_rent ?? 0, 2) }}</td>
                        <th data-en="Net" data-ar="الصافي">Net</th>
                        <td>{{ number_format($reservation?->grand_total ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <th data-en="Date/Time" data-ar="التاريخ/الوقت">Date/Time</th>
                        <td>{{ now()->format('Y/m/d H:i') }}</td>
                        <th data-en="Company Phone" data-ar="هاتف الشركة">Company Phone</th>
                        <td>{{ optional($property)->phone ?? '---' }}</td>
                    </tr>
                </table>
            </div>

            <div class="box">
                <div class="box-title" data-en="Guest Details" data-ar="بيانات الضيف">Guest Details</div>

                <table class="details">
                    <tr>
                        <th data-en="Customer Name" data-ar="اسم العميل">Customer Name</th>
                        <td>{{ optional($reservation?->guest)->first_name ?? '---' }} {{ optional($reservation?->guest)->last_name ?? '' }}</td>
                        <th data-en="Mobile" data-ar="الجوال">Mobile</th>
                        <td>{{ optional($reservation?->guest)->mobile ?? '---' }}</td>
                    </tr>
                    <tr>
                        <th data-en="Email" data-ar="البريد الإلكتروني">Email</th>
                        <td>{{ optional($reservation?->guest)->email ?? '---' }}</td>
                        <th data-en="Nationality" data-ar="الجنسية">Nationality</th>
                        <td>{{ optional($reservation?->guest)->nationality ?? '---' }}</td>
                    </tr>
                    <tr>
                        <th data-en="ID Number" data-ar="رقم الهوية">ID Number</th>
                        <td>{{ optional($reservation?->guest)->id_number ?? '---' }}</td>
                        <th data-en="Children" data-ar="الأطفال">Children</th>
                        <td>{{ $reservation?->children ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th data-en="Car Plate" data-ar="لوحة السيارة">Car Plate</th>
                        <td>{{ $reservation?->car_number ?? '---' }}</td>
                        <th data-en="Corporate Name" data-ar="اسم الشركة">Corporate Name</th>
                        <td>{{ optional($reservation?->corporate)->name ?? '---' }}</td>
                    </tr>
                </table>
            </div>

            <div class="box">
                <div class="box-title" data-en="Terms & Conditions" data-ar="الشروط والأحكام">Terms & Conditions</div>

                <div class="terms">
                    <ol class="terms-list">
                        @php
                            $terms = $hotelTerms ?? collect();
                        @endphp

                        @if($terms->isEmpty())
                            <li class="term-en">The Guest must pay 500 riyals as a refundable security deposit and he will be deducted from it in the case of any damage to the contents of the unit by the guest or his dependents.</li>
                            <li class="term-en">The Guest must observe the Islamic behavior and etiquette during his stay in the unit, and not allow the residence of any other persons who are not accompanying him, while observing calm and not disturbing the others.</li>
                            <li class="term-en">The Guest is responsible for the entire unit, if something is damaged, he must pay the appropriate penalty determined by the hotel management.</li>
                            <li class="term-en">Ensure that the air conditioning, lighting, and the electrical appliances are turned off when the guest leaves the unit.</li>
                            <li class="term-en">The value of the call should be paid immediately after its completion.</li>
                            <li class="term-en">Rates during the holidays and the seasons differs from regular periods.</li>
                            <li class="term-en">Check-out time is at (2) two o'clock in the afternoon, and if late, will be charged with a whole night rate.</li>
                            <li class="term-en">The rental fees should be paid in advance.</li>
                            <li class="term-en">In the case of guest absence for three days after the end of the contract, the management has the right to open the unit.</li>
                            <li class="term-en">The hotel management is not responsible for the loss of the guest's valuables inside the unit.</li>
                            <li class="term-en">The guest does not have the right to refund the rent fees in the case of departure before the end of the contracted period.</li>
                            <li class="term-en">If the guest wants to renew the period or vacate the unit, he must notify the hotel's management before an appropriate period.</li>
                            <li class="term-en">The contract will be void in case of breaching one of the mentioned terms and conditions.</li>
                        @else
                            @foreach($terms as $term)
                                @php
                                    $text = $term->description;
                                    $decoded = json_decode($text, true);
                                    $en = null;
                                    if (is_array($decoded) && isset($decoded['en'])) {
                                        $en = $decoded['en'];
                                    } else {
                                        $en = $text;
                                    }
                                @endphp
                                <li class="term-en">{!! nl2br(e($en)) !!}</li>
                            @endforeach
                        @endif
                    </ol>
                </div>
            </div>

            @if($isDoubleLang)
            </div>
            @endif
        </div>

        <!-- Arabic Section (only for double) -->
        @if($isDoubleLang)
        <div class="contract-section" data-lang="ar">
            <div class="box">
                <div class="box-title">العربية</div>

                <div class="box">
                    <div class="box-title">تفاصيل الحجز</div>

                    <table class="details">
                        <tr>
                            <th>من</th>
                            <td>{{ $reservation?->check_in_date?->format('Y/m/d') ?? 'DD/MM/YYYY' }}</td>
                            <th>إلى</th>
                            <td>{{ $reservation?->check_out_date?->format('Y/m/d') ?? 'DD/MM/YYYY' }}</td>
                        </tr>
                        <tr>
                            <th>الليالي</th>
                            <td>{{ $reservation?->nights ?? '0' }}</td>
                            <th>نوع الوحدة</th>
                            <td>{{ $reservation?->unit?->unitType->name ?? '---' }}</td>
                        </tr>
                        <tr>
                            <th>القطاع</th>
                            <td>{{ $reservation?->unit?->block?->name ?? '---' }}</td>
                            <th>العقد</th>
                            <td>{{ $reservation?->reservation_number ?? '---' }}</td>
                        </tr>
                        <tr>
                            <th>المبلغ الإجمالي</th>
                            <td>{{ number_format($reservation?->total_rent ?? 0, 2) }}</td>
                            <th>الصافي</th>
                            <td>{{ number_format($reservation?->grand_total ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>التاريخ/الوقت</th>
                            <td>{{ now()->format('Y/m/d H:i') }}</td>
                            <th>هاتف الشركة</th>
                            <td>{{ optional($property)->phone ?? '---' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="box">
                    <div class="box-title">بيانات الضيف</div>

                    <table class="details">
                        <tr>
                            <th>اسم العميل</th>
                            <td>{{ optional($reservation?->guest)->first_name ?? '---' }} {{ optional($reservation?->guest)->last_name ?? '' }}</td>
                            <th>الجوال</th>
                            <td>{{ optional($reservation?->guest)->mobile ?? '---' }}</td>
                        </tr>
                        <tr>
                            <th>البريد الإلكتروني</th>
                            <td>{{ optional($reservation?->guest)->email ?? '---' }}</td>
                            <th>الجنسية</th>
                            <td>{{ optional($reservation?->guest)->nationality ?? '---' }}</td>
                        </tr>
                        <tr>
                            <th>رقم الهوية</th>
                            <td>{{ optional($reservation?->guest)->id_number ?? '---' }}</td>
                            <th>الأطفال</th>
                            <td>{{ $reservation?->children ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th>لوحة السيارة</th>
                            <td>{{ $reservation?->car_number ?? '---' }}</td>
                            <th>اسم الشركة</th>
                            <td>{{ optional($reservation?->corporate)->name ?? '---' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="box">
                    <div class="box-title">الشروط والأحكام</div>

                    <div class="terms">
                        <ol>
                            @php
                                $terms = $hotelTerms ?? collect();
                            @endphp

                            @if($terms->isEmpty())
                                <li>يجب على النزيل دفع 500 ريال كإيداع أمن مسترد وسيتم خصم منه في حالة حدوث أي ضرر لمحتويات الوحدة من النزيل أو من يعوله.</li>
                                <li>يجب على النزيلobserv Islamic behavior and etiquette during his stay in the unit, and not allow the residence of any other persons who are not accompanying him.</li>
                                <li>النزيل مسؤول عن كامل الوحدة، إذا حدث أي ضرر يجب عليه دفع الغرامة المناسبة.</li>
                                <li>تأكد من إطفاء التكييف والإضاءة والأجهزة الكهربائية عند مغادرة النزيل للوحدة.</li>
                                <li>يجب دفع قيمة المكالمة فور انتهائها.</li>
                                <li>الأسعار خلال العطلات والمواسم تختلف عن الأسعار العادية.</li>
                                <li>موعد تسجيل الخروج هو الساعة الثانية ظهراً، وإذا تأخر سيتم فرض رسوم ليلة كاملة.</li>
                                <li>يجب دفع الإيجار مقدماً.</li>
                                <li>في حالة غياب النزيل ثلاثة أيام بعد انتهاء العقد، يحق للإدارة فتح الوحدة.</li>
                                <li>الإدارة الفندقية غير مسؤولة عن فقدان ممتلكات النزيل الثمينة داخل الوحدة.</li>
                                <li>لا يحق للنزيل استرداد رسوم الإيجار في حالة المغادرة قبل انتهاء الفترة المتعاقد عليها.</li>
                                <li>إذا أراد النزيل تجديد الفترة أو إخلاء الوحدة يجب عليه إبلاغ إدارة الفندق قبل فترة مناسبة.</li>
                                <li>يكون العقد لاغياً في حالة الإخلال بأي من الشروط والأحكام المذكورة.</li>
                            @else
                                @foreach($terms as $term)
                                    @php
                                        $text = $term->description;
                                        $decoded = json_decode($text, true);
                                        $ar = null;
                                        if (is_array($decoded) && isset($decoded['ar'])) {
                                            $ar = $decoded['ar'];
                                        } else {
                                            $ar = $text;
                                        }
                                    @endphp
                                    <li>{!! nl2br(e($ar)) !!}</li>
                                @endforeach
                            @endif
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="footer">
            <div class="row">
                <div>
                    <div class="small-text">Email: {{ optional($property)->email ?? 'aldhmi050850@gmail.com' }}</div>
                    <div class="small-text">Postal Code: {{ optional($property)->postal_code ?? '78452' }}</div>
                </div>
                <div>
                    <div class="small-text">By Printed: {{ auth()->user()?->name ?? '---' }}</div>
                    <div class="small-text">On Printed: {{ now()->format('Y-m-d H:i') }}</div>
                </div>
            </div>

            <div class="small-text" style="text-align: center; margin-top: 12px;">
                Page 1 of {{ $isDoubleLang ? 2 : 1 }}
            </div>
        </div>
    </div>

    <script>
        var currentLang = 'en';
        
        var isDoubleLang = {{ $isDoubleLang ? 'true' : 'false' }};
        var showPropertyInfo = {{ $showPropertyInfo ? 'true' : 'false' }};

        function switchLanguage(lang) {
            currentLang = lang;
            const content = document.getElementById('contractContent');
            const enSections = document.querySelectorAll('.contract-section[data-lang="en"]');
            const arSections = document.querySelectorAll('.contract-section[data-lang="ar"]');
            
            // Hide all sections first
            enSections.forEach(el => el.style.display = 'none');
            arSections.forEach(el => el.style.display = 'none');
            
            if (lang === 'both') {
                // Show both languages
                document.documentElement.lang = 'en';
                document.documentElement.dir = 'ltr';
                content.classList.remove('lang-ar');
                
                if (isDoubleLang) {
                    enSections.forEach(el => el.style.display = 'block');
                    arSections.forEach(el => el.style.display = 'block');
                } else {
                    enSections.forEach(el => el.style.display = 'block');
                }
                
                document.querySelectorAll('.english-text').forEach(el => el.style.display = '');
                document.querySelectorAll('.arabic-text').forEach(el => el.style.display = '');
                
            } else if (lang === 'ar') {
                content.classList.add('lang-ar');
                document.documentElement.lang = 'ar';
                document.documentElement.dir = 'rtl';
                
                document.querySelectorAll('.english-text').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.arabic-text').forEach(el => el.style.display = '');
                
                document.querySelectorAll('.box-title').forEach(el => {
                    if (el.dataset.ar) el.textContent = el.dataset.ar;
                });
                
                document.querySelectorAll('.details th').forEach(el => {
                    if (el.dataset.ar) el.textContent = el.dataset.ar;
                });
                
                if (isDoubleLang) {
                    arSections.forEach(el => el.style.display = 'block');
                } else {
                    enSections.forEach(el => el.style.display = 'block');
                }
            } else {
                content.classList.remove('lang-ar');
                document.documentElement.lang = 'en';
                document.documentElement.dir = 'ltr';
                
                document.querySelectorAll('.english-text').forEach(el => el.style.display = '');
                document.querySelectorAll('.arabic-text').forEach(el => el.style.display = 'none');
                
                document.querySelectorAll('.box-title').forEach(el => {
                    if (el.dataset.en) el.textContent = el.dataset.en;
                });
                
                document.querySelectorAll('.details th').forEach(el => {
                    if (el.dataset.en) el.textContent = el.dataset.en;
                });
                
                enSections.forEach(el => el.style.display = 'block');
            }
        }

        function printContract() {
            window.print();
        }
    </script>
</body>
</html>

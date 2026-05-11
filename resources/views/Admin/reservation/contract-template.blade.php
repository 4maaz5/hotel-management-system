<!DOCTYPE html>
<html lang="ar" dir="ltr">
<head>
    <meta charset="utf-8" />
    <title>Contract Template</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
        body {
            font-family: "DejaVu Sans", "Arial", sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 20px;
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

        .arabic {
            direction: rtl;
            unicode-bidi: embed;
        }

        .ltr {
            direction: ltr;
            unicode-bidi: embed;
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
            text-align: right;
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
            padding-right: 16px;
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
    </style>
</head>
<body>
    @php
        $commercialDetail = $property?->commercialDetail;
    @endphp

    <div class="box">
        <div class="box-title">Property Information</div>

        <div class="property-lines">
            <div class="property-line"><strong>{{ optional($property)->property_name_ar ?? 'أونارا استاي' }}</strong></div>
            <div class="property-line">{{ optional($property)->address_ar ?? 'المدينة المنورة - الملك فهد' }}</div>
            <div class="property-line">{{ optional($property)->property_code ?? '1100' }} | {{ optional($property)->report_name_en ?? 'B-IT' }}</div>
            <div class="property-line">{{ optional($property)->phone ?? '+966 14 124585858' }}</div>
            <div class="property-line property-meta">
                <span class="arabic">الرقم الضريبي: <span class="ltr">{{ optional($commercialDetail)->vat_registration_number ?? '333333333338333' }}</span> :No VAT</span>
                <br>
                <span class="arabic">السجل التجاري: <span class="ltr">{{ optional($commercialDetail)->registration_number ?? '13364765869' }}</span> :R.C</span>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-title">Reservation Details</div>

        <table class="details">
            <tr>
                <th>From</th>
                <td>{{ $reservation?->check_in_date?->format('Y/m/d') ?? 'DD/MM/YYYY' }}</td>
                <th>To</th>
                <td>{{ $reservation?->check_out_date?->format('Y/m/d') ?? 'DD/MM/YYYY' }}</td>
            </tr>
            <tr>
                <th>Nights</th>
                <td>{{ $reservation?->nights ?? '0' }}</td>
                <th>Unit Type</th>
                <td>{{ $reservation?->unit?->unitType->name ?? '---' }}</td>
            </tr>
            <tr>
                <th>Block</th>
                <td>{{ $reservation?->unit?->block?->name ?? '---' }}</td>
                <th>Contract</th>
                <td>{{ $reservation?->reservation_number ?? '---' }}</td>
            </tr>
            <tr>
                <th>Total Amount</th>
                <td>{{ number_format($reservation?->total_rent ?? 0, 2) }}</td>
                <th>Net</th>
                <td>{{ number_format($reservation?->grand_total ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th>Date/Time</th>
                <td>{{ now()->format('Y/m/d H:i') }}</td>
                <th>Company Phone</th>
                <td>{{ optional($property)->phone ?? '---' }}</td>
            </tr>
        </table>
    </div>

    <div class="box">
        <div class="box-title">Guest Details</div>

        <table class="details">
            <tr>
                <th>Customer Name</th>
                <td>{{ optional($reservation?->guest)->first_name ?? '---' }} {{ optional($reservation?->guest)->last_name ?? '' }}</td>
                <th>Mobile</th>
                <td>{{ optional($reservation?->guest)->mobile ?? $reservation?->guest?->mobile ?? '---' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ optional($reservation?->guest)->email ?? '---' }}</td>
                <th>Nationality</th>
                <td>{{ optional($reservation?->guest)->nationality ?? '---' }}</td>
            </tr>
            <tr>
                <th>ID Type/Number</th>
                <td>{{ optional($reservation?->guest)->id_number ?? '---' }}</td>
                <th>Dependents</th>
                <td>{{ $reservation?->children ?? 0 }}</td>
            </tr>
            <tr>
                <th>Car L Plate</th>
                <td>{{ $reservation?->car_number ?? '---' }}</td>
                <th>Corporate Name</th>
                <td>{{ optional($reservation?->corporate)->name ?? '---' }}</td>
            </tr>
        </table>
    </div>

    <div class="box">
        <div class="box-title">Terms & Conditions</div>

        <div class="terms">
            <ol>
                @php
                    $terms = $hotelTerms ?? collect();
                @endphp

                @if($terms->isEmpty())
                    <li>The Guest must pay 500 riyals as a refundable security deposit and he will be deducted from it in the case of any damage to the contents of the unit by the guest or his dependents.</li>
                    <li>The Guest must observe the Islamic behavior and etiquette during his stay in the unit, and not allow the residence of any other persons who are not accompanying him, while observing calm and not disturbing the others.</li>
                    <li>The Guest is responsible for the entire unit, if something is damaged, he must pay the appropriate penalty determined by the hotel management. also transferring the contract to another person is not allowed.</li>
                    <li>Ensure that the air conditioning, lighting, and the electrical appliances are turned off when the guest leaves the unit to prevent the occurrence of dangers - God forbid - and he will be responsible for them.</li>
                    <li>The value of the call should be paid immediately after its completion, provided that the phone of the units is not used for purposes that violate the behavior and morals, otherwise it will bear the responsibility in case of its violation.</li>
                    <li>Rates during the holidays and the seasons is differs from regular periods and should be agreed upon with the hotel management.</li>
                    <li>Check-out time is at (2) two o'clock in the afternoon, and if the is late, will be charged with a whole night rate.</li>
                    <li>The rental fees should be paid in advance.</li>
                    <li>In the case of guest absence for three days after the end of the contract, the management has the right to open the unit and dispose it and store the belongings of the guest to the warehouse without the slightest responsibility on the hotel management and the contract will be considered as void.</li>
                    <li>The hotel management is not responsible for the loss of the guest’s valuables inside the unit.</li>
                    <li>The guest does not have the right to refund the rent fees in the case of departure before the end of the contracted period.</li>
                    <li>If the guest wants to renew the period or vacate the unit, he must notify the hotel’s management before an appropriate period.</li>
                    <li>The contract will be void in case of breaching one of the mentioned terms and conditions and the hotel management has the right to cancel the contract without giving reasons.</li>
                @else
                    @foreach($terms as $term)
                        @php
                            $text = $term->description;
                            $decoded = json_decode($text, true);

                            $ar = null;
                            $en = null;

                            if (is_array($decoded) && (isset($decoded['ar']) || isset($decoded['en']))) {
                                $ar = $decoded['ar'] ?? null;
                                $en = $decoded['en'] ?? null;
                            } else {
                                $ar = $text;
                            }
                        @endphp

                        <li>
                            @if($ar)
                                {!! nl2br(e($ar)) !!}
                            @endif

                            @if($en)
                                <div style="margin-top: 6px; font-size: 11px; color: #444;">
                                    {!! nl2br(e($en)) !!}
                                </div>
                            @endif
                        </li>
                    @endforeach
                @endif
            </ol>
        </div>
    </div>

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
            Page 1 of 2
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

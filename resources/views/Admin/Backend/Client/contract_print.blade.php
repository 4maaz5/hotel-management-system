<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Agreement</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            background: #f1f3f6;
            min-height: 100vh;
            padding: 20px;
            direction: rtl;
        }

        .container {
            width: 210mm;
            height: 297mm;
            /* 🔴 FIXED A4 HEIGHT */
            margin: auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            /* 🔴 PREVENT PAGE BREAK */
        }


        /* ================= COMPANY HEADER ================= */
        .company-header {
            display: flex;
            flex-direction: row-reverse;

            justify-content: space-between;
            align-items: center;
            flex: 0 0 90px;
            padding: 18px 30px;
            border-bottom: 1px solid #dee2e6;
            background: #ffffff;
        }


        .company-info {
            text-align: right;
        }

        .company-info h5 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .company-info .small {
            font-size: 13px;
            color: #555;
            line-height: 1.6;
        }

        .company-logo img {
            max-height: 80px;
        }

        /* ================= TOP BAR ================= */
        .top-bar {
            background: ;
            padding: 18px 30px 22px;
            color: #000000;
            flex: 0 0 110px;
        }

        .print-btn {
            text-align: center;
            margin-bottom: 10px;
        }

        .print-btn button {
            background: transparent;
            border: 1.5px solid #fff;
            background-color: #2835f5;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }

        .header {
            text-align: center;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .contract-number {
            display: inline-block;
            font-size: 13px;
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 16px;
            border-radius: 20px;
        }

        /* ================= CONTENT ================= */
        .content {
            padding: 5px;
            flex: 1;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #4b5bdc;
            margin-bottom: 12px;
            border-bottom: 2px solid #4b5bdc;
            padding-bottom: 6px;
        }

        /* ================= INFO GRID ================= */
        .info-grid {
            display: grid;
            gap: 10px;
        }

        .info-row {
            display: grid;
            grid-template-columns: 180px 1fr;
            background: #f8f9fa;
            padding: 10px 14px;
            border-radius: 6px;
        }

        .label {
            font-weight: 600;
            color: #495057;
        }

        .value {
            color: #212529;
        }

        /* ================= STATUS ================= */
        .status-badge {
            padding: 5px 14px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        /* ================= REMARKS ================= */
        .remarks-box {
            background: #f8f9fa;
            border-right: 4px solid #4b5bdc;
            padding: 14px;
            border-radius: 6px;
            font-size: 14px;
            color: #555;
            line-height: 1.7;
        }

        /* ================= FOOTER ================= */
        .footer {
            background: #f8f9fa;
            text-align: center;
            padding: 12px;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #dee2e6;
            flex: 0 0 40px;
        }

        /* ================= PRINT ================= */
        @media print {

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                width: 210mm;
                height: 297mm;
                background: #fff;
            }

            .container {
                width: 210mm;
                height: 297mm;
                margin: 0 !important;
                border-radius: 0;
                box-shadow: none;
            }

            .print-btn {
                display: none;
            }

            @page {
                size: A4;
                margin: 0;
            }
        }


        /* ================= MOBILE ================= */
        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }

            .info-row {
                grid-template-columns: 1fr;
                gap: 6px;
            }
        }
    </style>

</head>

<body>
    <div class="container">

        <div class="company-header" style="display:flex; justify-content:space-between; align-items:center;">

            <!-- Company Logo -->
            <div class="company-logo" style="flex:0 0 auto;">
                @if ($contract->company->logo)
                    <img src="{{ asset('storage/' . $contract->company->logo) }}" height="80">
                @endif
            </div>

            <!-- Company Info -->
            <div class="company-info" style="text-align:right;">
                <h5 class="mb-1">
                    {{ __('dashboard.company_name') }} :
                    {{ $contract->company->legal_name ?? __('dashboard.company_name') }}
                </h5>

                <div class="small">
                    {{ __('dashboard.cr_number') }} : {{ $contract->company->cr_number ?? '-' }} <br>
                    {{ __('dashboard.vat_number') }} : {{ $contract->company->vat_number ?? '-' }} <br>
                    {{ __('dashboard.email') }} : {{ $contract->company->email ?? '-' }} <br>
                    {{ __('dashboard.phone') }} : {{ $contract->company->phone ?? '-' }}
                </div>
            </div>

        </div>



        <!-- Top Bar with Header -->
        <div class="top-bar">

            <div class="header">
                <h1>{{ __('dashboard.contract_agreement') }}</h1>
                <div class="contract-number">{{ __('dashboard.contract') }} {{ $contract->contract_number }}</div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <!-- Contract Information -->
            <div class="section">
                <div class="section-title">{{ __('dashboard.contract_information') }}</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="label">{{ __('dashboard.contract_title') }}</div>
                        <div class="value">{{ $contract->title }}</div>
                    </div>
                    <div class="info-row">
                        <div class="label">{{ __('dashboard.client') }}</div>
                        <div class="value">{{ $contract->client->client_name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="label">{{ __('dashboard.company_name') }}</div>
                        <div class="value">{{ $contract->company->legal_name }}</div>
                    </div>
                    {{-- <div class="info-row">
                        <div class="label">{{ __('dashboard.status') }}</div>
                        <div class="value">
                            <span class="status-badge status-active">{{ $contract->status }}</span>
                        </div>
                    </div> --}}
                    <div class="info-row">
                        <div class="label">{{ __('dashboard.start_date') }}</div>
                        <div class="value">{{ $contract->start_date->format('d-m-y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="label">{{ __('dashboard.end_date') }}</div>
                        <div class="value">{{ $contract->end_date->format('d-m-y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Remarks -->
            <div class="section">
                <div class="section-title">{{ __('dashboard.remarks') }}</div>
                <div class="remarks-box">
                    {{ $contract->remarks }}
                </div>
            </div>

            <!-- Attached Documents (Uncomment when needed) -->
            <!--
            <div class="section">
                <div class="section-title">Attached Documents</div>
                <div class="files">
                    <a href="#" class="file-link">
                        <div class="file-icon">📄</div>
                        <span>project-proposal.pdf</span>
                    </a>
                    <a href="#" class="file-link">
                        <div class="file-icon">📄</div>
                        <span>terms-and-conditions.pdf</span>
                    </a>
                </div>
            </div>
            -->

            <!-- Signatures -->
            {{-- <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Authorized Signature</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Client Signature</div>
                </div>
            </div> --}}
        </div>

        <div class="footer">
            {{ __('dashboard.generated_on') }} {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}
        </div>

        <div class="print-btn btn-btn-secondary">
            <button onclick="window.print()">🖨 {{ __('dashboard.print_contract') }}</button>
        </div>
    </div>
</body>

</html>

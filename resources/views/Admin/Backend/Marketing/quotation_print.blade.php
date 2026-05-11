<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>{{ __('dashboard.marketing_quotation') }} - {{ $quotation->quotation_number }}</title>

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            background: #fff;
            font-size: 14px;
            direction: rtl;
            text-align: right;
        }

        .letter {
            max-width: 800px;
            margin: auto;
            padding: 40px;
            border: 1px solid #ddd;
        }

        .header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 15px;
            margin-bottom: 25px;
            text-align: center;
        }

        .headers {

            text-align: center;
        }

        .section-title {
            background: #f1f3f5;
            padding: 8px 12px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 10px;
            border-right: 4px solid #0d6efd;
        }


        .ckeditor-content table {
            width: 100%;
            border-collapse: collapse;
        }

        .ckeditor-content table,
        .ckeditor-content th,
        .ckeditor-content td {
            border: 1px solid #000;
        }

        .ckeditor-content th,
        .ckeditor-content td {
            padding: 8px;
            text-align: left;
        }


        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="letter">

        <!-- HEADER -->
        <div class="header d-flex justify-content-between align-items-center">

            <!-- Company Info (RIGHT in RTL) -->
            <div class="company-info text-right">
                <h5 class="mb-1">
                    {{ $quotation->branch->company->legal_name ?? __('dashboard.company_name') }}</h5>

                <div class="small">
                    {{ __('dashboard.cr_number') }} : {{ $quotation->branch->company->cr_number ?? '-' }} <br>
                    {{ __('dashboard.vat_number') }} : {{ $quotation->branch->company->vat_number ?? '-' }} <br>
                    {{ __('dashboard.email') }} : {{ $quotation->branch->company->email ?? '-' }} <br>
                    {{ __('dashboard.phone') }} : {{ $quotation->branch->company->phone ?? '-' }}
                </div>
            </div>

            <!-- Company Logo (LEFT in RTL) -->
            <div class="company-logo text-left">
                @if ($quotation->branch->company->logo)
                    <img src="{{ asset('storage/' . $quotation->branch->company->logo) }}" height="80">
                @endif
            </div>

        </div>


        <!-- HEADER -->
        <div class="headers">
            @if ($quotation->logo)
                <img src="{{ asset('storage/' . $quotation->logo) }}" height="80" class="mb-2">
            @endif

            <h4>{{ __('dashboard.marketing_quotation') }}</h4>
            <small class="text-muted">{{ $quotation->quotation_number }}</small>
        </div>

        <!-- AGENT -->
        <div class="section-title">{{ __('dashboard.agent_name') }}</div>
        <p>
            @if (!empty($quotation->agent->name))
                <strong>{{ __('dashboard.agent_name') }}:</strong>
                {{ $quotation->agent->name }}<br>
            @else
                <strong>{{ __('dashboard.agent_name') }}:</strong>
                {{ $quotation->manual_agent_name ?? '-' }}
            @endif
        </p>

        <!-- CLIENT -->
        <div class="section-title">{{ __('dashboard.client_name') }}</div>
        <p>
            <strong>{{ __('dashboard.client_name') }}:</strong> {{ $quotation->client_name }}<br>
            <strong>{{ __('dashboard.client_contact') }}:</strong> {{ $quotation->client_contact ?? '-' }}<br>
            <strong>{{ __('dashboard.email') }}:</strong> {{ $quotation->email ?? '-' }}<br>
            <strong>{{ __('dashboard.vat_number') }}:</strong> {{ $quotation->vat_no ?? '-' }}<br>
            <strong>{{ __('dashboard.cr_number') }}:</strong> {{ $quotation->cr_no ?? '-' }}

        </p>

        <!-- DETAILS -->
        <div class="section-title">{{ __('dashboard.quotation_details') }}</div>
        <p>
            <strong>{{ __('dashboard.branch') }}:</strong> {{ $quotation->branch->name ?? '-' }}<br>
            <strong>{{ __('dashboard.quotation_amount') }}:</strong>
            {{ number_format($quotation->quotation_amount, 2) }}<br>
            <strong>{{ __('dashboard.status') }}:</strong> {{ __('dashboard.' . $quotation->status) }}<br>
            <strong>{{ __('dashboard.approve_at') }}:</strong>
            {{ $quotation->approved_at?->format('Y-m-d H:i') ?? '-' }}
        </p>

        <!-- DESCRIPTION -->
        <div class="section-title">{{ __('dashboard.description') }}</div>
        <div class="ckeditor-content">
            {!! $quotation->description ?: nl2br(e($quotation->description)) !!}
        </div>

        <!-- FOOTER -->
        <hr>
        <p class="text-center text-muted">
            {{ __('dashboard.generated_on') }} : {{ now()->format('Y-m-d H:i') }}
            <strong>{{ __('dashboard.account_number') }}:</strong> {{ $quotation->account_number ?? '-' }}
            <strong>{{ __('dashboard.bank_name') }}:</strong> {{ $quotation->bank_name ?? '-' }}
        </p>

    </div>

    <!-- PRINT BUTTON -->
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> {{ __('dashboard.print') }}
        </button>
    </div>

</body>

</html>

<script>
    window.onload = function() {
        window.print();
    }
</script>

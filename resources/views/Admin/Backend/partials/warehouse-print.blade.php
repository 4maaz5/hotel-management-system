<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Warehouse Request</title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        th {
            background: #0d6efd;
            color: #fff;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <div
            style="
        background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        padding: 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    ">

            <!-- Left Side - Company Details -->
            <div style="flex: 1;">
                <div style="margin-bottom: 8px;">
                    <strong style="min-width:140px; display:inline-block;">
                        {{ __('dashboard.company_name') }}:
                    </strong>
                    {{ optional(optional($request->branch)->company)->legal_name ?? '-' }}
                </div>

                <div style="margin-bottom: 8px;">
                    <strong style="min-width:140px; display:inline-block;">
                        {{ __('dashboard.brand_name') }}:
                    </strong>
                    {{ optional($request->branch)->brand->name ?? '-' }}
                </div>

                <div style="margin-bottom: 8px;">
                    <strong style="min-width:140px; display:inline-block;">
                        {{ __('dashboard.country') }}:
                    </strong>
                    {{ optional(optional($request->branch)->company)->country ?? '-' }}
                </div>

                <div style="margin-bottom: 8px;">
                    <strong style="min-width:140px; display:inline-block;">
                        {{ __('dashboard.cr_number') }}:
                    </strong>
                    {{ optional(optional($request->branch)->company)->cr_number ?? '-' }}
                </div>

                <div style="margin-bottom: 8px;">
                    <strong style="min-width:140px; display:inline-block;">
                        {{ __('dashboard.print_date') }}:
                    </strong>
                    {{ now()->format('d M Y, h:i A') }}
                </div>

                <div style="margin-bottom: 8px;">
                    <strong style="min-width:140px; display:inline-block;">
                        {{ __('dashboard.generated_by') }}:
                    </strong>
                    {{ Auth::user()->name ?? 'Admin' }}
                </div>
            </div>

            <!-- Right Side - Company Logo -->
            <div
                style="
            width: 150px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.6);
            border-radius: 10px;
        ">
                @if (optional(optional($request->branch)->company)->logo)
                    <img src="{{ asset('storage/' . optional(optional($request->branch)->company)->logo) }}"
                        style="max-width: 120px; max-height: 120px; object-fit: contain;">
                @else
                    <span style="font-size:12px; color:#64748b;">No Logo</span>
                @endif
            </div>

        </div>


        <h3 style="text-align:center;">{{ __('dashboard.warehouse_product_request') }}</h3>

        <p><strong>{{ __('dashboard.requested_by') }} #:</strong> {{ $request->requester->name ?? '' }}</p>
        <p><strong>{{ __('dashboard.approved_by') }} #:</strong> {{ $request->approver->name ?? '-' }}</p>
        <p><strong>{{ __('dashboard.status') }} #:</strong> {{ $request->status }}</p>
        <p><strong>{{ __('dashboard.branch') }}:</strong> {{ optional($request->branch)->name ?? '-' }}</p>
        <p><strong>{{ __('dashboard.date') }}:</strong> {{ $request->created_at->format('d M Y') }}</p>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('dashboard.classify') }}</th>
                    <th>{{ __('dashboard.type') }}</th>
                    <th>{{ __('dashboard.quantity') }}</th>
                    <th>{{ __('dashboard.unit') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($request->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td>{{ $item->product->category->name ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->product->unit ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</body>

</html>
<script>
    window.onload = () => window.print();
</script>

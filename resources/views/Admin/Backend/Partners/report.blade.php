@extends('layout.master')
@section('title', 'Dashboard | Report')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center" style="margin-bottom: 1.5rem; color: #2d3748; font-weight: 600;">
            {{ __('dashboard.partner_report') }}</h1>

        <!-- Header with company details and logo -->
        <div
            style="background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%); padding: 1.5rem; border-radius: 10px; border-left: 5px solid #667eea; margin-bottom: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <!-- Company Details - Left Side -->
                <div style="flex: 1; padding-right: 2rem;">
                    <div style="background: #f8f9fc; border-radius: 8px; border-left: 4px solid #4c51bf; padding: 1.25rem;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div>
                                <div style="font-weight: 600; color: #4a5568; font-size: 0.8rem; margin-bottom: 0.25rem;">
                                    {{ __('dashboard.company_name') }}</div>
                                <div style="font-size: 0.95rem; color: #2d3748;">{{ $partner->company->legal_name }}</div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #4a5568; font-size: 0.8rem; margin-bottom: 0.25rem;">
                                    {{ __('dashboard.country') }}</div>
                                <div style="font-size: 0.95rem; color: #2d3748;">{{ $partner->company->country }}</div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #4a5568; font-size: 0.8rem; margin-bottom: 0.25rem;">
                                    {{ __('dashboard.cr_number') }}</div>
                                <div style="font-size: 0.95rem; color: #2d3748;">{{ $partner->company->cr_number }}</div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #4a5568; font-size: 0.8rem; margin-bottom: 0.25rem;">
                                    {{ __('dashboard.print_date') }}</div>
                                <div style="font-size: 0.95rem; color: #2d3748;">{{ now()->format('d M Y, h:i A') }}</div>
                            </div>
                            <div style="grid-column: span 2;">
                                <div style="font-weight: 600; color: #4a5568; font-size: 0.8rem; margin-bottom: 0.25rem;">
                                    {{ __('dashboard.generated_by') }}</div>
                                <div style="font-size: 0.95rem; color: #2d3748;">{{ Auth::user()->name ?? 'Admin' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Logo - Right Side Only -->
                <div style="width: 120px; height: 120px; text-align: center; margin-left: 1rem;">
                    @if ($partner->company->logo)
                        <img src="{{ asset('storage/' . $partner->company->logo) }}" alt="Company Logo"
                            style="max-width: 100%; max-height: 120px; object-fit: contain; border-radius: 8px; border: 2px solid #e2e8f0; padding: 4px; background: white;">
                    @else
                        <div
                            style="width: 120px; height: 120px; border: 2px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; border-radius: 8px; background: #f8f9fa;">
                            <span style="color: #718096; font-size: 0.85rem;">No Logo</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Download PDF Button -->
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <a href="{{ route('partners.download.pdf', $partner->id) }}" target="_blank"
                style="display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white; padding: 0.5rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 1rem; box-shadow: 0 4px 6px rgba(220, 38, 38, 0.3);">
                <i class="fas fa-file-pdf" style="margin-right: 0.5rem;"></i>{{ __('dashboard.print') }}
            </a>
        </div>

        <!-- Report Content -->
        <section style="margin-top: 1.5rem;">
            <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden;">
                <!-- Card Header -->
                <div
                    style="padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <h4 style="margin: 0; color: #2d3748; font-weight: 600; font-size: 1.1rem;">
                        {{ __('dashboard.partner_details') }}</h4>
                    <span
                        style="background: #4c51bf; color: white; padding: 0.2rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                        {{ ucfirst($partner->partner_type) }}
                    </span>
                </div>

                <div style="padding: 1.25rem;">
                    <!--  Basic Information -->
                    <div style="margin-bottom: 1.5rem; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <div
                            style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 0.75rem 1.25rem; display: flex; align-items: center;">
                            <i class="fas fa-user-circle" style="margin-right: 0.5rem; font-size: 0.9rem;"></i>
                            <h5 style="margin: 0; font-size: 1rem; font-weight: 600;">{{ __('dashboard.basic_info') }}</h5>
                        </div>
                        <div style="padding: 1rem;">
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                    <tbody>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <th
                                                style="width: 20%; padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left;">
                                                {{ __('dashboard.full_name') }}</th>
                                            <td style="width: 30%; padding: 0.5rem;">{{ $partner->full_name }}</td>
                                            <th
                                                style="width: 20%; padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left;">
                                                {{ __('dashboard.email') }}</th>
                                            <td style="width: 30%; padding: 0.5rem;">{{ $partner->email ?? '-' }}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left;">
                                                {{ __('dashboard.phone') }}</th>
                                            <td style="padding: 0.5rem;">{{ $partner->phone ?? '-' }}</td>
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left;">
                                                {{ __('dashboard.nationality') }}</th>
                                            <td style="padding: 0.5rem;">{{ $partner->nationality }}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left;">
                                                {{ __('dashboard.company') }}</th>
                                            <td style="padding: 0.5rem;">{{ $partner->company->name ?? '-' }}</td>
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left;">
                                                {{ __('dashboard.partner_type') }}</th>
                                            <td style="padding: 0.5rem;">{{ ucfirst($partner->partner_type) }}</td>
                                        </tr>
                                        <tr>
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left;">
                                                {{ __('dashboard.id_type') }}</th>
                                            <td style="padding: 0.5rem;">{{ ucfirst($partner->id_type) }}</td>
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left;">
                                                {{ __('dashboard.id_number') }}</th>
                                            <td style="padding: 0.5rem;">{{ $partner->id_number }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Investment Information -->
                    <div style="margin-bottom: 1.5rem; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <div
                            style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 0.75rem 1.25rem; display: flex; align-items: center;">
                            <i class="fas fa-chart-line" style="margin-right: 0.5rem; font-size: 0.9rem;"></i>
                            <h5 style="margin: 0; font-size: 1rem; font-weight: 600;">{{ __('dashboard.investment_info') }}
                            </h5>
                        </div>
                        <div style="padding: 1rem;">
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                    <tbody>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <th
                                                style="width: 25%; padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left;">
                                                {{ __('dashboard.investment_amount') }}</th>
                                            <td style="width: 25%; padding: 0.5rem; font-weight: 600; color: #2b6cb0;">
                                                ${{ number_format($partner->investment_amount, 2) }}
                                            </td>
                                            <th
                                                style="width: 25%; padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left;">
                                                {{ __('dashboard.share_percentage') }}</th>
                                            <td style="width: 25%; padding: 0.5rem; font-weight: 600; color: #2f855a;">
                                                {{ $partner->share_percentage }}%
                                            </td>
                                        </tr>
                                        <tr>
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left;">
                                                {{ __('dashboard.share_quantity') }}</th>
                                            <td style="padding: 0.5rem; font-weight: 600;">{{ $partner->share_quantity }}
                                            </td>
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left;">
                                                {{ __('dashboard.notes') }}</th>
                                            <td style="padding: 0.5rem;">{{ $partner->notes ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!--  Documents Section -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <div
                            style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: #212529; padding: 0.75rem 1.25rem; display: flex; align-items: center;">
                            <i class="fas fa-folder" style="margin-right: 0.5rem; font-size: 0.9rem;"></i>
                            <h5 style="margin: 0; font-size: 1rem; font-weight: 600;">{{ __('dashboard.documents') }}</h5>
                        </div>
                        <div style="padding: 1rem;">
                            @if ($partner->documents->count() > 0)
                                <div style="overflow-x: auto;">
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                        <thead>
                                            <tr style="background: #f8f9fa; border-bottom: 2px solid #e2e8f0;">
                                                <th
                                                    style="padding: 0.5rem; font-weight: 600; color: #4a5568; text-align: left; width: 30%;">
                                                    {{ __('dashboard.document_type') }}</th>
                                                <th
                                                    style="padding: 0.5rem; font-weight: 600; color: #4a5568; text-align: left; width: 40%;">
                                                    {{ __('dashboard.file_name') }}</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($partner->documents as $doc)
                                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                                    <td style="padding: 0.5rem;">
                                                        <span
                                                            style="background: #4c51bf; color: white; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.8rem;">
                                                            {{ ucfirst($doc->document_type) }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 0.5rem;">
                                                        <i class="fas fa-file"
                                                            style="color: #718096; margin-right: 0.5rem; font-size: 0.8rem;"></i>
                                                        <span style="font-size: 0.85rem;">{{ $doc->original_name }}</span>
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div style="text-align: center; padding: 2rem 1rem;">
                                    <i class="fas fa-folder-open"
                                        style="font-size: 2.5rem; color: #cbd5e0; margin-bottom: 0.75rem;"></i>
                                    <p style="color: #718096; margin: 0; font-size: 0.9rem;">
                                        {{ __('dashboard.no_documents') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

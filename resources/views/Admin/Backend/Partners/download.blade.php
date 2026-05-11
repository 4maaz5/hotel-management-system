    <script>
        // Trigger print dialog on page load
        window.onload = function() {
            window.print();
        };
    </script>
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center" style="margin-bottom: 1.5rem; color: #2d3748; font-weight: 600;">
            {{ __('dashboard.partner_report') }}</h1>

        <!-- Header with company details and logo -->
        <div
            style="background: #f8f9fc; padding: 1.5rem; border-radius: 10px; border-left: 5px solid #667eea; margin-bottom: 1.5rem; border: 1px solid #e2e8f0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="vertical-align: top; padding-right: 1.5rem;">
                        <!-- Company Details -->
                        <div style="padding: 1rem; border-radius: 8px; border-left: 4px solid #4c51bf;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                                <tr>
                                    <td style="padding: 0.5rem 0; width: 35%;">
                                        <div style="font-weight: 600; color: #4a5568; font-size: 0.8rem;">
                                            {{ __('dashboard.company_name') }}:
                                        </div>
                                    </td>
                                    <td style="padding: 0.5rem 0; width: 65%;">
                                        <div style="color: #2d3748;">{{ $partner->company->legal_name }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0.5rem 0;">
                                        <div style="font-weight: 600; color: #4a5568; font-size: 0.8rem;">
                                            {{ __('dashboard.country') }}:
                                        </div>
                                    </td>
                                    <td style="padding: 0.5rem 0;">
                                        <div style="color: #2d3748;">{{ $partner->company->country }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0.5rem 0;">
                                        <div style="font-weight: 600; color: #4a5568; font-size: 0.8rem;">
                                            {{ __('dashboard.cr_number') }}:
                                        </div>
                                    </td>
                                    <td style="padding: 0.5rem 0;">
                                        <div style="color: #2d3748;">{{ $partner->company->cr_number }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0.5rem 0;">
                                        <div style="font-weight: 600; color: #4a5568; font-size: 0.8rem;">
                                            {{ __('dashboard.print_date') }}:
                                        </div>
                                    </td>
                                    <td style="padding: 0.5rem 0;">
                                        <div style="color: #2d3748;">{{ now()->format('d M Y, h:i A') }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0.5rem 0;">
                                        <div style="font-weight: 600; color: #4a5568; font-size: 0.8rem;">
                                            {{ __('dashboard.generated_by') }}:
                                        </div>
                                    </td>
                                    <td style="padding: 0.5rem 0;">
                                        <div style="color: #2d3748;">{{ Auth::user()->name ?? 'Admin' }}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td style="vertical-align: top; width: 120px; text-align: right;">
                        <!-- Company Logo -->
                        <div style="width: 100px; height: 100px; text-align: center;">
                            @if ($partner->company->logo)
                                <img src="{{ public_path('storage/' . $partner->company->logo) }}" alt="Company Logo"
                                    style="max-width: 100%; max-height: 100px; object-fit: contain; border-radius: 6px; border: 1px solid #e2e8f0;">
                            @else
                                <div
                                    style="width: 100px; height: 100px; border: 1px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; border-radius: 6px; background: #f8f9fa;">
                                    <span style="color: #718096; font-size: 0.8rem;">No Logo</span>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Report Content -->
        <section style="margin-top: 1.5rem;">
            <div style="background: white; border-radius: 10px; border: 1px solid #e2e8f0; overflow: hidden;">
                <!-- Card Header -->
                <div
                    style="padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <h4 style="margin: 0; color: #2d3748; font-weight: 600; font-size: 1rem;">
                        {{ __('dashboard.partner_details') }}</h4>
                    <span
                        style="background: #4c51bf; color: white; padding: 0.15rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                        {{ ucfirst($partner->partner_type) }}
                    </span>
                </div>

                <div style="padding: 1.25rem;">
                    <!-- Basic Information -->
                    <div
                        style="margin-bottom: 1.25rem; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <!-- PDF-Compatible Header - Using solid background -->
                        <div
                            style="background: #17a2b8; color: white; padding: 0.75rem 1rem; display: flex; align-items: center;">
                            <i class="fas fa-user-circle" style="margin-right: 0.5rem; font-size: 0.9rem;"></i>
                            <h5 style="margin: 0; font-size: 0.95rem; font-weight: 600;">
                                {{ __('dashboard.basic_info') }}
                            </h5>
                        </div>
                        <div style="padding: 1rem;">
                            <div style="overflow-x: auto;">
                                <table
                                    style="width: 100%; border-collapse: collapse; font-size: 0.8rem; border: 1px solid #e2e8f0;">
                                    <tbody>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <th
                                                style="width: 20%; padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left; border-right: 1px solid #e2e8f0;">
                                                {{ __('dashboard.full_name') }}</th>
                                            <td style="width: 30%; padding: 0.5rem;">{{ $partner->full_name }}</td>
                                            <th
                                                style="width: 20%; padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left; border-right: 1px solid #e2e8f0;">
                                                {{ __('dashboard.email') }}</th>
                                            <td style="width: 30%; padding: 0.5rem;">{{ $partner->email ?? '-' }}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left; border-right: 1px solid #e2e8f0;">
                                                {{ __('dashboard.phone') }}</th>
                                            <td style="padding: 0.5rem;">{{ $partner->phone ?? '-' }}</td>
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left; border-right: 1px solid #e2e8f0;">
                                                {{ __('dashboard.nationality') }}</th>
                                            <td style="padding: 0.5rem;">{{ $partner->nationality }}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left; border-right: 1px solid #e2e8f0;">
                                                {{ __('dashboard.company') }}</th>
                                            <td style="padding: 0.5rem;">{{ $partner->company->name ?? '-' }}</td>
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left; border-right: 1px solid #e2e8f0;">
                                                {{ __('dashboard.partner_type') }}</th>
                                            <td style="padding: 0.5rem;">{{ ucfirst($partner->partner_type) }}</td>
                                        </tr>
                                        <tr>
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left; border-right: 1px solid #e2e8f0;">
                                                {{ __('dashboard.id_type') }}</th>
                                            <td style="padding: 0.5rem;">{{ ucfirst($partner->id_type) }}</td>
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left; border-right: 1px solid #e2e8f0;">
                                                {{ __('dashboard.id_number') }}</th>
                                            <td style="padding: 0.5rem;">{{ $partner->id_number }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Investment Information -->
                    <div
                        style="margin-bottom: 1.25rem; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <!-- PDF-Compatible Header - Using solid background -->
                        <div
                            style="background: #28a745; color: white; padding: 0.75rem 1rem; display: flex; align-items: center;">
                            <i class="fas fa-chart-line" style="margin-right: 0.5rem; font-size: 0.9rem;"></i>
                            <h5 style="margin: 0; font-size: 0.95rem; font-weight: 600;">
                                {{ __('dashboard.investment_info') }}</h5>
                        </div>
                        <div style="padding: 1rem;">
                            <div style="overflow-x: auto;">
                                <table
                                    style="width: 100%; border-collapse: collapse; font-size: 0.8rem; border: 1px solid #e2e8f0;">
                                    <tbody>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <th
                                                style="width: 25%; padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left; border-right: 1px solid #e2e8f0;">
                                                {{ __('dashboard.investment_amount') }}</th>
                                            <td style="width: 25%; padding: 0.5rem; font-weight: 600; color: #2b6cb0;">
                                                ${{ number_format($partner->investment_amount, 2) }}
                                            </td>
                                            <th
                                                style="width: 25%; padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left; border-right: 1px solid #e2e8f0;">
                                                {{ __('dashboard.share_percentage') }}</th>
                                            <td style="width: 25%; padding: 0.5rem; font-weight: 600; color: #2f855a;">
                                                {{ $partner->share_percentage }}%
                                            </td>
                                        </tr>
                                        <tr>
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left; border-right: 1px solid #e2e8f0;">
                                                {{ __('dashboard.share_quantity') }}</th>
                                            <td style="padding: 0.5rem; font-weight: 600;">
                                                {{ $partner->share_quantity }}
                                            </td>
                                            <th
                                                style="padding: 0.5rem; background: #f8f9fa; font-weight: 600; color: #4a5568; text-align: left; border-right: 1px solid #e2e8f0;">
                                                {{ __('dashboard.notes') }}</th>
                                            <td style="padding: 0.5rem;">{{ $partner->notes ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <!-- PDF-Compatible Header - Using solid background -->
                        <div
                            style="background: #ffc107; color: #212529; padding: 0.75rem 1rem; display: flex; align-items: center;">
                            <i class="fas fa-folder" style="margin-right: 0.5rem; font-size: 0.9rem;"></i>
                            <h5 style="margin: 0; font-size: 0.95rem; font-weight: 600;">
                                {{ __('dashboard.documents') }}
                            </h5>
                        </div>
                        <div style="padding: 1rem;">
                            @if ($partner->documents->count() > 0)
                                <div style="overflow-x: auto;">
                                    <table
                                        style="width: 100%; border-collapse: collapse; font-size: 0.8rem; border: 1px solid #e2e8f0;">
                                        <thead>
                                            <tr style="background: #f8f9fa; border-bottom: 2px solid #e2e8f0;">
                                                <th
                                                    style="padding: 0.5rem; font-weight: 600; color: #4a5568; text-align: left; width: 30%; border-right: 1px solid #e2e8f0;">
                                                    {{ __('dashboard.document_type') }}</th>
                                                <th
                                                    style="padding: 0.5rem; font-weight: 600; color: #4a5568; text-align: left; width: 70%;">
                                                    {{ __('dashboard.file_name') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($partner->documents as $doc)
                                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                                    <td style="padding: 0.5rem; border-right: 1px solid #e2e8f0;">
                                                        <span
                                                            style="background: #4c51bf; color: white; padding: 0.15rem 0.5rem; border-radius: 20px; font-size: 0.75rem;">
                                                            {{ ucfirst($doc->document_type) }}
                                                        </span>
                                                    </td>
                                                    <td style="padding: 0.5rem;">
                                                        <i class="fas fa-file"
                                                            style="color: #718096; margin-right: 0.5rem; font-size: 0.8rem;"></i>
                                                        <span
                                                            style="font-size: 0.8rem;">{{ $doc->original_name }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div style="text-align: center; padding: 1.5rem 1rem;">
                                    <i class="fas fa-folder-open"
                                        style="font-size: 2rem; color: #cbd5e0; margin-bottom: 0.5rem;"></i>
                                    <p style="color: #718096; margin: 0; font-size: 0.85rem;">
                                        {{ __('dashboard.no_documents') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

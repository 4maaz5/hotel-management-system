     <div class="card-body" id="printable-report">
         @if ($selectedCompany)
             @php
                 $company = $companies->where('id', $selectedCompany)->first();
                 $companyName = $company->legal_name ?? '';
                 $companyAddress = $company->city ?? 'N/A';
                 $companyCR = $company->cr_number ?? 'N/A';
                 $companyLogo = $company->logo ?? null;
             @endphp

             <!-- Report Header -->
             <div class="mb-4 pb-4 border-bottom"
                 style="background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%); padding: 20px; border-radius: 10px;">
                 <h3 class="text-center mb-4 font-weight-bold" style="color: #2d3748;">
                     {{ __('dashboard.finance_report') }}</h3>

                 <div class="row ">
                     <!-- Left Side - Company Details -->
                     <div class="col-md-8">
                         <div
                             style="background: #f8f9fc; padding: 20px; border-radius: 10px; border-left: 4px solid #667eea;">
                             <div class="d-flex mb-2">
                                 <span class="font-weight-bold"
                                     style="color: #2d3748; min-width: 140px; font-size: 14px;">{{ __('dashboard.company_name') }}:</span>
                                 <span style="color: #4a5568; font-size: 14px;">{{ $companyName }}</span>
                             </div>
                             <div class="d-flex mb-2">
                                 <span class="font-weight-bold"
                                     style="color: #2d3748; min-width: 140px; font-size: 14px;">{{ __('dashboard.address') }}:</span>
                                 <span style="color: #4a5568; font-size: 14px;">{{ $companyAddress }}</span>
                             </div>
                             <div class="d-flex mb-2">
                                 <span class="font-weight-bold"
                                     style="color: #2d3748; min-width: 140px; font-size: 14px;">{{ __('dashboard.cr_number') }}:</span>
                                 <span style="color: #4a5568; font-size: 14px;">{{ $companyCR }}</span>
                             </div>
                             <div class="d-flex mb-2">
                                 <span class="font-weight-bold"
                                     style="color: #2d3748; min-width: 140px; font-size: 14px;">{{ __('dashboard.print_date') }}:</span>
                                 <span
                                     style="color: #4a5568; font-size: 14px;">{{ now()->format('d M Y, h:i A') }}</span>
                             </div>
                             <div class="d-flex mb-2">
                                 <span class="font-weight-bold"
                                     style="color: #2d3748; min-width: 140px; font-size: 14px;">{{ __('dashboard.generated_by') }}:</span>
                                 <span
                                     style="color: #4a5568; font-size: 14px;">{{ Auth::user()->name ?? 'Admin' }}</span>
                             </div>
                         </div>
                     </div>

                     <!-- Right Side - Company Logo -->
                     <div class="col-md-4 text-right">
                         @if ($companyLogo)
                             <img src="{{ asset('storage/' . $companyLogo) }}" alt="{{ $companyName }}"
                                 style="max-width: 150px; max-height: 150px; object-fit: contain;">
                         @else
                             <div class="d-flex align-items-center justify-content-center ml-auto"
                                 style="width: 150px; height: 150px; background: #f7fafc; border: 2px dashed #cbd5e1; border-radius: 10px;">
                                 <span class="text-muted">No Logo</span>
                             </div>
                         @endif
                     </div>
                 </div>
             </div>

             <!-- Brands Section -->
             <div class="mb-4">
                 <h5 class="font-weight-bold mb-3 pb-2" style="color: #2d3748; border-bottom: 2px solid #e2e8f0;">
                     <i class="fas fa-tag mr-2" style="color: #667eea;"></i>{{ __('dashboard.brands') }}
                 </h5>
                 @if ($brands->isNotEmpty())
                     <div class="row">
                         @foreach ($brands as $brand)
                             <div class="col-md-4 mb-3">
                                 <div
                                     style="background: #f7fafc; border-radius: 8px; border: 1px solid #e2e8f0; padding: 12px 15px; transition: all 0.3s ease;">
                                     <i class="fas fa-circle mr-2" style="color: #667eea; font-size: 8px;"></i>
                                     <span
                                         style="color: #2d3748; font-weight: 600; font-size: 14px;">{{ $brand->name }}</span>
                                 </div>
                             </div>
                         @endforeach
                     </div>
                 @else
                     <div class="alert alert-info">
                         <i class="fas fa-info-circle mr-2"></i>{{ __('dashboard.no_brands_found') }}
                     </div>
                 @endif
             </div>

             <!-- Branches Section -->
             <div class="mb-4">
                 <h5 class="font-weight-bold mb-3 pb-2" style="color: #2d3748; border-bottom: 2px solid #e2e8f0;">
                     <i class="fas fa-building mr-2" style="color: #764ba2;"></i>{{ __('dashboard.branches') }}
                 </h5>
                 @if ($branches->isNotEmpty())
                     <div class="row" id="branch-list">
                         @foreach ($branches as $branch)
                             <div class="col-md-4 mb-3">
                                 <div class="branch-box" data-branch="{{ $branch->id }}"
                                     style="background: #f7fafc; border-radius: 8px; border: 1px solid #e2e8f0; padding: 12px 15px; transition: all 0.3s ease; cursor:pointer;">
                                     <i class="fas fa-circle mr-2" style="color: #764ba2; font-size: 8px;"></i>
                                     <span style="color: #2d3748; font-weight: 600; font-size: 14px;">
                                         {{ $branch->name }}
                                     </span>
                                 </div>
                             </div>
                         @endforeach
                     </div>
                 @else
                     <div class="alert alert-info">
                         <i class="fas fa-info-circle mr-2"></i>{{ __('dashboard.no_branches_found') }}
                     </div>
                 @endif

                 <!-- Branch report will be rendered here -->
                 <div id="branch-report" class="mt-4"></div>

             </div>
         @else
             <div class="text-center py-5">
                 <i class="fas fa-chart-line" style="font-size: 4rem; color: #cbd5e1;"></i>
                 <p class="mt-3 text-muted">{{ __('dashboard.please_select_a_company') }}</p>
             </div>
         @endif
     </div>

@extends('layout.master')
@section('title', 'Dashboard | Quotation')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_quotations') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.quotations') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addMarketingQuotationModal">
                                    {{ __('dashboard.add_quotation') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>

                                                <th>{{ __('dashboard.quotation_number') }}</th>
                                                <th>{{ __('dashboard.agent_name') }}</th>
                                                <th>{{ __('dashboard.agent_name') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.client_name') }}</th>

                                                <th>{{ __('dashboard.quotation_amount') }}</th>
                                                <th>{{ __('dashboard.description') }}</th>
                                                <th>{{ __('dashboard.logo') }}</th>
                                                <th>{{ __('dashboard.status') }}</th>
                                                <th>{{ __('dashboard.approve_at') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($quotations as $quotation)
                                                <tr>
                                                    <td>{{ $quotation->quotation_number }}</td>
                                                    <td>{{ $quotation->agent->name ?? '-' }}</td>
                                                    <td>{{ $quotation->manual_agent_name ?? '-' }}</td>
                                                    <td>{{ $quotation->branch->name ?? '-' }}</td>
                                                    <td>{{ $quotation->client_name }}</td>

                                                    <td>{{ number_format($quotation->quotation_amount, 2) }}</td>
                                                    <td class="ckeditor-content"> {!! $quotation->description ?: nl2br(e($quotation->description)) !!}</td>
                                                    <td>
                                                        @if ($quotation->logo)
                                                            <img src="{{ asset('storage/' . $quotation->logo) }}"
                                                                alt="Logo" style="height:40px;">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($quotation->status === 'pending')
                                                            <span
                                                                class="badge bg-warning text-dark">{{ __('dashboard.pending') }}</span>
                                                        @elseif($quotation->status === 'approved')
                                                            <span
                                                                class="badge bg-success">{{ __('dashboard.approved') }}</span>
                                                        @else
                                                            <span
                                                                class="badge bg-danger">{{ __('dashboard.rejected') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $quotation->approved_at ? $quotation->approved_at->format('Y-m-d H:i') : '-' }}
                                                    </td>
                                                    <td>

                                                        <!-- view -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#viewQuotationModal_{{ $quotation->id }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>


                                                        <!-- Edit -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#editQuotationModal_{{ $quotation->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteQuotationModal_{{ $quotation->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Add Marketing Quotation Modal -->
        <div class="modal fade" id="addMarketingQuotationModal" tabindex="-1"
            aria-labelledby="addMarketingQuotationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <form action="{{ route('marketing-quotations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title" id="addMarketingQuotationModalLabel">
                                {{ __('dashboard.add_marketing_quotation') }}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <!-- Marketing Agent (optional now) -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.marketing_agent') }}</label>
                                    <select name="marketing_agent_id" class="form-control">
                                        <option value="">{{ __('dashboard.select_agent_or_leave_blank') }}</option>
                                        @foreach ($marketingAgents as $agent)
                                            <option value="{{ $agent->id }}"
                                                {{ old('marketing_agent_id') == $agent->id ? 'selected' : '' }}>
                                                {{ $agent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('marketing_agent_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Manual Agent Name -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.agent_name') }}</label>
                                    <input type="text" name="manual_agent_name" class="form-control"
                                        value="{{ old('manual_agent_name') }}">
                                    @error('manual_agent_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Branch -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.branch') }}</label>
                                    <select name="branch_id" class="form-control" required>
                                        <option value="">{{ __('dashboard.select_branch') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Quotation Number -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.quotation_number') }}</label>
                                    <input type="text" name="quotation_number" class="form-control"
                                        value="{{ $nextQuotationNumber }}" readonly>
                                    @error('quotation_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Client Name -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.client_name') }}</label>
                                    <input type="text" name="client_name" class="form-control"
                                        value="{{ old('client_name') }}" required>
                                    @error('client_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Client Contact -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.client_contact') }}</label>
                                    <input type="text" name="client_contact" class="form-control"
                                        value="{{ old('client_contact') }}">
                                    @error('client_contact')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.email') }}</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email') }}">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- CR Number -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.client_cr_no') }}</label>
                                    <input type="text" name="cr_no" class="form-control"
                                        value="{{ old('cr_no') }}">
                                    @error('cr_no')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <!-- VAT Number -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.client_vat_no') }}</label>
                                    <input type="text" name="vat_no" class="form-control"
                                        value="{{ old('vat_no') }}">
                                    @error('vat_no')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>


                                <!-- Account Number -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.account_number') }}</label>
                                    <input type="text" name="account_number" class="form-control"
                                        value="{{ old('account_number') }}">
                                    @error('account_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Account Number -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.bank_name') }}</label>
                                    <input type="text" name="bank_name" class="form-control"
                                        value="{{ old('bank_name') }}">
                                    @error('bank_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>




                                <!-- Quotation Amount -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.quotation_amount') }}</label>
                                    <input type="number" step="0.01" name="quotation_amount" class="form-control"
                                        value="" required>
                                    @error('quotation_amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="col-12">
                                    <label class="form-label">{{ __('dashboard.description') }}</label>
                                    <textarea name="description" id="summernote" rows="4" class="form-control">{{ old('description') }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Logo Upload -->
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('dashboard.logo') }}</label>
                                    <input type="file" name="logo" class="form-control">
                                    @error('logo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('dashboard.status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                            {{ __('dashboard.pending') }}
                                        </option>
                                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>
                                            {{ __('dashboard.approved') }}</option>
                                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>
                                            {{ __('dashboard.rejected') }}</option>
                                    </select>
                                    @error('status')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                {{ __('dashboard.save') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        @foreach ($quotations as $quotation)
            <!-- View Marketing Quotation Modal -->
            <div class="modal fade" id="viewQuotationModal_{{ $quotation->id }}" tabindex="-1"
                aria-labelledby="viewQuotationModalLabel_{{ $quotation->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content p-3">

                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="viewQuotationModalLabel_{{ $quotation->id }}">
                                {{ __('dashboard.view_marketing_quotation') }}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="letter p-4" style="border:1px solid #ccc; border-radius:8px; background:#fff;">
                                <!-- HEADER -->
                                <div class="header d-flex justify-content-between align-items-center">

                                    <!-- Company Info (RIGHT in RTL) -->
                                    <div class="company-info text-right">
                                        <h5 class="mb-1">
                                            {{ $quotation->branch->company->legal_name ?? __('dashboard.company_name') }}
                                        </h5>

                                        <div class="small">
                                            {{ __('dashboard.cr_number') }} :
                                            {{ $quotation->branch->company->cr_number ?? '-' }} <br>
                                            {{ __('dashboard.vat_number') }} :
                                            {{ $quotation->branch->company->vat_number ?? '-' }} <br>
                                            {{ __('dashboard.email') }} : {{ $quotation->branch->company->email ?? '-' }}
                                            <br>
                                            {{ __('dashboard.phone') }} : {{ $quotation->branch->company->phone ?? '-' }}
                                        </div>
                                    </div>

                                    <!-- Company Logo (LEFT in RTL) -->
                                    <div class="company-logo text-left">
                                        @if ($quotation->branch->company->logo)
                                            <img src="{{ asset('storage/' . $quotation->branch->company->logo) }}"
                                                height="80">
                                        @endif
                                    </div>

                                </div>

                                <!-- Header / Company Logo -->
                                <div class="text-center mb-4">
                                    @if ($quotation->logo)
                                        <img src="{{ asset('storage/' . $quotation->logo) }}" alt="Logo"
                                            style="height:80px;">
                                    @endif
                                    <h4 class="mt-2">{{ __('dashboard.marketing_quotation') }}</h4>
                                    <p>{{ $quotation->quotation_number }}</p>
                                </div>

                                <!-- Sender / Agent Info -->
                                <div class="mb-3">
                                    @if (!empty($quotation->agent->name))
                                        <strong>{{ __('dashboard.agent_name') }}:</strong>
                                        {{ $quotation->agent->name ?? '-' }}<br>
                                    @else
                                        <strong>{{ __('dashboard.agent_name') }}:</strong>
                                        {{ $quotation->manual_agent_name ?? '-' }}
                                    @endif


                                </div>

                                <!-- Branch & Client Info -->
                                <div class="mb-3">
                                    <strong>{{ __('dashboard.branch') }}:</strong>
                                    {{ $quotation->branch->name ?? '-' }}<br>
                                    <strong>{{ __('dashboard.client_name') }}:</strong>
                                    {{ $quotation->client_name }}<br>
                                    <strong>{{ __('dashboard.email') }}:</strong>
                                    {{ $quotation->email ?? '-' }}<br>
                                    <strong>{{ __('dashboard.vat_number') }}:</strong>
                                    {{ $quotation->vat_no ?? '-' }}<br>
                                    <strong>{{ __('dashboard.cr_number') }}:</strong>
                                    {{ $quotation->cr_no ?? '-' }}<br>
                                    <strong>{{ __('dashboard.client_contact') }}:</strong>
                                    {{ $quotation->client_contact ?? '-' }}
                                </div>

                                <!-- Quotation Amount & Status -->
                                <div class="mb-3">
                                    <strong>{{ __('dashboard.quotation_amount') }}:</strong>
                                    {{ number_format($quotation->quotation_amount, 2) }}<br>
                                    <strong>{{ __('dashboard.status') }}:</strong> {{ ucfirst($quotation->status) }}<br>
                                    <strong>{{ __('dashboard.approve_at') }}:</strong>
                                    {{ $quotation->approved_at?->format('Y-m-d H:i') ?? '-' }}
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <strong>{{ __('dashboard.description') }}:</strong>
                                    <div class="ckeditor-content"
                                        style="border:1px solid #ccc; padding:10px; border-radius:5px; background:#f9f9f9;">
                                        {!! $quotation->description ?: nl2br(e($quotation->description)) !!}
                                    </div>
                                </div>
                                <p class="text-center text-muted">
                                    {{ __('dashboard.generated_on') }} : {{ now()->format('Y-m-d H:i') }}
                                    <strong>{{ __('dashboard.account_number') }}:</strong>
                                    {{ $quotation->account_number ?? '-' }}
                                    <strong>{{ __('dashboard.bank_name') }}:</strong> {{ $quotation->bank_name ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="modal-footer border-0">
                            <!-- Print Button -->
                            <a href="{{ route('marketing-quotations.print', $quotation->id) }}" target="_blank"
                                class="btn btn-primary">
                                <i class="fas fa-print"></i> {{ __('dashboard.print') }}
                            </a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                {{ __('dashboard.close') }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach



        @foreach ($quotations as $quotation)
            <!-- Edit Marketing Quotation Modal -->
            <div class="modal fade" id="editQuotationModal_{{ $quotation->id }}" tabindex="-1"
                aria-labelledby="editQuotationModalLabel_{{ $quotation->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('marketing-quotations.update', $quotation->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <h5 class="modal-title" id="editQuotationModalLabel_{{ $quotation->id }}">
                                    {{ __('dashboard.edit_marketing_quotation') }}</h5>
                                <button type="button" class="close text-dark" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="row g-3">

                                    <!-- Marketing Agent (optional now) -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.marketing_agent') }}</label>
                                        <select name="marketing_agent_id" class="form-control">
                                            <option value="">{{ __('dashboard.select_agent_or_leave_blank') }}
                                            </option>
                                            @foreach ($marketingAgents as $agent)
                                                <option value="{{ $agent->id }}"
                                                    {{ $quotation->marketing_agent_id == $agent->id ? 'selected' : '' }}>
                                                    {{ $agent->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('marketing_agent_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Manual Agent Name -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.agent_name') }}</label>
                                        <input type="text" name="manual_agent_name" class="form-control"
                                            value="{{ $quotation->manual_agent_name }}">
                                        @error('manual_agent_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Branch -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.branch') }}</label>
                                        <select name="branch_id" class="form-control" required>
                                            <option value="">{{ __('dashboard.select_branch') }}</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ $quotation->branch_id == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Quotation Number -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.quotation_number') }}</label>
                                        <input type="text" name="quotation_number" class="form-control"
                                            value="{{ $quotation->quotation_number }}" required readonly>
                                        @error('quotation_number')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Client Name -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.client_name') }}</label>
                                        <input type="text" name="client_name" class="form-control"
                                            value="{{ $quotation->client_name }}" required>
                                        @error('client_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Client Contact -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.client_contact') }}</label>
                                        <input type="text" name="client_contact" class="form-control"
                                            value="{{ $quotation->client_contact }}">
                                        @error('client_contact')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.email') }}</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ $quotation->email }}">
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- CR Number -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.client_cr_no') }}</label>
                                        <input type="text" name="cr_no" class="form-control"
                                            value="{{ $quotation->cr_no }}">
                                        @error('cr_no')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <!-- VAT Number -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.client_vat_no') }}</label>
                                        <input type="text" name="vat_no" class="form-control"
                                            value="{{ $quotation->vat_no }}">
                                        @error('vat_no')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Account Number -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.account_number') }}</label>
                                        <input type="text" name="account_number" class="form-control"
                                            value="{{ $quotation->account_number }}">
                                        @error('account_number')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Client Contact -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.client_contact') }}</label>
                                        <input type="text" name="client_contact" class="form-control"
                                            value="{{ $quotation->client_contact }}">
                                        @error('client_contact')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Quotation Amount -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.quotation_amount') }}</label>
                                        <input type="number" step="0.01" name="quotation_amount"
                                            class="form-control" value="{{ $quotation->quotation_amount }}" required>
                                        @error('quotation_amount')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Logo Upload -->
                                    <div class="col-md-12">
                                        <label class="form-label">{{ __('dashboard.logo') }}</label>
                                        <input type="file" name="logo" class="form-control">
                                        @if ($quotation->logo)
                                            <small>Current: <a href="{{ asset('storage/' . $quotation->logo) }}"
                                                    target="_blank">View</a></small>
                                        @endif
                                        @error('logo')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>



                                    <!-- Description -->
                                    <div class="col-12">
                                        <label class="form-label">{{ __('dashboard.description') }}</label>
                                        <textarea name="description" rows="4" id="edit_quotation_{{ $quotation->id }}"
                                            class="form-contro edit-quotation-body">{{ $quotation->description }}</textarea>
                                        @error('description')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-12">
                                        <label class="form-label">{{ __('dashboard.status') }}</label>
                                        <select name="status" class="form-control">
                                            <option value="pending"
                                                {{ $quotation->status == 'pending' ? 'selected' : '' }}>
                                                {{ __('dashboard.pending') }}</option>
                                            <option value="approved"
                                                {{ $quotation->status == 'approved' ? 'selected' : '' }}>
                                                {{ __('dashboard.approved') }}</option>
                                            <option value="rejected"
                                                {{ $quotation->status == 'rejected' ? 'selected' : '' }}>
                                                {{ __('dashboard.rejected') }}</option>
                                        </select>
                                        @error('status')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        @endforeach


        @foreach ($quotations as $quotation)
            <!-- Delete Quotation Modal -->
            <div class="modal fade" id="deleteQuotationModal_{{ $quotation->id }}" tabindex="-1"
                aria-labelledby="deleteQuotationModalLabel_{{ $quotation->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteQuotationModalLabel_{{ $quotation->id }}">
                                {{ __('dashboard.delete_marketing_quotation') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('marketing-quotations.destroy', $quotation->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $quotation->quotation_number }}</strong>?
                                </p>
                            </div>

                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-danger">{{ __('dashboard.yes_delete') }}</button>
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        @endforeach


    </div>
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('addMarketingQuotationModal'));
                myModal.show();
            });
        </script>
    @endif

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        ClassicEditor
            .create(document.querySelector('#summernote'))
            .catch(error => {
                console.error(error);
            });
        let editors = {};

        $(document).ready(function() {
            // When any edit modal opens
            $('[id^="editQuotationModal_"]').on('shown.bs.modal', function() {
                let modal = $(this);
                let textarea = modal.find('textarea.edit-quotation-body');
                let textareaId = textarea.attr('id');

                // If already initialized, do nothing
                if (!editors[textareaId]) {
                    ClassicEditor
                        .create(document.querySelector('#' + textareaId))
                        .then(editor => {
                            editors[textareaId] = editor;
                            editor.ui.view.editable.element.style.height = '300px';
                        })
                        .catch(error => console.error(error));
                }
            });

            // When any edit modal closes
            $('[id^="editQuotationModal_"]').on('hidden.bs.modal', function() {
                let modal = $(this);
                let textarea = modal.find('textarea.edit-quotation-body');
                let textareaId = textarea.attr('id');

                // Destroy editor instance
                if (editors[textareaId]) {
                    editors[textareaId].destroy()
                        .then(() => {
                            editors[textareaId] = null;
                        })
                        .catch(error => console.error(error));
                }
            });
        });
    </script>

@endsection

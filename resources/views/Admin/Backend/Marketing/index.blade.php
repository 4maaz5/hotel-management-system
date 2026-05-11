@extends('layout.master')
@section('title', 'Dashboard | Agent')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_agents') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.agents') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addMarketingAgentModal">
                                    {{ __('dashboard.add_agent') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.company_name') }}</th>
                                                <th>{{ __('dashboard.brand_name') }}</th>
                                                <th>{{ __('dashboard.branch_name') }}</th>
                                                <th>{{ __('dashboard.agent_name') }}</th>
                                                <th>{{ __('dashboard.contact_person') }}</th>
                                                <th>{{ __('dashboard.email') }}</th>
                                                <th>{{ __('dashboard.phone') }}</th>
                                                <th>{{ __('dashboard.commission') }} (%)</th>
                                                <th>{{ __('dashboard.type') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($agents as $agent)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $agent->company->name ?? '-' }}</td>
                                                    <td>{{ $agent->brand->name ?? '-' }}</td>
                                                    <td>{{ $agent->branch->name ?? '-' }}</td>
                                                    <td>{{ $agent->name }}</td>
                                                    <td>{{ $agent->contact_person ?? '-' }}</td>
                                                    <td>{{ $agent->email ?? '-' }}</td>
                                                    <td>{{ $agent->phone ?? '-' }}</td>
                                                    <td>{{ number_format($agent->commission_percent, 2) }}</td>
                                                    <td>{{ ucfirst($agent->type) }}</td>
                                                    <td>
                                                        <!-- Edit -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#editAgentModal{{ $agent->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteAgentModal_{{ $agent->id }}">
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


        <!-- Add Marketing Agent Modal -->
        <div class="modal fade" id="addMarketingAgentModal" tabindex="-1" aria-labelledby="addMarketingAgentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <form action="{{ route('marketing-agents.store') }}" method="POST">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title" id="addMarketingAgentModalLabel">
                                {{ __('dashboard.add_marketing_agent') }}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <!-- Company -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.company_name') }}</label>
                                    <select name="company_id" class="form-control" required>
                                        <option value="">{{ __('dashboard.select_company') }}</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">
                                                {{ $company->legal_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Type -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.type') }}</label>
                                    <select name="type" class="form-control">
                                        <option value="agent" selected>{{ __('dashboard.agent') }}</option>
                                        <option value="company">{{ __('dashboard.company') }}</option>
                                    </select>
                                </div>

                                <!-- Name -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.agent_name') }}</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>

                                <!-- Contact Person -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.contact_person') }}</label>
                                    <input type="text" name="contact_person" class="form-control">
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.email') }}</label>
                                    <input type="email" name="email" class="form-control">
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.phone') }}</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>

                                <!-- Branch (Optional) -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.branch') }}
                                        ({{ __('dashboard.optional') }})</label>
                                    <select name="branch_id" class="form-control">
                                        <option value="">{{ __('dashboard.select_branch') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Brand (Optional) -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.brand') }}
                                        ({{ __('dashboard.optional') }})</label>
                                    <select name="brand_id" class="form-control">
                                        <option value="">{{ __('dashboard.select_brand') }}</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Commission Percentage -->
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('dashboard.commission') }} (%)</label>
                                    <input type="number" name="commission_percent" step="0.01" value="5.00"
                                        class="form-control" required>
                                </div>
                                <!-- Address -->
                                <div class="col-12">
                                    <label class="form-label">{{ __('dashboard.address') }}</label>
                                    <textarea name="address" rows="3" class="form-control"></textarea>
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

        @foreach ($agents as $agent)
            <div class="modal fade" id="editAgentModal{{ $agent->id }}" tabindex="-1"
                aria-labelledby="editMarketingAgentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <form id="editMarketingAgentForm" action="{{ route('marketing-agents.update', $agent->id) }}"
                            method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <h5 class="modal-title" id="editMarketingAgentModalLabel">
                                    {{ __('dashboard.edit_marketing_agent') }}</h5>
                                <button type="button" class="close text-dark" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="row g-3">

                                    <!-- Company -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.company') }}</label>
                                        <select name="company_id" class="form-control" required>
                                            <option value="">{{ __('dashboard.select_company') }}</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}"
                                                    {{ $agent->company_id == $company->id ? 'selected' : '' }}>
                                                    {{ $company->legal_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Type -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.type') }}</label>
                                        <select name="type" class="form-control">
                                            <option value="agent" {{ $agent->type == 'agent' ? 'selected' : '' }}>
                                                {{ __('dashboard.agent') }}
                                            </option>
                                            <option value="company" {{ $agent->type == 'company' ? 'selected' : '' }}>
                                                {{ __('dashboard.company') }}</option>
                                        </select>
                                    </div>

                                    <!-- Name -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.agent_name') }}</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $agent->name }}" required>
                                    </div>

                                    <!-- Contact Person -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.contact_person') }}</label>
                                        <input type="text" name="contact_person" class="form-control"
                                            value="{{ $agent->contact_person }}">
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.email') }}</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ $agent->email }}">
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.phone') }}</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ $agent->phone }}">
                                    </div>

                                    <!-- Branch (Optional) -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.branch') }}
                                            ({{ __('dashboard.optional') }})</label>
                                        <select name="branch_id" class="form-control">
                                            <option value="">{{ __('dashboard.select_branch') }}</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ $agent->branch_id == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Brand (Optional) -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.brand') }}
                                            ({{ __('dashboard.optional') }})</label>
                                        <select name="brand_id" class="form-control">
                                            <option value="">{{ __('dashboard.select_brand') }}</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ $agent->brand_id == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Commission Percentage -->
                                    <div class="col-md-12">
                                        <label class="form-label">{{ __('dashboard.commission') }} (%)</label>
                                        <input type="number" name="commission_percent" step="0.01"
                                            class="form-control" value="{{ $agent->commission_percent }}" required>
                                    </div>

                                    <!-- Address -->
                                    <div class="col-md-12">
                                        <label class="form-label">{{ __('dashboard.address') }}</label>
                                        <textarea name="address" rows="3" class="form-control">{{ $agent->address }}</textarea>
                                    </div>

                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    {{ __('dashboard.cancel') }}
                                </button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @foreach ($agents as $agent)
            <div class="modal fade" id="deleteAgentModal_{{ $agent->id }}" tabindex="-1"
                aria-labelledby="deleteAgentModalLabel_{{ $agent->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteAgentModalLabel_{{ $agent->id }}">
                                {{ __('dashboard.delete_marketing_agent') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('marketing-agents.destroy', $agent->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $agent->name }}</strong>?
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



@endsection

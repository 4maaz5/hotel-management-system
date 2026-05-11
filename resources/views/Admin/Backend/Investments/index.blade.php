@extends('layout.master')
@section('title', 'Dashboard | Warehouse')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_investments') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.partners') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#partnerInvestmentModal">
                                    Add Investments
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Nationality</th>
                                                <th>Investment</th>
                                                <th>Share %</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    Ahmed Al Saud<br>
                                                    <small class="text-muted">ahmed.owner@email.com</small>
                                                </td>
                                                <td><span class="badge bg-primary">Owner</span></td>
                                                <td>Saudi</td>
                                                <td>500,000</td>
                                                <td>60%</td>
                                                <td><span class="badge bg-success">Active</span></td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary" data-toggle="modal"
                                                        data-target="#editPartnerInvestmentModal">Edit</button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-toggle="modal" data-target="#deletePartnerModal">
                                                        Delete
                                                    </button>



                                                </td>
                                            </tr>

                                            <tr>
                                                <td>2</td>
                                                <td>
                                                    John Smith<br>
                                                    <small class="text-muted">john@email.com</small>
                                                </td>
                                                <td><span class="badge bg-info">Investor</span></td>
                                                <td>American</td>
                                                <td>200,000</td>
                                                <td>25%</td>
                                                <td><span class="badge bg-success">Active</span></td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary">Edit</button>
                                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add / Edit Partner Investment Modal -->
        <div class="modal fade" id="partnerInvestmentModal" tabindex="-1" aria-labelledby="partnerInvestmentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="partnerInvestmentModalLabel">
                            {{ __('dashboard.add_investment') }}
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <form id="partnerInvestmentForm" method="POST" action="#">
                            @csrf

                            <!-- For Edit -->
                            <input type="hidden" name="id">
                            <input type="hidden" name="company_partner_id">

                            <div class="row">

                                <!-- Partner -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.partner') }}</label>
                                    <select name="company_partner_id_display" class="form-control" disabled>
                                        <option value="">{{ __('dashboard.select_partner') }}</option>
                                        <option value="1">Ahmed Al Saud</option>
                                        <option value="2">John Smith</option>
                                    </select>
                                </div>

                                <!-- Amount -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.amount') }}</label>
                                    <input type="number" step="0.01" name="amount" class="form-control" required>
                                </div>

                                <!-- Investment Date -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.investment_date') }}</label>
                                    <input type="date" name="investment_date" class="form-control" required>
                                </div>

                                <!-- Payment Method -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.payment_method') }}</label>
                                    <select name="payment_method" class="form-control" required>
                                        <option value="">{{ __('dashboard.select') }}</option>
                                        <option value="cash">{{ __('dashboard.cash') }}</option>
                                        <option value="bank">{{ __('dashboard.bank') }}</option>
                                        <option value="cheque">{{ __('dashboard.cheque') }}</option>
                                    </select>
                                </div>

                                <!-- Reference Number -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.reference_number') }}</label>
                                    <input type="text" name="reference_number" class="form-control">
                                </div>

                                <!-- Notes -->
                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.notes') }}</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>

                            </div>

                            <!-- Footer Buttons -->
                            <div class="text-end mt-3">
                                <button type="reset" class="btn btn-secondary">
                                    {{ __('dashboard.reset') }}
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('dashboard.save') }}
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Edit Partner Investment Modal -->
        <div class="modal fade" id="editPartnerInvestmentModal" tabindex="-1"
            aria-labelledby="editPartnerInvestmentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPartnerInvestmentModalLabel">
                            {{ __('dashboard.edit_investment') }}
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <form id="editPartnerInvestmentForm" method="POST" action="#">
                            @csrf
                            @method('PUT')

                            <!-- Hidden for Edit -->
                            <input type="hidden" name="id">
                            <input type="hidden" name="company_partner_id">

                            <div class="row">

                                <!-- Partner (read-only) -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.partner') }}</label>
                                    <input type="text" class="form-control" name="partner_name" readonly>
                                </div>

                                <!-- Amount -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.amount') }}</label>
                                    <input type="number" step="0.01" name="amount" class="form-control" required>
                                </div>

                                <!-- Investment Date -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.investment_date') }}</label>
                                    <input type="date" name="investment_date" class="form-control" required>
                                </div>

                                <!-- Payment Method -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.payment_method') }}</label>
                                    <select name="payment_method" class="form-control" required>
                                        <option value="cash">{{ __('dashboard.cash') }}</option>
                                        <option value="bank">{{ __('dashboard.bank') }}</option>
                                        <option value="cheque">{{ __('dashboard.cheque') }}</option>
                                    </select>
                                </div>

                                <!-- Reference Number -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.reference_number') }}</label>
                                    <input type="text" name="reference_number" class="form-control">
                                </div>

                                <!-- Notes -->
                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.notes') }}</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>

                            </div>

                            <!-- Footer Buttons -->
                            <div class="text-end mt-3">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    {{ __('dashboard.cancel') }}
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('dashboard.update') }}
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>



        <div class="modal fade" id="deletePartnerModal" tabindex="-1" aria-labelledby="deletePartnerModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deletePartnerModalLabel">
                            {{ __('dashboard.delete_classify') }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="deleteProductForm" method="POST" action="{{ route('dashboard.products.delete') }}">
                        @csrf
                        @method('DELETE')

                        <input type="hidden" name="id" id="delete_product_id">

                        <div class="modal-body text-center">
                            <p>
                                {{ __('dashboard.classify_delete') }}
                                <strong id="delete_product_name"></strong>?
                            </p>
                        </div>

                        <div class="modal-footer justify-content-center">
                            <button type="submit" class="btn btn-danger">
                                {{ __('dashboard.yes_delete') }}
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>




    </div>

@endsection

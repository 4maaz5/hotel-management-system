@extends('layout.master')
@section('title', 'Dashboard | Employee')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Edit Payroll</h4>
                                <a href="#">
                                    <button class="btn btn-secondary">Back</button>
                                </a>
                            </div>
                            <div class="card-body">

                                <form>
                                    <!-- Employee Image -->
                                    <div class="form-group text-center">
                                        <label class="font-weight-semibold d-block mb-2">Employee Image</label>

                                        <!-- Circle Image Preview -->
                                        {{-- <img id="editImagePreview" src="https://randomuser.me/api/portraits/men/75.jpg"
                                            class="rounded-circle border border-secondary mb-2"
                                            style="width: 140px; height: 140px; object-fit: cover;"> --}}

                                        <!-- File Upload -->
                                        <div>
                                            <input type="file" id="editEmployeeThumbnail" accept="image/*"
                                                class="d-none">
                                            <label for="editEmployeeThumbnail" class="btn btn-sm btn-primary">Upload
                                                Image</label>
                                        </div>
                                    </div>

                                    <!-- Company Information -->
                                    <h5 class="mt-4">Company Information</h5>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Company Name</label>
                                            <input type="text" class="form-control" value="ABC Pvt Ltd">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Company Address</label>
                                            <input type="text" class="form-control" value="123 Main Street, City">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Tax ID</label>
                                            <input type="text" class="form-control" value="123456789">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Pay Period</label>
                                            <input type="text" class="form-control"
                                                placeholder="e.g. 01/05/2025 - 31/05/2025" value="01/05/2025 - 31/05/2025">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Pay Date</label>
                                            <input type="date" class="form-control" value="2025-05-31">
                                        </div>
                                    </div>

                                    <!-- Employee Information -->
                                    <h5 class="mt-4">Employee Information</h5>
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label>Employee ID</label>
                                            <input type="text" class="form-control" value="EMP-1001">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Employee Name</label>
                                            <input type="text" class="form-control" value="John Doe">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Department</label>
                                            <input type="text" class="form-control" value="Finance">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Designation</label>
                                            <input type="text" class="form-control" value="Accountant">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Bank Name</label>
                                            <input type="text" class="form-control" value="ABC Bank">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Account Number</label>
                                            <input type="text" class="form-control" value="1234567890">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>PAN Number</label>
                                            <input type="text" class="form-control" value="ABCDE1234F">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="form-group col-md-4">
                                            <label>Payment Mode</label>
                                            <select class="form-control">
                                                <option selected>Bank Transfer</option>
                                                <option>Cash</option>
                                                <option>Cheque</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Earnings -->
                                    <h5 class="mt-4">Earnings</h5>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Description</label>
                                            <input type="text" class="form-control" value="Basic Salary">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Amount</label>
                                            <input type="number" class="form-control" value="4200">
                                        </div>
                                    </div>

                                    <!-- Deductions -->
                                    <h5 class="mt-4">Deductions</h5>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Description</label>
                                            <input type="text" class="form-control" value="Income Tax">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Amount</label>
                                            <input type="number" class="form-control" value="320">
                                        </div>
                                    </div>

                                    <!-- Leave Information (Optional) -->
                                    <h5 class="mt-4">Leave Information (Optional)</h5>
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label>Leave Type</label>
                                            <input type="text" class="form-control" value="Annual Leave">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Entitled</label>
                                            <input type="number" class="form-control" value="12">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Availed</label>
                                            <input type="number" class="form-control" value="5">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Balance</label>
                                            <input type="number" class="form-control" value="7">
                                        </div>
                                    </div>

                                    <!-- Additional Notes -->
                                    <h5 class="mt-4">Additional Notes</h5>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label>Notes</label>
                                            <textarea class="form-control" rows="3">Any additional information...</textarea>
                                        </div>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="text-end mt-3">
                                        <button type="reset" class="btn btn-secondary me-2">Reset</button>
                                        <button type="submit" class="btn btn-primary">Update Payslip</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@extends('layout.master')
@section('title', 'Dashboard | Employee')
@section('main')
    <!-- Main Content -->
    {{-- <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12 ">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Add New Employee</h4>
                                <a href="{{ route('dashboard.employee.index') }}">
                                    <button class="btn btn-secondary">Back</button>
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Name</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Employee ID</label>
                                        <input type="number" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Email</label>
                                        <input type="email" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Phone</label>
                                        <input type="number" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>DOB</label>
                                        <input type="date" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Gender</label>
                                        <select class="form-control">
                                            <option>Male</option>
                                            <option>Female</option>
                                            <optiom>Others</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Designation</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Join Date</label>
                                        <input type="date" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Contract Expiry</label>
                                        <input type="date" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Residence Expiry Date</label>
                                        <input type="date" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Basic Sallary</label>
                                        <input type="number" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Allowances</label>
                                        <input type="number" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Photo</label>
                                        <input type="file" class="form-control">
                                    </div>

                                </div>
                                <button class="btn btn-success">Submit</button>

                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4>Image Check</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">Image Check</label>
                                    <div class="row gutters-sm">
                                        <div class="col-6 col-sm-4">
                                            <label class="imagecheck mb-4">
                                                <input name="imagecheck" type="checkbox" value="1"
                                                    class="imagecheck-input" />
                                                <span class="imagecheck-figure">
                                                    <img src="assets/img/blog/img01.png" alt="}"
                                                        class="imagecheck-image">
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-6 col-sm-4">
                                            <label class="imagecheck mb-4">
                                                <input name="imagecheck" type="checkbox" value="2"
                                                    class="imagecheck-input" checked />
                                                <span class="imagecheck-figure">
                                                    <img src="assets/img/blog/img02.png" alt="}"
                                                        class="imagecheck-image">
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-6 col-sm-4">
                                            <label class="imagecheck mb-4">
                                                <input name="imagecheck" type="checkbox" value="3"
                                                    class="imagecheck-input" />
                                                <span class="imagecheck-figure">
                                                    <img src="assets/img/blog/img03.png" alt="}"
                                                        class="imagecheck-image">
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-6 col-sm-4">
                                            <label class="imagecheck mb-4">
                                                <input name="imagecheck" type="checkbox" value="4"
                                                    class="imagecheck-input" checked />
                                                <span class="imagecheck-figure">
                                                    <img src="assets/img/blog/img04.png" alt="}"
                                                        class="imagecheck-image">
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-6 col-sm-4">
                                            <label class="imagecheck mb-4">
                                                <input name="imagecheck" type="checkbox" value="5"
                                                    class="imagecheck-input" />
                                                <span class="imagecheck-figure">
                                                    <img src="assets/img/blog/img05.png" alt="}"
                                                        class="imagecheck-image">
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-6 col-sm-4">
                                            <label class="imagecheck mb-4">
                                                <input name="imagecheck" type="checkbox" value="6"
                                                    class="imagecheck-input" />
                                                <span class="imagecheck-figure">
                                                    <img src="assets/img/blog/img06.png" alt="}"
                                                        class="imagecheck-image">
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4>Color</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Simple</label>
                                    <input type="text" class="form-control colorpickerinput">
                                </div>
                                <div class="form-group">
                                    <label>Pick Your Color</label>
                                    <div class="input-group colorpickerinput">
                                        <input type="text" class="form-control">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <i class="fas fa-fill-drip"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Color Input</label>
                                    <div class="row gutters-xs">
                                        <div class="col-auto">
                                            <label class="colorinput">
                                                <input name="color" type="checkbox" value="primary"
                                                    class="colorinput-input" />
                                                <span class="colorinput-color bg-primary"></span>
                                            </label>
                                        </div>
                                        <div class="col-auto">
                                            <label class="colorinput">
                                                <input name="color" type="checkbox" value="secondary"
                                                    class="colorinput-input" />
                                                <span class="colorinput-color bg-secondary"></span>
                                            </label>
                                        </div>
                                        <div class="col-auto">
                                            <label class="colorinput">
                                                <input name="color" type="checkbox" value="danger"
                                                    class="colorinput-input" />
                                                <span class="colorinput-color bg-danger"></span>
                                            </label>
                                        </div>
                                        <div class="col-auto">
                                            <label class="colorinput">
                                                <input name="color" type="checkbox" value="warning"
                                                    class="colorinput-input" />
                                                <span class="colorinput-color bg-warning"></span>
                                            </label>
                                        </div>
                                        <div class="col-auto">
                                            <label class="colorinput">
                                                <input name="color" type="checkbox" value="info"
                                                    class="colorinput-input" />
                                                <span class="colorinput-color bg-info"></span>
                                            </label>
                                        </div>
                                        <div class="col-auto">
                                            <label class="colorinput">
                                                <input name="color" type="checkbox" value="success"
                                                    class="colorinput-input" />
                                                <span class="colorinput-color bg-success"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

    </div> --}}
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Add Payslip</h4>
                                <a href="#">
                                    <button class="btn btn-secondary">Back</button>
                                </a>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="form-group text-center">
                                        <label class="font-weight-semibold d-block mb-2">Branch Logo</label>

                                        <!-- Circle Image Preview -->
                                        {{-- <img id="imagePreview" src="https://randomuser.me/api/portraits/men/75.jpg"
                                            class="rounded-circle border border-secondary mb-2"
                                            style="width: 140px; height: 140px; object-fit: cover;"> --}}

                                        <!-- File Upload -->
                                        <div>
                                            <input type="file" id="employeeThumbnail" accept="image/*" class="d-none">
                                            <label for="employeeThumbnail" class="btn btn-sm btn-primary">Upload
                                                Image</label>
                                        </div>
                                    </div>

                                    <h5 class="mt-4">Company Information</h5>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Company Name</label>
                                            <input type="text" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Company Address</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Tax ID</label>
                                            <input type="text" class="form-control">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Pay Period</label>
                                            <input type="text" class="form-control"
                                                placeholder="e.g. 01/05/2025 - 31/05/2025">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Pay Date</label>
                                            <input type="date" class="form-control">
                                        </div>
                                    </div>

                                    <h5 class="mt-4">Employee Information</h5>
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label>Employee ID</label>
                                            <input type="text" class="form-control" placeholder="EMP-1001">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Employee Name</label>
                                            <input type="text" class="form-control">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Department</label>
                                            <input type="text" class="form-control">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Designation</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Bank Name</label>
                                            <input type="text" class="form-control">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Account Number</label>
                                            <input type="text" class="form-control">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>PAN Number</label>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="form-group col-md-4">
                                            <label>Payment Mode</label>
                                            <select class="form-control">
                                                <option>Bank Transfer</option>
                                                <option>Cash</option>
                                                <option>Cheque</option>
                                            </select>
                                        </div>
                                    </div>

                                    <h5 class="mt-4">Earnings</h5>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Description</label>
                                            <input type="text" class="form-control" placeholder="Basic Salary">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Amount</label>
                                            <input type="number" class="form-control" value="0.00">
                                        </div>
                                    </div>

                                    <h5 class="mt-4">Deductions</h5>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Description</label>
                                            <input type="text" class="form-control" placeholder="Income Tax">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Amount</label>
                                            <input type="number" class="form-control" value="0.00">
                                        </div>
                                    </div>

                                    <h5 class="mt-4">Leave Information (Optional)</h5>
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label>Leave Type</label>
                                            <input type="text" class="form-control" placeholder="Annual Leave">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Entitled</label>
                                            <input type="number" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Availed</label>
                                            <input type="number" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Balance</label>
                                            <input type="number" class="form-control" value="0">
                                        </div>
                                    </div>

                                    <h5 class="mt-4">Additional Notes</h5>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label>Notes</label>
                                            <textarea class="form-control" rows="3" placeholder="Any additional information..."></textarea>
                                        </div>
                                    </div>

                                    <div class="text-end mt-3">
                                        <button type="reset" class="btn btn-secondary me-2">Reset</button>
                                        <button type="submit" class="btn btn-primary">Save Payslip</button>
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

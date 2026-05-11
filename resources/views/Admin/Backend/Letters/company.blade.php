@extends('layout.master')
@section('title', 'Dashboard | Letter')
@section('main')
    <!-- Add these in your <head> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.7/jodit.min.css" rel="stylesheet">
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_letters') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.letters') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#letterModal">
                                    {{ __('dashboard.add_letter') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.letter_number') }}</th>
                                                <th>{{ __('dashboard.company_name') }}</th>
                                                <th>{{ __('dashboard.branch_name') }}</th>
                                                <th>{{ __('dashboard.employe_name') }}</th>
                                                <th>{{ __('dashboard.reciever_name') }}</th>
                                                <th>{{ __('dashboard.letter_type') }}</th>
                                                <th>{{ __('dashboard.subject') }}</th>
                                                <th>{{ __('dashboard.hijri_date') }}</th>
                                                <th>{{ __('dashboard.gregorian_date') }}</th>
                                                <th>{{ __('dashboard.view') }}</th>
                                                <th>{{ __('dashboard.print') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($letters as $letter)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $letter->letter_number }}</td>
                                                    <td>{{ $letter->company->legal_name }}</td>
                                                    <td>{{ $letter->branch->name }}</td>
                                                    <td>{{ $letter->employee?->first_name ?? '-' }}</td>
                                                    <td>{{ $letter->receiver_name ?? '-' }}</td>
                                                    <td>{{ ucfirst($letter->letter_type) }}</td>
                                                    <td>{{ $letter->subject }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($letter->hijri_date)->format('Y-m-d') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($letter->gregorian_date)->format('Y-m-d H:i') }}
                                                    </td>
                                                    <td>

                                                        <!-- View Letter Modal Trigger -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#viewLetterModal_{{ $letter->id }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                    </td>
                                                    <td>
                                                        <a href="#" type="button" class=" text-secondary"
                                                            onclick="printLetter('letter_{{ $letter->id }}')">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                    </td>

                                                    <td>
                                                        <!-- Edit -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#editLetterModal_{{ $letter->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteLetterModal_{{ $letter->id }}">
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

        @foreach ($letters as $letter)
            <!-- View Modal -->
            <div class="modal fade" id="viewLetterModal_{{ $letter->id }}" tabindex="-1"
                aria-labelledby="viewLetterModalLabel_{{ $letter->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 900px;">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="viewLetterModalLabel_{{ $letter->id }}">
                                <i class="fas fa-file-alt"></i> {{ __('dashboard.letter_preview') }} -
                                {{ $letter->letter_number }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-0">
                            <!-- Saudi Style Letter Preview -->
                            <div class="letter-preview-container" style="background-color: #f8f9fa; min-height: 500px;">
                                <div class="letter-content" id="letter_{{ $letter->id }}"
                                    style="background: white; margin: 0 auto;
                                 padding: 30px; max-width: 794px; min-height: 1123px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                 position: relative; font-family: 'Arial', sans-serif; text-align: right; direction: rtl;">

                                    <!-- Company Header -->
                                    <div
                                        style="text-align: center; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 0px;">


                                    </div>

                                    <!-- Member Information -->
                                    <div
                                        style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 12px;">
                                        <div style="text-align: right; width: 48%;">
                                            <div style="font-weight: bold; margin-bottom: 5px; color: #2c3e50;">بيانات
                                                المشترك:</div>
                                            <div>شركة:
                                                {{ $letter->company->legal_name ?? $letter->company->name }}
                                            </div>
                                            @if ($letter->company->cr_number)
                                                <div>رقم السجل التجاري: {{ $letter->company->cr_number }}
                                                </div>
                                            @endif
                                            @if ($letter->company->phone)
                                                <div>هاتف: {{ $letter->company->phone }}</div>
                                            @endif
                                            @if ($letter->company->vat_number)
                                                <div>الرقم الموحد: {{ $letter->company->vat_number }}</div>
                                            @endif
                                        </div>

                                        <div style="text-align: center; margin-bottom: 30px; padding-bottom: 15px;">
                                            @if ($letter->letterSetting?->company_logo)
                                                <img src="{{ asset('storage/' . $letter->letterSetting->company_logo) }}"
                                                    style="height: 80px; margin-bottom: 10px;" alt="شعار الشركة">
                                            @endif


                                        </div>

                                        <div
                                            style="text-align: left; direction: ltr; width: 48%; border-left: 1px solid #ccc; padding-left: 15px;">
                                            <div style="font-weight: bold; margin-bottom: 5px; color: #2c3e50;">Member's
                                                Detail:</div>
                                            <div>Company: {{ $letter->company->legal_name ?? $letter->company->name }}
                                            </div>
                                            @if ($letter->company->cr_number)
                                                <div>C.R. No: {{ $letter->company->cr_number }}</div>
                                            @endif
                                            @if ($letter->company->phone)
                                                <div>Tel: {{ $letter->company->phone }}</div>
                                            @endif
                                            @if ($letter->company->vat_number)
                                                <div>Vat Number: {{ $letter->company->vat_number }}</div>
                                            @endif
                                        </div>
                                    </div>



                                    <!-- Letter Information -->
                                    <div
                                        style="margin-bottom: 25px; background-color: #f8f9fa; padding: 15px;
                                     border-right: 4px solid #3498db; border-radius: 5px;">
                                        <div style="margin-bottom: 8px;">
                                            <strong>رقم الخطاب:</strong> {{ $letter->letter_number }}
                                        </div>
                                        <div style="margin-bottom: 8px;">
                                            <strong>التاريخ:</strong> {{ $letter->hijri_date }} هـ الموافق
                                            {{ $letter->gregorian_date }} م
                                            {{-- ->format('Y/m/d') --}}
                                        </div>
                                        <div style="margin-bottom: 8px;">
                                            <strong>الموضوع:</strong> {{ $letter->subject }}
                                        </div>
                                        @if ($letter->employee)
                                            <div style="margin-bottom: 8px;">
                                                <strong>السيد/ة:</strong>
                                                {{ $letter->employee->first_name }}
                                            </div>
                                        @endif
                                        @if ($letter->receiver_name)
                                            <div style="margin-bottom: 8px;">
                                                <strong>السادة:</strong>
                                                {{ $letter->receiver_name }}
                                            </div>
                                        @endif
                                    </div>



                                    <!-- Salutation -->
                                    <div
                                        style="text-align: center; font-size: 18px; margin: 30px 0; font-weight: bold; color: #2c3e50;">
                                        السَّلامُ عَلَيْكُمْ وَرَحْمَةُ اللَّهِ وَبَرَكَاتُهُ،
                                    </div>

                                    <!-- Letter Body -->
                                    <div class="letter-content"
                                        style="margin: 10px 0; line-height: 1.8; text-align: justify; min-height: 160px;
                                     padding: 10px; border: 1px solid #eee; border-radius: 5px; background-color: #fff;">
                                        {!! $letter->body ?: nl2br(e($letter->body)) !!}
                                    </div>


                                    <div style="margin-top: 80px; text-align: center;">
                                        <div style="display: inline-block; text-align: center; width: 250px;">

                                            <div style="font-weight: bold; margin-top: 5px; font-size: 16px;">
                                                {{ $letter->letterSetting->authorized_sign_name ?? 'صالح بن احمد بن جاسر الضويعي' }}
                                            </div>
                                            <div style="font-size: 14px; color: #666;">
                                                {{ $letter->letterSetting->authorized_sign_title ?? 'مدير عام' }}
                                            </div>
                                            {{-- Signature --}}
                                            @if ($letter->letterSetting && $letter->letterSetting->signature_image)
                                                <img src="{{ asset('storage/' . $letter->letterSetting->signature_image) }}"
                                                    style="height: 60px; margin-bottom: 10px;" alt="التوقيع">
                                            @else
                                                <div style="border-top: 1px solid #000; width: 200px; margin: 10px auto;">
                                                </div>
                                            @endif




                                            <div style="margin-top: 10px; font-size: 14px; font-weight: bold;">
                                                {{ $letter->company->legal_name ?? $letter->company->name }}
                                            </div>

                                        </div>
                                    </div>

                                    {{-- Stamp --}}
                                    @if ($letter->letterSetting && $letter->letterSetting->stamp_image)
                                        <div style="position: absolute; bottom: 150px; left: 60px;">
                                            <img src="{{ asset('storage/' . $letter->letterSetting->stamp_image) }}"
                                                style="width: 120px; opacity: 0.8;" alt="ختم الشركة">
                                        </div>
                                    @endif
                                    <!-- Document Note -->
                                    <div
                                        style="text-align: center; font-size: 10px; color: #666; margin: 10px 0; padding: 10px;
                                     background-color: #f9f9f9; border-radius: 5px;">
                                        حررت هذه الوثيقة بك على طلب المشترك أعلاه<br>
                                        التاريخ: {{ $letter->hijri_date }} هـ الموافق
                                        {{ $letter->gregorian_date }} م
                                        {{-- ->format('Y/m/d') --}}
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">

                            <button type="button" class="btn btn-primary"
                                onclick="printLetter('letter_{{ $letter->id }}')">
                                <i class="fas fa-print"></i> {{ __('dashboard.print') }}
                            </button>

                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> {{ __('dashboard.close') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


        <!-- Add / Edit Letter Modal -->
        <div class="modal fade" id="letterModal" tabindex="-1" aria-labelledby="letterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="letterModalLabel">
                            {{ __('dashboard.create_letter') }}
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <form id="letterForm" method="POST" action="{{ route('dashboard.letters.store') }}">
                            @csrf

                            <div class="row">

                                <!-- Company -->
                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.company') }}</label>
                                    <select name="company_id" class="form-control" required>
                                        <option value="">{{ __('dashboard.select_company') }}</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->legal_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Branch -->
                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.branch') }}</label>
                                    <select name="branch_id" class="form-control" required>
                                        <option value="">{{ __('dashboard.select_branch') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Employee -->
                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.employee') }} ({{ __('dashboard.optional') }})</label>
                                    <select name="employee_id" class="form-control">
                                        <option value="">{{ __('dashboard.select_employee') }}</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->first_name }} {{ $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label
                                        for="letter_setting_id">{{ __('dashboard.select_company_signature_and_stamp') }}
                                        <span class="text-danger">*</span></label>
                                    <select name="letter_setting_id" id="letter_setting_id"
                                        class="form-control @error('letter_setting_id') is-invalid @enderror">
                                        <option value="">{{ __('dashboard.choose_setting') }}</option>
                                        @foreach ($letterSettings as $setting)
                                            <option value="{{ $setting->id }}"
                                                {{ old('letter_setting_id') == $setting->id ? 'selected' : '' }}>
                                                {{ $setting->company_name_ar }} - {{ $setting->authorized_sign_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('letter_setting_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- Letter Type -->
                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.letter_type') }}</label>
                                    <select name="letter_type" class="form-control" required>
                                        <option value="">{{ __('dashboard.select') }}</option>
                                        <option value="open">{{ __('dashboard.open_letter') }}</option>
                                        <option value="warning">{{ __('dashboard.warning_letter') }}</option>
                                    </select>
                                    @error('letter_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Letter Number (readonly, auto-generated) -->
                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.letter_number') }}</label>
                                    <input type="text" name="letter_number" class="form-control"
                                        value="{{ $generatedLetterNumber ?? 'LTR-2025-00001' }}" readonly>
                                </div>


                                <!-- Subject -->
                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.subject') }}</label>
                                    <input type="text" name="subject" class="form-control" required>
                                    @error('subject')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Hijri Date -->
                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.hijri_date') }}</label>
                                    <input type="date" name="hijri_date" class="form-control"
                                        value="{{ old('hijri_date', now()->format('Y-m-d')) }}" readonly>
                                </div>

                                <!-- Gregorian Date  -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.gregorian_date') }}</label>
                                    <input type="datetime-local" name="gregorian_date" class="form-control"
                                        value="{{ old('gregorian_date', now()->format('Y-m-d\TH:i')) }}" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.reciever_name') }}</label>
                                    <input type="text" name="receiver_name" class="form-control">
                                </div>

                                <!-- Letter Body -->
                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.letter_body') }}</label>
                                    <textarea name="body" class="form-control " id="summernote" cols="30" rows="5" class="summernote"></textarea>
                                    @error('body')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <!-- Footer -->
                            <div class="text-end mt-3">
                                <button type="reset" class="btn btn-secondary">
                                    {{ __('dashboard.reset') }}
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('dashboard.generate_letter') }}
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>


        @foreach ($letters as $letter)
            <!-- Edit Letter Modal -->
            <div class="modal fade" id="editLetterModal_{{ $letter->id }}" tabindex="-1"
                aria-labelledby="editLetterModalLabel_{{ $letter->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLetterModalLabel_{{ $letter->id }}">
                                {{ __('dashboard.edit_letter') }} - {{ $letter->letter_number }}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">
                            <form method="POST" action="{{ route('dashboard.letters.update', $letter->id) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="edit_letter_id" value="{{ $letter->id }}">
                                <div class="row">

                                    <!-- Company -->
                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.company') }}</label>
                                        <select name="company_id" class="form-control" required>
                                            <option value="">{{ __('dashboard.select_company') }}</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}"
                                                    @if ($company->id == $letter->company_id) selected @endif>
                                                    {{ $company->legal_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('company_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Branch -->
                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.branch') }}</label>
                                        <select name="branch_id" class="form-control" required>
                                            <option value="">{{ __('dashboard.select_branch') }}</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    @if ($branch->id == $letter->branch_id) selected @endif>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Employee (Optional) -->
                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.employee') }} ({{ __('dashboard.optional') }})</label>
                                        <select name="employee_id" class="form-control">
                                            <option value="">{{ __('dashboard.select_employee') }}</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}"
                                                    @if ($employee->id == $letter->employee_id) selected @endif>
                                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label
                                            for="letter_setting_id">{{ __('dashboard.select_company_signature_and_stamp') }}
                                            <span class="text-danger">*</span></label>
                                        <select name="letter_setting_id" id="letter_setting_id"
                                            class="form-control @error('letter_setting_id') is-invalid @enderror">

                                            <option value="">{{ __('dashboard.choose_setting') }}</option>

                                            @foreach ($letterSettings as $setting)
                                                <option value="{{ $setting->id }}"
                                                    {{ old('letter_setting_id', $letter->letter_setting_id) == $setting->id ? 'selected' : '' }}>
                                                    {{ $setting->company_name_ar }} -
                                                    {{ $setting->authorized_sign_name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('letter_setting_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    <!-- Letter Type -->
                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.letter_type') }}</label>
                                        <select name="letter_type" class="form-control" required>
                                            <option value="">{{ __('dashboard.select') }}</option>
                                            <option value="open" @if ($letter->letter_type == 'open') selected @endif>
                                                {{ __('dashboard.open_letter') }}</option>
                                            <option value="warning" @if ($letter->letter_type == 'warning') selected @endif>
                                                {{ __('dashboard.warning_letter') }}</option>
                                        </select>
                                        @error('letter_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Letter Number (readonly) -->
                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.letter_number') }}</label>
                                        <input type="text" name="letter_number" class="form-control"
                                            value="{{ $letter->letter_number }}" readonly>
                                    </div>

                                    <!-- Subject -->
                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.subject') }}</label>
                                        <input type="text" name="subject" class="form-control"
                                            value="{{ $letter->subject }}" required>
                                        @error('subject')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Hijri Date (readonly or editable) -->
                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.hijri_date') }}</label>
                                        <input type="date" name="hijri_date" class="form-control"
                                            value="{{ $letter->hijri_date }}" readonly>
                                    </div>

                                    <!-- Gregorian Date (readonly) -->
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.gregorian_date') }}</label>
                                        <input type="datetime-local" name="gregorian_date" class="form-control"
                                            value="{{ \Carbon\Carbon::parse($letter->gregorian_date)->format('Y-m-d\TH:i') }}"
                                            readonly>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.reciever_name') }}</label>
                                        <input type="text" name="receiver_name" class="form-control"
                                            value="{{ $letter->receiver_name }}">
                                    </div>


                                    <!-- Letter Body - CHANGED ID TO BE UNIQUE -->
                                    <div class="form-group col-md-12">
                                        <label>{{ __('dashboard.letter_body') }}</label>
                                        <textarea name="body" class="form-control edit-letter-body" id="edit_letter_body_{{ $letter->id }}"
                                            rows="6" required>{{ $letter->body }}</textarea>
                                        @error('body')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>

                                <!-- Footer -->
                                <div class="text-end mt-3">
                                    <button type="reset" class="btn btn-secondary">
                                        {{ __('dashboard.reset') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('dashboard.update_letter') }}
                                    </button>

                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach


        @foreach ($letters as $letter)
            <div class="modal fade" id="deleteLetterModal_{{ $letter->id }}" tabindex="-1"
                aria-labelledby="deleteLetterModalLabel_{{ $letter->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteLetterModalLabel_{{ $letter->id }}">
                                {{ __('dashboard.delete_letter') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('dashboard.letters.delete', $letter->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $letter->letter_number }}</strong>?
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
    @if ($errors->any() && !old('edit_letter_id'))
        <script>
            $(document).ready(function() {
                $('#letterModal').modal('show');
            });
        </script>
    @endif

    @if ($errors->any() && old('edit_letter_id'))
        <script>
            $(document).ready(function() {
                $('#editLetterModal_' + '{{ old('edit_letter_id') }}').modal('show');
            });
        </script>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.7/jodit.min.js"></script>

    <script>
        let editors = {};
        let mainEditor = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize main Jodit editor for ADD form
            mainEditor = Jodit.make('#summernote', {
                height: 500,
                toolbarAdaptive: true,
                buttons: [
                    'source', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'superscript', 'subscript', '|',
                    'ul', 'ol', '|',
                    'outdent', 'indent', '|',
                    'font', 'fontsize', 'brush', 'paragraph', '|',
                    'image', 'video', 'file', '|',
                    'table', 'link', '|',
                    'align', '|',
                    'undo', 'redo', '|',
                    'eraser', 'copyformat', '|',
                    'fullsize', 'preview', 'print', '|',
                    'symbol', 'about'
                ],
                uploader: {
                    insertImageAsBase64URI: true
                },
                fontSizeValues: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '30', '36', '48',
                    '72'
                ],
                defaultFontSizePoints: 'pt',
                showXPathInStatusbar: true,
                showCharsCounter: true,
                showWordsCounter: true,
                showPlaceholder: true,
                style: {
                    font: 'Arial, sans-serif',
                    fontSize: '14px'
                }
            });

            // Function to initialize modal editor
            function initModalEditor(modal) {
                let textarea = modal.querySelector('textarea.edit-letter-body');
                if (!textarea) return;

                let textareaId = textarea.id;

                // If already initialized, do nothing
                if (editors[textareaId]) return;

                try {
                    // Get the HTML content from the 
                    let initialContent = textarea.value || '';

                    editors[textareaId] = Jodit.make('#' + textareaId, {
                        height: 350,
                        toolbarAdaptive: true,
                        buttons: [
                            'source', '|',
                            'bold', 'italic', 'underline', 'strikethrough', '|',
                            'ul', 'ol', '|',
                            'outdent', 'indent', '|',
                            'font', 'fontsize', 'brush', 'paragraph', '|',
                            'image', 'link', 'table', '|',
                            'align', '|',
                            'undo', 'redo'
                        ],
                        fontSizeValues: ['8', '10', '12', '14', '16', '18', '20', '24'],
                        uploader: {
                            insertImageAsBase64URI: true
                        },
                        image: {
                            editSrc: true,
                            resize: true,
                            upload: true
                        },
                        events: {
                            afterInit: function(editor) {
                                // Set initial content
                                editor.value = initialContent;
                                // Adjust height
                                editor.editor.style.minHeight = '300px';
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error creating editor:', error);
                }
            }

            // Function to destroy modal editor
            function destroyModalEditor(modal) {
                let textarea = modal.querySelector('textarea.edit-letter-body');
                if (!textarea) return;

                let textareaId = textarea.id;

                if (editors[textareaId]) {
                    try {
                        // Save editor content back to textarea
                        let editorContent = editors[textareaId].value;
                        if (editorContent) {
                            textarea.value = editorContent;
                        }

                        // Destroy the editor
                        editors[textareaId].destruct();
                        editors[textareaId] = null;
                        delete editors[textareaId];
                    } catch (error) {
                        console.error('Error destroying editor:', error);
                    }
                }
            }

            // Bootstrap 4 events (using jQuery)
            if (typeof $ !== 'undefined') {
                $(document).on('shown.bs.modal', '[id^="editLetterModal_"]', function() {
                    initModalEditor(this);
                });

                $(document).on('hidden.bs.modal', '[id^="editLetterModal_"]', function() {
                    destroyModalEditor(this);
                });
            }

            // Handle form submission for ADD form
            const addForm = document.querySelector('form:not([id^="editLetterModal_"])');
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    let editorData = mainEditor?.value || '';

                    // Update hidden textarea with editor content
                    const textarea = document.getElementById('summernote');
                    if (textarea) {
                        textarea.value = editorData;
                    }

                    // Validate
                    if (!editorData.trim() || editorData === '<p><br></p>') {
                        e.preventDefault();
                        alert('Letter body is required');
                        mainEditor?.focus();
                        return false;
                    }
                });
            }

            // Handle form submission for EDIT forms in modals
            document.addEventListener('submit', function(e) {
                // Check if this is a modal form
                let form = e.target;
                let modal = form.closest('[id^="editLetterModal_"]');

                if (modal) {
                    let textarea = form.querySelector('textarea.edit-letter-body');
                    if (!textarea) return;

                    let textareaId = textarea.id;
                    let editor = editors[textareaId];

                    if (editor) {
                        // Update textarea with editor content
                        let editorData = editor.value || '';
                        textarea.value = editorData;

                        // Validate
                        if (!editorData.trim() || editorData === '<p><br></p>') {
                            e.preventDefault();
                            alert('Letter body is required');
                            editor.focus();
                            return false;
                        }
                    }
                }
            });
        });

        // Your existing printLetter function
        function printLetter(letterId) {
            let contentElement = document.getElementById(letterId);
            let content = contentElement.cloneNode(true);

            // Find the stamp element and reposition it for print
            let stampDiv = content.querySelector('div[style*="position: absolute"][style*="bottom: 150px"]');
            if (stampDiv) {
                // Remove from original position
                let stampImg = stampDiv.querySelector('img');
                stampDiv.remove();

                // Find signature section and add stamp there
                let signatureSection = content.querySelector('div[style*="margin-top: 80px"]');
                if (signatureSection && stampImg) {
                    // Create new stamp container for print
                    let newStampDiv = document.createElement('div');
                    newStampDiv.style.cssText = 'position: absolute; bottom: 30px; left: 50px; z-index: 10;';
                    newStampDiv.appendChild(stampImg.cloneNode(true));
                    signatureSection.appendChild(newStampDiv);

                    // Ensure signature section is positioned
                    signatureSection.style.position = 'relative';
                    signatureSection.style.minHeight = '140px';
                }
            }

            let printWindow = window.open('', '', 'width=900,height=650');

            printWindow.document.write(`
        <html>
            <head>
                <title>Print Letter</title>
                <meta charset="UTF-8">
                <style>
                    @page {
                        size: A4 portrait;
                        margin: 15mm;
                    }

                    * {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        color-adjust: exact !important;
                    }

                    body {
                        font-family: 'Arial', sans-serif;
                        direction: rtl;
                        margin: 0;
                        padding: 0;
                        background: white;
                    }

                    /* Main letter container */
                    body > div {
                        background: white !important;
                        margin: 0 !important;
                        padding: 20px !important;
                        max-width: 100% !important;
                        min-height: auto !important;
                        box-shadow: none !important;
                        position: relative;
                        font-size: 11px !important;
                        line-height: 1.5 !important;
                    }

                    /* Reduce all margins */
                    div[style*="margin-bottom: 20px"],
                    div[style*="margin-bottom: 25px"],
                    div[style*="margin-bottom: 30px"] {
                        margin-bottom: 10px !important;
                    }

                    /* Header */
                    div[style*="border-bottom: 2px solid"] {
                        margin-bottom: 8px !important;
                        padding-bottom: 5px !important;
                    }

                    /* Logo */
                    img[alt="شعار الشركة"] {
                        height: 60px !important;
                        margin-bottom: 5px !important;
                    }

                    /* Member info */
                    div[style*="display: flex"] {
                        font-size: 10px !important;
                        margin-bottom: 10px !important;
                    }

                    /* Letter info box */
                    div[style*="border-right: 4px solid"] {
                        margin-bottom: 12px !important;
                        padding: 10px !important;
                        font-size: 11px !important;
                    }

                    div[style*="border-right: 4px solid"] > div {
                        margin-bottom: 4px !important;
                    }

                    /* Salutation */
                    div[style*="السَّلامُ عَلَيْكُمْ"] {
                        font-size: 14px !important;
                        margin: 15px 0 !important;
                    }

                    /* Letter body */
                    .letter-content {
                        margin: 8px 0 !important;
                        line-height: 1.6 !important;
                        min-height: auto !important;
                        padding: 8px !important;
                        font-size: 11px !important;
                    }

                    /* Signature section */
                    div[style*="margin-top: 80px"] {
                        margin-top: 30px !important;
                        page-break-inside: avoid !important;
                        position: relative !important;
                        min-height: 140px !important;
                    }

                    /* Signature image */
                    img[alt="التوقيع"] {
                        height: 50px !important;
                        margin-bottom: 5px !important;
                    }

                    /* Signature text */
                    div[style*="margin-top: 80px"] div[style*="font-weight: bold"] {
                        font-size: 13px !important;
                        margin-top: 3px !important;
                    }

                    div[style*="margin-top: 80px"] div[style*="color: #666"] {
                        font-size: 12px !important;
                    }

                    /* Stamp - ensure it prints */
                    img[alt="ختم الشركة"] {
                        width: 100px !important;
                        height: auto !important;
                        opacity: 0.7 !important;
                        display: block !important;
                    }

                    /* Document note */
                    div[style*="حررت هذه الوثيقة"] {
                        margin: 8px 0 !important;
                        padding: 8px !important;
                        font-size: 9px !important;
                    }

                    /* Tables */
                    table {
                        width: 100%;
                        max-width: 500px;
                        border-collapse: collapse;
                        font-size: 10px !important;
                        page-break-inside: avoid;
                    }

                    table, th, td {
                        border: 1px solid #333;
                    }

                    th, td {
                        padding: 4px !important;
                        text-align: right;
                    }

                    /* Remove backgrounds */
                    div[style*="background-color: #f8f9fa"],
                    div[style*="background-color: #f9f9f9"],
                    div[style*="background-color: #fff"] {
                        background-color: white !important;
                    }

                    @media print {
                        body, html {
                            height: 100%;
                            margin: 0;
                            padding: 0;
                        }

                        img {
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                            color-adjust: exact !important;
                        }

                        body > div {
                            page-break-inside: avoid;
                            page-break-after: avoid;
                        }
                    }
                </style>
            </head>
            <body>
                ${content.outerHTML}
            </body>
        </html>
    `);

            printWindow.document.close();
            printWindow.focus();

            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 1000);
        }

        // Helper functions
        function getEditorContent() {
            return mainEditor?.value || '';
        }

        function setEditorContent(content) {
            if (mainEditor) {
                mainEditor.value = content;
            }
        }
    </script>
@endsection

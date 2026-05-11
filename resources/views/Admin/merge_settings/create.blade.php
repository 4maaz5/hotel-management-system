   @extends('layouts.app')

   @section('title', 'Merge Setting')
   @section('content')
       <main class="u-white-bg bg-white p-3" style="border-radius:15px;"">

           <!-- Page Header -->
           <div class="page-category">{{ __('dashboard.company') }}</div>
           <div class="page-header">
               <div>
                   <h2 class="page-header__title">{{ __('dashboard.new_unit_merge_setting') }}</h2>
                   <div class="page-header__subtitle">{{ __('dashboard.fill_the_information_to_add_unit_merge_setting') }}
                   </div>
               </div>
           </div>

           <form method="POST" action="{{ route('setup-sidebar.merge_setting.store') }}">
               @csrf

               {{-- Block --}}
               <div class="row mb-3">
                   <div class="col-md-3">
                       <label class="form-label">{{ __('dashboard.block') }}</label>
                       <select class="form-select" name="block_id" id="block_id">
                           <option value="">{{ __('dashboard.select_block') }}</option>
                           @foreach ($blocks as $block)
                               <option value="{{ $block->id }}">{{ $block->name }}</option>
                           @endforeach
                       </select>

                   </div>
               </div>

               {{-- Floor --}}
               <div class="row mb-3">
                   <div class="col-md-3">
                       <label class="form-label">{{ __('dashboard.floor') }}</label>
                       <select class="form-select" name="floor_id" id="floor_id">
                           <option value="">{{ __('dashboard.select_floor') }}</option>
                       </select>

                   </div>
               </div>

               {{-- Unit Class --}}
               <div class="row mb-3">
                   <div class="col-md-3">
                       <label class="form-label">{{ __('dashboard.unit_class') }}</label>
                       <select class="form-select" name="unit_class_id" id="unit_class_id">
                           <option value="">{{ __('dashboard.select_unit_class') }}</option>
                           @foreach ($units as $unit)
                               <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                           @endforeach
                       </select>

                   </div>
               </div>

               {{-- Unit A & Unit B --}}
               <div class="row mb-3">
                   <div class="col-md-3">
                       <label class="form-label">
                           {{ __('dashboard.unit_a') }} <span class="text-danger">*</span>
                       </label>
                       <select class="form-select" name="unit_a_id" id="unit_a_id" required>
                           <option value="">{{ __('dashboard.select_unit_a') }}</option>
                       </select>
                   </div>

                   <div class="col-md-3">
                       <label class="form-label">
                           {{ __('dashboard.unit_b') }} <span class="text-danger">*</span>
                       </label>
                       <select class="form-select" name="unit_b_id" id="unit_b_id" required>
                           <option value="">{{ __('dashboard.select_unit_b') }}</option>
                       </select>
                   </div>
               </div>

               {{-- Actions --}}
               <div class="d-flex justify-content-end mt-4">
                   <a href="{{ route('setup-sidebar.merge_setting.index') }}" class="btn btn-outline-danger me-3">
                       {{ __('dashboard.discard') }}
                   </a>
                   <button type="submit" class="btn btn-primary">
                       {{ __('dashboard.add_unit_merge') }}
                   </button>
               </div>
           </form>

       </main>
   @endsection
   @push('scripts')
       <script>
           $(document).ready(function() {

               // Block → Floors
               $('#block_id').on('change', function() {

                   let blockId = $(this).val();
                   $('#floor_id').html('<option value="">Loading...</option>');

                   if (blockId) {
                       $.get('/floors/' + blockId, function(data) {
                           let options = '<option value="">Select Floor</option>';
                           $.each(data, function(i, floor) {
                               options += '<option value="' + floor.id + '">' + floor.name +
                                   '</option>';
                           });
                           $('#floor_id').html(options);
                       });
                   } else {
                       $('#floor_id').html('<option value="">Select Floor</option>');
                   }
               });

               // Floor + Class → Units
               $('#floor_id, #unit_class_id').on('change', function() {

                   let blockId = $('#block_id').val();
                   let floorId = $('#floor_id').val();
                   let classId = $('#unit_class_id').val();

                   if (blockId && floorId && classId) {

                       $.get('/units', {
                           block_id: blockId,
                           floor_id: floorId,
                           unit_class_id: classId
                       }, function(data) {

                           let options = '<option value="">Select Unit</option>';

                           $.each(data, function(i, unit) {
                               options += '<option value="' + unit.id + '">' + unit
                                   .unit_number + '</option>';
                           });

                           $('#unit_a_id').html(options);
                           $('#unit_b_id').html(options);
                       });
                   }
               });

               $('#unit_a_id').on('change', function() {
                   let selected = $(this).val();
                   $('#unit_b_id option').show();
                   $('#unit_b_id option[value="' + selected + '"]').hide();
               });

           });
       </script>
   @endpush

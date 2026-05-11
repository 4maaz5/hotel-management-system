   @extends('layouts.app')

   @section('title', 'Merge Setting')
   @section('content')
       <main class="u-white-bg bg-white p-3" style="border-radius:15px;"">

           <!-- Page Header -->
           <div class="page-category">{{ __('dashboard.company') }}</div>
           <div class="page-header">
               <div>
                   <h2 class="page-header__title">{{ __('dashboard.new_unit_merge_setting') }}</h2>
                   <div class="page-header__subtitle">{{ __('dashboard.edit_and_manage_unit_merge_setting') }}
                   </div>
               </div>
           </div>

           <form method="POST" action="#">
               @csrf

               {{-- Block --}}
               <div class="row mb-3">
                   <div class="col-md-3">
                       <label class="form-label">{{ __('dashboard.block') }}</label>
                       <select class="form-select" name="block_id">
                           <option value="">{{ __('dashboard.select_block') }}</option>
                           <option value="1">Block A</option>
                           <option value="2">Block B</option>
                           <option value="3">Block C</option>
                       </select>
                   </div>
               </div>

               {{-- Floor --}}
               <div class="row mb-3">
                   <div class="col-md-3">
                       <label class="form-label">{{ __('dashboard.floor') }}</label>
                       <select class="form-select" name="floor_id">
                           <option value="">{{ __('dashboard.select_floor') }}</option>
                           <option value="1">Floor 1</option>
                           <option value="2">Floor 2</option>
                           <option value="3">Floor 3</option>
                       </select>
                   </div>
               </div>

               {{-- Unit Class --}}
               <div class="row mb-3">
                   <div class="col-md-3">
                       <label class="form-label">{{ __('dashbaord.unit_class') }}</label>
                       <select class="form-select" name="unit_class_id">
                           <option value="">{{ __('dashboard.select_class') }}</option>
                           <option value="1">Standard</option>
                           <option value="2">Deluxe</option>
                           <option value="3">Suite</option>
                       </select>
                   </div>
               </div>

               {{-- Unit A & Unit B --}}
               <div class="row mb-3">
                   <div class="col-md-3">
                       <label class="form-label">
                           {{ __('dashboard.unit_a') }} <span class="text-danger">*</span>
                       </label>
                       <select class="form-select" name="unit_a_id" required>
                           <option value="">{{ __('dashboard.select_unit_a') }}</option>
                           <option value="101">Room 101</option>
                           <option value="102">Room 102</option>
                           <option value="103">Room 103</option>
                       </select>
                   </div>

                   <div class="col-md-3">
                       <label class="form-label">
                           {{ __('dashboard.unit_b') }} <span class="text-danger">*</span>
                       </label>
                       <select class="form-select" name="unit_b_id" required>
                           <option value="">{{ __('dashboard.select_unit_b') }}</option>
                           <option value="104">Room 104</option>
                           <option value="105">Room 105</option>
                           <option value="106">Room 106</option>
                       </select>
                   </div>
               </div>

               {{-- Actions --}}
               <div class="d-flex justify-content-end mt-4">
                   <a href="{{ route('setup-sidebar.merge_setting.index') }}" class="btn btn-outline-danger me-3">
                       {{ __('dashboard.discard') }}
                   </a>
                   <button type="submit" class="btn btn-primary">
                       {{ __('dashboard.update') }}
                   </button>
               </div>
           </form>

       </main>
   @endsection

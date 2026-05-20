<?php

use App\Http\Controllers\BlocksAndFloors\BlocksController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\BlocksAndFloors\FloorsController;
use App\Http\Controllers\CashDrawerController;
use App\Http\Controllers\ChannelManager\InventoryController;
use App\Http\Controllers\ChannelManager\ProductController;
use App\Http\Controllers\ChannelManager\WebsiteConfigureController;
use App\Http\Controllers\ChannelManager\WebsiteFaqController;
use App\Http\Controllers\ChannelManager\WebsitePageController;
use App\Http\Controllers\Customers\CorporateController;
use App\Http\Controllers\Customers\GuestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Financials\BankAccountController;
use App\Http\Controllers\Financials\CostCenterController;
use App\Http\Controllers\Financials\CurrencyController;
use App\Http\Controllers\Financials\DiscountTypeController;
use App\Http\Controllers\Financials\PaymentController;
use App\Http\Controllers\Financials\SecurityDepositController;
use App\Http\Controllers\Financials\TaxesController;
use App\Http\Controllers\Housekeeping\StatusController;
use App\Http\Controllers\Housekeeping\TaskController;
use App\Http\Controllers\HouseKeepingSetting\HouseKeeperController;
use App\Http\Controllers\HouseKeepingSetting\StaffAttendanceController;
use App\Http\Controllers\HouseKeepingSetting\TaskTypeController;
use App\Http\Controllers\Lang\LocaleController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\OnlineReservationController;
use App\Http\Controllers\Outlets\ItemCategoryController;
use App\Http\Controllers\Outlets\ItemsController;
use App\Http\Controllers\Outlets\OutletSetupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Property\PropertyController;
use App\Http\Controllers\Property\PropertyInfoController;
use App\Http\Controllers\Property\RoleController;
use App\Http\Controllers\Property\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Reporting\NumberingController;
use App\Http\Controllers\Reporting\PrintingController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Rules\CancelReasonController;
use App\Http\Controllers\Rules\ConditionController;
use App\Http\Controllers\Rules\GuestFeedbackController;
use App\Http\Controllers\Rules\NightAuditController;
use App\Http\Controllers\Rules\PenaltyController;
use App\Http\Controllers\Rules\SetupReservationController;
use App\Http\Controllers\Rules\UnitReasonController;
use App\Http\Controllers\Settings\AutoSMSController;
use App\Http\Controllers\Settings\DateController;
use App\Http\Controllers\Settings\NtmpController;
use App\Http\Controllers\Settings\GuestClassController;
use App\Http\Controllers\Settings\LoyaltyProgramController;
use App\Http\Controllers\Settings\PropertyFacilityController;
use App\Http\Controllers\Settings\ReservationSourceController;
use App\Http\Controllers\Settings\ShomoosController;
use App\Http\Controllers\Settings\ThemeCustomizationController;
use App\Http\Controllers\Setup\SetupController;
use App\Http\Controllers\SMS\ManualSMSController;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\App\CurrentPropertyController;
use App\Http\Controllers\Units\AmenityController;
use App\Http\Controllers\Units\BaseRateController;
use App\Http\Controllers\Units\MergeSettingController;
use App\Http\Controllers\Units\RatePlanController;
use App\Http\Controllers\Units\SeasonalRateController;
use App\Http\Controllers\Units\SpecialRateController;
use App\Http\Controllers\Units\TypeCustomizationController;
use App\Http\Controllers\Units\UnitController;
use App\Http\Controllers\UnitStatusController;
use App\Http\Controllers\Vouchers\CreditController;
use App\Http\Controllers\Vouchers\DropController;
use App\Http\Controllers\Vouchers\InvoiceController;
use App\Http\Controllers\Vouchers\PromissoryController;
use App\Http\Controllers\Vouchers\ReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:dashboard.view|reservation.view|unit_status.view|housekeeping_task.view|receipt.view|outlet_setup.view|guest.view|sms.send|cash_drawer_balance.view|reports.view|logs.view|night_audit.edit'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/api/chat/session', [ChatController::class, 'current'])
        ->name('api.chat.session.current');
    Route::get('/api/chat/sessions/{session}/messages', [ChatController::class, 'messages'])
        ->name('api.chat.messages');
    Route::post('/api/chat', [ChatController::class, 'store'])
        ->middleware('throttle:chatbot')
        ->name('api.chat.store');
});

Route::get('/program', function () {
    return view('program');
})->middleware(['auth', 'verified', 'permission:dashboard.view|reservation.view|unit_status.view|housekeeping_task.view|receipt.view|outlet_setup.view|guest.view|sms.send|cash_drawer_balance.view|reports.view|logs.view|night_audit.edit'])->name('program');

Route::middleware('auth')->group(function () {
    Route::post('/current-property', [CurrentPropertyController::class, 'update'])
        ->name('current-property.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::middleware(['auth'])->group(function () {

    Route::middleware(['permission:dashboard.view'])->group(function () {
        Route::get('/dashboard/setup-sidebar', [SetupController::class, 'index'])
            ->name('setup-sidebar');
    });

    Route::middleware('permission:property.view')->group(function () {
        Route::get('/dashboard/setup-sidebar/property', [PropertyController::class, 'index'])
            ->name('setup-sidebar.property.index');

        Route::get('/dashboard/setup-sidebar/property-show/{property}', [PropertyController::class, 'show'])
            ->name('setup-sidebar.property.show');
    });

    Route::middleware('permission:property.add')->group(function () {
        Route::get('/dashboard/setup-sidebar/property-create', [PropertyController::class, 'create'])
            ->name('setup-sidebar.property.create');

        Route::post('/dashboard/setup-sidebar/property-store', [PropertyController::class, 'store'])
            ->middleware('plan.limit:max_properties')
            ->name('setup-sidebar.property.store');
    });

    Route::middleware('permission:property.edit')->group(function () {
        Route::get('/dashboard/setup-sidebar/property-edit/{property}', [PropertyController::class, 'edit'])
            ->name('setup-sidebar.property.edit');

        Route::put('/dashboard/setup-sidebar/property-update/{property}', [PropertyController::class, 'update'])
            ->name('setup-sidebar.property.update');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::middleware(['permission:property_info.view'])->group(function () {
        Route::get('/dashboard/setup-sidebar/property-info', [PropertyInfoController::class, 'index'])
            ->name('setup-sidebar.property-info.index');
    });

    Route::middleware(['permission:property_info.edit'])->group(function () {
        Route::get('/dashboard/setup-sidebar/property-info-edit', [PropertyInfoController::class, 'edit'])
            ->name('setup-sidebar.property-info.edit');

        Route::post('/dashboard/setup-sidebar/property-info-save', [PropertyInfoController::class, 'savePropertyDetails'])
            ->name('setup-sidebar.property-info.save');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/setup-sidebar/property-user', [UserController::class, 'index'])
        ->middleware(['permission:user.view'])
        ->name('setup-sidebar.property-user.index');

    Route::middleware(['permission:user.view'])->group(function () {

        Route::get('/dashboard/setup-sidebar/property-user-view/{id}', [UserController::class, 'view'])
            ->name('setup-sidebar.property-user.view');
    });

    Route::middleware(['permission:user.add'])->group(function () {
        Route::get('/dashboard/setup-sidebar/property-user-create', [UserController::class, 'create'])
            ->name('setup-sidebar.property-user.create');

        Route::post('/dashboard/setup-sidebar/property-user-store', [UserController::class, 'store'])
            ->middleware('plan.limit:max_users')
            ->name('setup-sidebar.property-user.store');
    });

    Route::middleware(['permission:user.edit'])->group(function () {
        Route::get('/dashboard/setup-sidebar/property-user-edit/{id}', [UserController::class, 'edit'])
            ->name('setup-sidebar.property-user.edit');

        Route::put('/dashboard/setup-sidebar/property-user-update/{id}', [UserController::class, 'update'])
            ->name('setup-sidebar.property-user.update');
    });

    Route::middleware(['permission:user.delete'])->group(function () {
        Route::patch('/dashboard/setup-sidebar/property-user-delete/{id}', [UserController::class, 'deactivate'])
            ->name('setup-sidebar.property-user.delete');
    });

    Route::middleware(['permission:user.assing_outlet'])->group(function () {
        Route::patch('/dashboard/setup-sidebar/property-assign-outlet/{user}', [UserController::class, 'assignOutlet'])
            ->name('setup-sidebar.property-user.assign');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/setup-sidebar/property-role', [RoleController::class, 'index'])
        ->middleware(['permission:role.view'])
        ->name('setup-sidebar.property-role.index');

    Route::middleware(['permission:role.add'])->group(function () {
        Route::get('/dashboard/setup-sidebar/property-role-create', [RoleController::class, 'create'])
            ->name('setup-sidebar.property-role.create');

        Route::post('/dashboard/setup-sidebar/property-role-store', [RoleController::class, 'store'])
            ->name('setup-sidebar.property-role.store');
    });

    Route::middleware(['permission:role.edit'])->group(function () {
        Route::get('/dashboard/setup-sidebar/property-role-edit/{role}', [RoleController::class, 'edit'])
            ->name('setup-sidebar.property-role.edit');

        Route::put('/dashboard/setup-sidebar/property-role-update/{role}', [RoleController::class, 'update'])
            ->name('setup-sidebar.property-role.update');
    });

    Route::middleware(['permission:role.view'])->group(function () {
        Route::get('/dashboard/setup-sidebar/property-role-view/{role}', [RoleController::class, 'view'])
            ->name('setup-sidebar.property-role.view');
    });

    Route::middleware(['permission:role.delete'])->group(function () {
        Route::patch('/dashboard/setup-sidebar/property-role-delete/{role}', [RoleController::class, 'delete'])
            ->name('setup-sidebar.property-role.delete');
    });

    Route::middleware(['permission:role.copy'])->group(function () {
        Route::get('/dashboard/setup-sidebar/property-role-copy/{role}', [RoleController::class, 'copy'])
            ->name('setup-sidebar.property-role.copy');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/setup-sidebar/blocks', [BlocksController::class, 'index'])
        ->middleware(['permission:block.view'])
        ->name('setup-sidebar.blocks.index');

    Route::middleware(['permission:block.add'])->group(function () {

        Route::post('/dashboard/setup-sidebar/blocks-store', [BlocksController::class, 'store'])
            ->name('setup-sidebar.blocks.store');
    });

    Route::middleware(['permission:block.edit'])->group(function () {
        Route::put('/dashboard/setup-sidebar/blocks-update/{block}', [BlocksController::class, 'update'])
            ->name('setup-sidebar.blocks.update');
    });

    Route::middleware(['permission:block.delete'])->group(function () {
        Route::patch('/dashboard/setup-sidebar/blocks-delete/{block}', [BlocksController::class, 'delete'])
            ->name('setup-sidebar.blocks.delete');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/setup-sidebar/floors', [FloorsController::class, 'index'])
        ->middleware(['permission:floor.view'])
        ->name('setup-sidebar.floors.index');

    Route::middleware(['permission:floor.add'])->group(function () {

        Route::post('/dashboard/setup-sidebar/floors-store', [FloorsController::class, 'store'])
            ->name('setup-sidebar.floors.store');
    });

    Route::middleware(['permission:floor.edit'])->group(function () {
        Route::put('/dashboard/setup-sidebar/floors-update/{floor}', [FloorsController::class, 'update'])
            ->name('setup-sidebar.floors.update');
    });

    Route::middleware(['permission:floor.delete'])->group(function () {
        Route::patch('/dashboard/setup-sidebar/floors-delete/{floor}', [FloorsController::class, 'delete'])
            ->name('setup-sidebar.floors.delete');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/setup-sidebar/unit-type-customization', [TypeCustomizationController::class, 'index'])
        ->middleware(['permission:type.view'])
        ->name('setup-sidebar.typeCustomization.index');

    Route::middleware(['permission:type.add'])->group(function () {
        Route::get('/dashboard/setup-sidebar/unit-type-customization-create', [typeCustomizationController::class, 'create'])
            ->name('setup-sidebar.typeCustomization.create');
        Route::post('/dashboard/setup-sidebar/unit-type-customization-store', [TypeCustomizationController::class, 'store'])
            ->name('setup-sidebar.typeCustomization.store');
    });

    Route::middleware(['permission:type.edit'])->group(function () {
        Route::get('/dashboard/setup-sidebar/unit-type-customization-edit/{id}', [TypeCustomizationController::class, 'edit'])
            ->name('setup-sidebar.typeCustomization.edit');

        Route::put('/dashboard/setup-sidebar/unit-type-customization-update/{id}', [TypeCustomizationController::class, 'update'])
            ->name('setup-sidebar.typeCustomization.update');
    });

    Route::middleware(['permission:type.delete'])->group(function () {
        Route::patch('/dashboard/setup-sidebar/unit-type-customization-delete/{id}', [TypeCustomizationController::class, 'delete'])
            ->name('setup-sidebar.typeCustomization.delete');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/setup-sidebar/amenity', [AmenityController::class, 'index'])
        ->middleware(['permission:amenity.view'])
        ->name('setup-sidebar.amenity.index');

    Route::middleware(['permission:amenity.add'])->group(function () {
        Route::post('/dashboard/setup-sidebar/amenity-store', [AmenityController::class, 'store'])
            ->name('setup-sidebar.amenity.store');
    });

    Route::middleware(['permission:amenity.edit'])->group(function () {
        Route::put('/dashboard/setup-sidebar/amenity-update/{amenity}', [AmenityController::class, 'update'])
            ->name('setup-sidebar.amenity.update');
    });

    Route::middleware(['permission:amenity.delete'])->group(function () {
        Route::patch('/dashboard/setup-sidebar/amenity-delete/{amenity}', [AmenityController::class, 'delete'])
            ->name('setup-sidebar.amenity.delete');
    });

    Route::middleware(['permission:amenity.copy'])->group(function () {
        Route::patch('/dashboard/setup-sidebar/amenity-copy/{amenity}', [AmenityController::class, 'applyToAllUnits'])
            ->name('setup-sidebar.amenity.copy');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/setup-sidebar/unit', [UnitController::class, 'index'])
        ->middleware(['permission:unit.view'])
        ->name('setup-sidebar.unit.index');

    Route::middleware(['permission:unit.add'])->group(function () {
        Route::get('/dashboard/setup-sidebar/unit-create', [UnitController::class, 'create'])
            ->name('setup-sidebar.unit.create');
        Route::post('/dashboard/setup-sidebar/unit-store', [UnitController::class, 'store'])
            ->name('setup-sidebar.unit.store');
    });

    Route::middleware(['permission:unit.edit'])->group(function () {
        Route::get('/dashboard/setup-sidebar/unit-edit/{id}', [UnitController::class, 'edit'])
            ->name('setup-sidebar.unit.edit');

        Route::put('/dashboard/setup-sidebar/unit-update/{id}', [UnitController::class, 'update'])
            ->name('setup-sidebar.unit.update');
    });

    Route::middleware(['permission:unit.view'])->group(function () {
        Route::get('/dashboard/setup-sidebar/unit-view/{id}', [UnitController::class, 'view'])
            ->name('setup-sidebar.unit.view');
    });

    Route::middleware(['permission:unit.delete'])->group(function () {
        Route::patch('/dashboard/setup-sidebar/unit--delete/{id}', [UnitController::class, 'delete'])
            ->name('setup-sidebar.unit.delete');
    });

    Route::get('dashboard/reservation/get-rates', [ReservationController::class, 'getRates'])
        ->middleware(['permission:reservation.view|reservation.add|reservation.edit'])
        ->name('dashboard.reservation.get_rates');
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/unit-merge-setting', [MergeSettingController::class, 'index'])
        ->middleware(['permission:merge_setting.view'])
        ->name('setup-sidebar.merge_setting.index');

    Route::middleware(['permission:merge_setting.add'])->group(function () {
        Route::get('setup-sidebar/unit-merge-setting-create', [MergeSettingController::class, 'create'])
            ->name('setup-sidebar.merge_setting.create');
        Route::post('setup-sidebar/unit-merge-setting-store', [MergeSettingController::class, 'store'])
            ->name('setup-sidebar.merge_setting.store');
    });
    Route::get('/floors/{block}', [MergeSettingController::class, 'getFloors'])
        ->middleware(['permission:merge_setting.view|merge_setting.add']);
    Route::get('/units', [MergeSettingController::class, 'getUnits'])
        ->middleware(['permission:merge_setting.view|merge_setting.add']);

    Route::middleware(['permission:merge_setting.delete'])->group(function () {
        Route::patch('/dashboard/setup-sidebar/merge-setting/{id}', [MergeSettingController::class, 'destroy'])
            ->name('setup-sidebar.merge_setting.destroy');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/unit-base-rates', [BaseRateController::class, 'index'])
        ->middleware(['permission:base_rate.edit'])
        ->name('setup-sidebar.base_rate.index');

    Route::middleware(['permission:base_rate.edit'])->group(function () {

        Route::post('setup-sidebar/unit-base-rate-store', [BaseRateController::class, 'store'])
            ->name('setup-sidebar.base_rate.store');
    });
    Route::post('setup-sidebar/high-week-days', [BaseRateController::class, 'saveHighWeekdays'])
        ->middleware(['permission:base_rate.edit'])
        ->name('setup-sidebar.high_weekdays.store');

    Route::middleware(['permission:custom_rate.add'])->group(function () {
        Route::post('setup-sidebar/unit-custom-rate/store', [BaseRateController::class, 'storeCustomRate']
        )->name('setup-sidebar.custom_rate.store');
    });

    Route::middleware(['permission:custom_rate.edit'])->group(function () {
        Route::put('setup-sidebar/unit-custom-rate/update/{id}', [BaseRateController::class, 'updateCustomRate']
        )->name('setup-sidebar.custom_rate.update');
    });

    Route::middleware(['permission:custom_rate.delete'])->group(function () {
        Route::delete('setup-sidebar/unit-custom-rate/{id}', [BaseRateController::class, 'destroy']
        )->name('setup-sidebar.custom_rate.destroy');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/unit-seasonal-rates', [SeasonalRateController::class, 'index'])
        ->middleware(['permission:seasonal_rate.view'])
        ->name('setup-sidebar.seasonal_rate.index');

    Route::middleware(['permission:seasonal_rate.add'])->group(function () {

        Route::get('setup-sidebar/unit-seasonal-rates/create', [SeasonalRateController::class, 'create'])
            ->name('setup-sidebar.seasonal_rate.create');

        Route::post('setup-sidebar/unit-seasonal-rates/store', [SeasonalRateController::class, 'store'])
            ->name('setup-sidebar.seasonal_rate.store');
    });

    Route::middleware(['permission:seasonal_rate.edit'])->group(function () {
        Route::get('setup-sidebar/unit-seasonal-rates/view/{id}', [SeasonalRateController::class, 'view'])
            ->name('setup-sidebar.seasonal_rate.view');
    });

    Route::middleware(['permission:seasonal_rate.edit'])->group(function () {
        Route::get('setup-sidebar/unit-seasonal-rates/edit/{id}', [SeasonalRateController::class, 'edit'])
            ->name('setup-sidebar.seasonal_rate.edit');

        Route::Put('setup-sidebar/unit-seasonal-rates/update/{id}', [SeasonalRateController::class, 'update'])
            ->name('setup-sidebar.seasonal_rate.update');
    });

    Route::middleware(['permission:seasonal_rate.delete'])->group(function () {
        Route::delete('setup-sidebar/unit-seasonal-rate/delete/{id}', [SeasonalRateController::class, 'delete'])
            ->name('setup-sidebar.seasonal_rate.delete');
    });

    Route::post('setup-sidebar/unit-seasonal-custom/store', [SeasonalRateController::class, 'seasonalCustomRate'])
        ->middleware(['permission:seasonal_custom_rate.add'])
        ->name('setup-sidebar.seasonal_custom_rate.store');
    Route::delete('setup-sidebar/unit-seasonal-custom/delete/{id}', [SeasonalRateController::class, 'deleteCustomRate'])
        ->middleware(['permission:seasonal_custom_rate.delete'])
        ->name('setup-sidebar.seasonal_custom_rate.delete');

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/unit-special-rates', [SpecialRateController::class, 'index'])
        ->middleware(['permission:special_rate.view'])
        ->name('setup-sidebar.special_rate.index');

    Route::middleware(['permission:special_rate.add'])->group(function () {

        Route::get('setup-sidebar/unit-special-rates/create', [SpecialRateController::class, 'create'])
            ->name('setup-sidebar.special_rate.create');

        Route::post('setup-sidebar/unit-special-rates/store', [SpecialRateController::class, 'store'])
            ->name('setup-sidebar.special_rate.store');
    });

    Route::middleware(['permission:special_rate.view'])->group(function () {
        Route::get('setup-sidebar/unit-special-rates/view/{id}', [SpecialRateController::class, 'view'])
            ->name('setup-sidebar.special_rate.view');
    });

    Route::middleware(['permission:special_rate.edit'])->group(function () {
        Route::get('setup-sidebar/unit-special-rates/edit/{id}', [SpecialRateController::class, 'edit'])
            ->name('setup-sidebar.special_rate.edit');

        Route::Put('setup-sidebar/unit-special-rates/update/{id}', [SpecialRateController::class, 'update'])
            ->name('setup-sidebar.special_rate.update');
    });

    Route::middleware(['permission:special_rate.delete'])->group(function () {
        Route::delete('setup-sidebar/unit-special-rate/delete/{id}', [SpecialRateController::class, 'delete'])
            ->name('setup-sidebar.special_rate.delete');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/unit-rate-plan', [RatePlanController::class, 'index'])
        ->middleware(['permission:rate_plan.view'])
        ->name('setup-sidebar.rate_plan.index');

    Route::middleware(['permission:rate_plan.add'])->group(function () {

        Route::get('setup-sidebar/unit-rate-plan/create', [RatePlanController::class, 'create'])
            ->name('setup-sidebar.rate_plan.create');

        Route::post('setup-sidebar/unit-rate-plan/store', [RatePlanController::class, 'store'])
            ->name('setup-sidebar.rate_plan.store');
    });

    Route::middleware(['permission:rate_plan.view'])->group(function () {
        Route::get('setup-sidebar/unit-rate-plan/view/{id}', [RatePlanController::class, 'view'])
            ->name('setup-sidebar.rate_plan.view');
    });

    Route::middleware(['permission:rate_plan.edit'])->group(function () {
        Route::get('setup-sidebar/unit-rate-plan/edit/{id}', [RatePlanController::class, 'edit'])
            ->name('setup-sidebar.rate_plan.edit');

        Route::put('setup-sidebar/unit-rate-plan/update/{id}', [RatePlanController::class, 'update'])
            ->name('setup-sidebar.rate_plan.update');
    });

    Route::middleware(['permission:rate_plan.delete'])->group(function () {
        Route::delete('setup-sidebar/unit-rate-plan/delete/{id}', [RatePlanController::class, 'delete'])
            ->name('setup-sidebar.rate_plan.delete');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/financial-bank-account', [BankAccountController::class, 'index'])
        ->middleware(['permission:bank_account.view'])
        ->name('setup-sidebar.bank_account.index');

    Route::middleware(['permission:bank_account.add'])->group(function () {

        Route::get('setup-sidebar/financial-bank-account/create', [BankAccountController::class, 'create'])
            ->name('setup-sidebar.bank_account.create');

        Route::post('setup-sidebar/financial-bank-account/store', [BankAccountController::class, 'store'])
            ->name('setup-sidebar.bank_account.store');
    });

    Route::middleware(['permission:bank_account.view'])->group(function () {
        Route::get('setup-sidebar/financial-bank-account/view/{bank}', [BankAccountController::class, 'view'])
            ->name('setup-sidebar.bank_account.view');
    });

    Route::middleware(['permission:bank_account.edit'])->group(function () {
        Route::get('setup-sidebar/financial-bank-account/edit/{bank}', [BankAccountController::class, 'edit'])
            ->name('setup-sidebar.bank_account.edit');

        Route::put('setup-sidebar/financial-bank-account/update/{bank}', [BankAccountController::class, 'update'])
            ->name('setup-sidebar.bank_account.update');
    });

    Route::middleware(['permission:bank_account.delete'])->group(function () {
        Route::delete('setup-sidebar/financial-bank-account/delete/{bank}', [BankAccountController::class, 'delete'])
            ->name('setup-sidebar.bank_account.delete');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/financial-cost-center', [CostCenterController::class, 'index'])
        ->middleware(['permission:cost_center.view'])
        ->name('setup-sidebar.cost_center.index');

    Route::middleware(['permission:cost_center.add'])->group(function () {
        Route::post('setup-sidebar/financial-cost-center/store', [CostCenterController::class, 'store'])
            ->name('setup-sidebar.cost_center.store');
    });

    Route::middleware(['permission:cost_center.edit'])->group(function () {
        Route::put('setup-sidebar/financial-cost-center/update/{costCenter}', [CostCenterController::class, 'update'])
            ->name('setup-sidebar.cost_center.update');
    });

    Route::middleware(['permission:cost_center.delete'])->group(function () {
        Route::delete('setup-sidebar/financial-cost-center/delete/{costCenter}', [CostCenterController::class, 'delete'])
            ->name('setup-sidebar.cost_center.delete');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/financial-security-deposit', [SecurityDepositController::class, 'index'])
        ->middleware(['permission:security_deposit.update'])
        ->name('setup-sidebar.security_deposit.index');

    Route::middleware(['permission:security_deposit.update'])->group(function () {
        Route::post('setup-sidebar/financial-security-deposit/store', [SecurityDepositController::class, 'updateDeposits'])
            ->name('setup-sidebar.security_deposit.store');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/financial-taxes', [TaxesController::class, 'index'])
        ->middleware(['permission:tax_customization.view'])
        ->name('setup-sidebar.taxes.index');

    Route::middleware(['permission:tax_customization.add'])->group(function () {
        Route::get('setup-sidebar/financial-taxes/create', [TaxesController::class, 'create'])
            ->name('setup-sidebar.taxes.create');

        Route::post('setup-sidebar/financial-taxes/store', [TaxesController::class, 'store'])
            ->name('setup-sidebar.taxes.store');
    });

    Route::middleware(['permission:tax_customization.edit'])->group(function () {
        Route::get('setup-sidebar/financial-taxes/edit/{id}', [TaxesController::class, 'edit'])
            ->name('setup-sidebar.taxes.edit');

        Route::put('setup-sidebar/financial-taxes/update/{id}', [TaxesController::class, 'update'])
            ->name('setup-sidebar.taxes.update');
    });

    Route::middleware(['permission:tax_customization.delete'])->group(function () {
        Route::get('setup-sidebar/financial-taxes/view/{id}', [TaxesController::class, 'view'])
            ->name('setup-sidebar.taxes.view');
    });

    Route::middleware(['permission:tax_customization.delete'])->group(function () {
        Route::delete('setup-sidebar/financial-taxes/delete/{id}', [TaxesController::class, 'delete'])
            ->name('setup-sidebar.taxes.delete');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/financial-payments', [PaymentController::class, 'index'])
        ->middleware(['permission:payment_method.view'])
        ->name('setup-sidebar.payments.index');

    Route::middleware(['permission:payment_method.add'])->group(function () {
        Route::post('setup-sidebar/financial-payments/store', [PaymentController::class, 'store'])
            ->name('setup-sidebar.payments.store');
    });

    Route::middleware(['permission:payment_method.edit'])->group(function () {
        Route::put('setup-sidebar/financial-payments/update/{id}', [PaymentController::class, 'update'])
            ->name('setup-sidebar.payments.update');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/financial-discount', [DiscountTypeController::class, 'index'])
        ->middleware(['permission:discount_type.view'])
        ->name('setup-sidebar.discount.index');

    Route::middleware(['permission:discount_type.add'])->group(function () {
        Route::post('setup-sidebar/financial-discount/store', [DiscountTypeController::class, 'store'])
            ->name('setup-sidebar.discount.store');
    });

    Route::middleware(['permission:discount_type.edit'])->group(function () {
        Route::put('setup-sidebar/financial-discount/update/{discount}', [DiscountTypeController::class, 'update'])
            ->name('setup-sidebar.discount.update');
    });
    Route::patch('setup-sidebar/financial-discount/status-update/{discount}', [DiscountTypeController::class, 'statusToggle'])
        ->middleware(['permission:discount_type.edit'])
        ->name('setup-sidebar.discount.toggle');

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/setting-date', [DateController::class, 'index'])
        ->middleware(['permission:date_setting.edit'])
        ->name('setup-sidebar.date.index');

    Route::middleware(['permission:date_setting.edit'])->group(function () {
        Route::post('setup-sidebar/setting-date/update', [DateController::class, 'update'])
            ->name('setup-sidebar.date.update');
    });

    Route::get('setup-sidebar/theme-customization', [ThemeCustomizationController::class, 'index'])
        ->middleware(['permission:theme_customization.edit', 'plan.feature:custom_branding'])
        ->name('setup-sidebar.theme_customization.index');

    Route::middleware(['permission:theme_customization.edit', 'plan.feature:custom_branding'])->group(function () {
        Route::post('setup-sidebar/theme-customization/update', [ThemeCustomizationController::class, 'update'])
            ->name('setup-sidebar.theme_customization.update');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/reservation-source', [ReservationSourceController::class, 'index'])
        ->middleware(['permission:reservation_source.view'])
        ->name('setup-sidebar.reservation_source.index');

    Route::middleware(['permission:reservation_source.add'])->group(function () {
        Route::post('setup-sidebar/reservation-source/store', [ReservationSourceController::class, 'store'])
            ->name('setup-sidebar.reservation_source.store');

    });

    Route::middleware(['permission:reservation_source.edit'])->group(function () {
        Route::put('setup-sidebar/reservation-source/update/{id}', [ReservationSourceController::class, 'update'])
            ->name('setup-sidebar.reservation_source.update');
    });

    Route::middleware(['permission:reservation_source.delete'])->group(function () {
        Route::delete('setup-sidebar/reservation-source/delete/{id}', [ReservationSourceController::class, 'delete'])
            ->name('setup-sidebar.reservation_source.delete');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/guest-class-index', [GuestClassController::class, 'index'])
        ->middleware(['permission:guest_class.view'])
        ->name('setup-sidebar.guest_class.index');

    Route::middleware(['permission:guest_class.add'])->group(function () {
        Route::get('setup-sidebar/guest-class-create', [GuestClassController::class, 'create'])
            ->name('setup-sidebar.guest_class.create');

        Route::post('setup-sidebar/guest-class-store', [GuestClassController::class, 'store'])
            ->name('setup-sidebar.guest_class.store');
    });

    Route::middleware(['permission:guest_class.edit'])->group(function () {
        Route::get('setup-sidebar/guest-class-edit/{id}', [GuestClassController::class, 'edit'])
            ->name('setup-sidebar.guest_class.edit');

        Route::put('setup-sidebar/guest-class-update/{guestClass}', [GuestClassController::class, 'update'])
            ->name('setup-sidebar.guest_class.update');
    });

    Route::middleware(['permission:guest_class.delete'])->group(function () {
        Route::delete('setup-sidebar/guest-class-delete/{guestClass}', [GuestClassController::class, 'delete'])
            ->name('setup-sidebar.guest_class.delete');
    });

    Route::middleware(['permission:guest_class.view'])->group(function () {
        Route::get('setup-sidebar/guest-class-view/{id}', [GuestClassController::class, 'view'])
            ->name('setup-sidebar.guest_class.view');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/loyalty-program', [LoyaltyProgramController::class, 'index'])
        ->middleware(['permission:loyalty_setting.view'])
        ->name('setup-sidebar.loyalty_program.index');

    Route::middleware(['permission:loyalty_setting.add'])->group(function () {
        Route::post('setup-sidebar/loyalty-program/store', [LoyaltyProgramController::class, 'store'])
            ->name('setup-sidebar.loyalty_program.store');
    });

    Route::middleware(['permission:loyalty_setting.edit'])->group(function () {
        Route::put('setup-sidebar/loyalty-program/update/{id}', [LoyaltyProgramController::class, 'update'])
            ->name('setup-sidebar.loyalty_program.update');
    });

    Route::middleware(['permission:loyalty_setting.delete'])->group(function () {
        Route::delete('setup-sidebar/loyalty-program/delete/{id}', [LoyaltyProgramController::class, 'delete'])
            ->name('setup-sidebar.loyalty_program.delete');
    });
    Route::post('setup-sidebar/loyalty-program/auto-setting', [LoyaltyProgramController::class, 'toggleAutoUpgrade'])
        ->middleware(['permission:loyalty_setting.edit'])
        ->name('setup-sidebar.loyalty_program.autoUpgrade');

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/auto-sms', [AutoSMSController::class, 'index'])
        ->middleware(['permission:sms.send'])
        ->name('setup-sidebar.auto_sms.index');

    Route::post('setup-sidebar/auto-sms/update', [AutoSMSController::class, 'update'])
        ->middleware(['permission:sms.send'])
        ->name('setup-sidebar.auto_sms.update');

    Route::post('setup-sidebar/auto-sms/append', [AutoSMSController::class, 'appendUser'])
        ->middleware(['permission:sms.send'])
        ->name('setup-sidebar.auto_sms.append');

    Route::delete('setup-sidebar/auto-sms/delete/{id}', [AutoSMSController::class, 'delete'])
        ->middleware(['permission:sms.send'])
        ->name('setup-sidebar.auto_sms.delete');

    Route::get('/sms-user-types/{user}', [AutoSMSController::class, 'getUserTypes'])
        ->middleware(['permission:sms.send']);
    Route::post('/sms-user-types/save', [AutoSMSController::class, 'saveUserTypes'])
        ->middleware(['permission:sms.send']);
    Route::get('setup-sidebar/shomoos', [ShomoosController::class, 'index'])
        ->middleware(['permission:shomoos_setting.view'])
        ->name('setup-sidebar.shomoos.index');
    Route::get('setup-sidebar/shomoos/{submission}', [ShomoosController::class, 'show'])
        ->middleware(['permission:shomoos_setting.view'])
        ->name('setup-sidebar.shomoos.show');
    Route::post('setup-sidebar/shomoos/update', [ShomoosController::class, 'update'])
        ->middleware(['permission:shomoos_setting.view'])
        ->name('setup-sidebar.shomoos.update');
    Route::get('setup-sidebar/ntmp', [NtmpController::class, 'index'])
        ->middleware(['permission:ntmp_setting.view'])
        ->name('setup-sidebar.ntmp.index');
    Route::get('setup-sidebar/ntmp/{submission}', [NtmpController::class, 'show'])
        ->middleware(['permission:ntmp_setting.view'])
        ->name('setup-sidebar.ntmp.show');
    Route::post('setup-sidebar/ntmp/update', [NtmpController::class, 'update'])
        ->middleware(['permission:ntmp_setting.view'])
        ->name('setup-sidebar.ntmp.update');

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/property-facility', [PropertyFacilityController::class, 'index'])
        ->middleware(['permission:property_facility.view'])
        ->name('setup-sidebar.property_facility.index');

    Route::middleware(['permission:property_facility.add'])->group(function () {
        Route::post('setup-sidebar/property-facility/store', [PropertyFacilityController::class, 'store'])
            ->name('setup-sidebar.property_facility.store');
    });

    Route::middleware(['permission:property_facility.edit'])->group(function () {
        Route::put('setup-sidebar/property-facility/update/{id}', [PropertyFacilityController::class, 'update'])
            ->name('setup-sidebar.property_facility.update');
    });

    Route::middleware(['permission:property_facility.delete'])->group(function () {
        Route::delete('setup-sidebar/property-facility/delete/{id}', [PropertyFacilityController::class, 'delete'])
            ->name('setup-sidebar.property_facility.delete');
    });
    Route::post('setup-sidebar/property-facility/toggleUpdate/{id}', [PropertyFacilityController::class, 'toggleStatus'])
        ->middleware(['permission:property_facility.edit'])
        ->name('setup-sidebar.property_facility.toggleUpdate');
    Route::get('/admin/get-facilities', [PropertyFacilityController::class, 'getFacilities'])
        ->name('setup-sidebar.property_facility.facilities');

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/reporting-numbering', [NumberingController::class, 'index'])
        ->middleware(['permission:numbering_option.edit'])
        ->name('setup-sidebar.numbering_option.index');

    Route::middleware(['permission:numbering_option.edit'])->group(function () {
        Route::put('setup-sidebar/reporting-numbering.update/{id}', [NumberingController::class, 'update'])
            ->name('setup-sidebar.numbering_option.update');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/reporting-printing', [PrintingController::class, 'index'])
        ->middleware(['permission:printing_option.edit'])
        ->name('setup-sidebar.print_option.index');

    Route::middleware(['permission:printing_option.edit'])->group(function () {
        Route::post('setup-sidebar/reporting-printing/update', [PrintingController::class, 'update'])
            ->name('setup-sidebar.print_option.update');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/outlets-setup', [OutletSetupController::class, 'index'])
        ->middleware(['permission:outlet_setup.view'])
        ->name('setup-sidebar.outlet_setup.index');

    Route::middleware(['permission:outlet_setup.add'])->group(function () {
        Route::post('setup-sidebar/outlets-setup/store', [OutletSetupController::class, 'store'])
            ->name('setup-sidebar.outlet_setup.store');
    });

    Route::middleware(['permission:outlet_setup.edit'])->group(function () {
        Route::put('setup-sidebar/outlets-setup/update/{id}', [OutletSetupController::class, 'update'])
            ->name('setup-sidebar.outlet_setup.update');
    });

    Route::middleware(['permission:outlet_setup.delete'])->group(function () {
        Route::delete('setup-sidebar/outlets-setup/delete/{id}', [OutletSetupController::class, 'delete'])
            ->name('setup-sidebar.outlet_setup.delete');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/item-categories', [ItemCategoryController::class, 'index'])
        ->middleware(['permission:item_categories.view'])
        ->name('setup-sidebar.item_category.index');

    Route::middleware(['permission:item_categories.add'])->group(function () {
        Route::post('setup-sidebar/item-categories/store', [ItemCategoryController::class, 'store'])
            ->name('setup-sidebar.item_category.store');
    });

    Route::middleware(['permission:item_categories.edit'])->group(function () {
        Route::put('setup-sidebar/item-categories/update/{id}', [ItemCategoryController::class, 'update'])
            ->name('setup-sidebar.item_category.update');
    });

    Route::middleware(['permission:item_categories.delete'])->group(function () {
        Route::delete('setup-sidebar/item-categories/delete/{id}', [ItemCategoryController::class, 'delete'])
            ->name('setup-sidebar.item_category.delete');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/item', [ItemsController::class, 'index'])
        ->middleware(['permission:outlet_item.view'])
        ->name('setup-sidebar.items.index');

    Route::middleware(['permission:outlet_item.add'])->group(function () {
        Route::get('setup-sidebar/item/create', [ItemsController::class, 'create'])
            ->name('setup-sidebar.items.create');

        Route::post('setup-sidebar/item/store', [ItemsController::class, 'store'])
            ->name('setup-sidebar.items.store');
    });

    Route::middleware(['permission:outlet_item.edit'])->group(function () {
        Route::get('setup-sidebar/item/edit/{id}', [ItemsController::class, 'edit'])
            ->name('setup-sidebar.items.edit');

        Route::put('setup-sidebar/item/update/{id}', [ItemsController::class, 'update'])
            ->name('setup-sidebar.items.update');
    });

    Route::middleware(['permission:outlet_item.delete'])->group(function () {
        Route::delete('setup-sidebar/item/delete/{id}', [ItemsController::class, 'delete'])
            ->name('setup-sidebar.items.delete');
    });
    Route::get('/outlet/{outlet}/categories', [ItemsController::class, 'getCategories']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/conditions', [ConditionController::class, 'index'])
        ->middleware(['permission:terms_and_condition.add|terms_and_condition.edit|terms_and_condition.delete'])
        ->name('setup-sidebar.condition.index');

    Route::middleware(['permission:terms_and_condition.add'])->group(function () {
        Route::get('setup-sidebar/conditions/create', [ConditionController::class, 'create'])
            ->name('setup-sidebar.condition.create');

        Route::post('setup-sidebar/conditions/store', [ConditionController::class, 'store'])
            ->name('setup-sidebar.condition.store');
    });

    Route::middleware(['permission:terms_and_condition.edit'])->group(function () {

        Route::get('setup-sidebar/conditions/edit/{id}', [ConditionController::class, 'edit'])
            ->name('setup-sidebar.condition.edit');

        Route::put('setup-sidebar/conditions/update/{id}', [ConditionController::class, 'update'])
            ->name('setup-sidebar.condition.update');
    });

    Route::middleware(['permission:terms_and_condition.delete'])->group(function () {
        Route::delete('setup-sidebar/conditions/delete/{id}', [ConditionController::class, 'delete'])
            ->name('setup-sidebar.condition.delete');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/penalties', [PenaltyController::class, 'index'])
        ->middleware(['permission:penalties.add|penalties.edit|penalties.delete'])
        ->name('setup-sidebar.penalty.index');

    Route::middleware(['permission:penalties.add'])->group(function () {
        Route::get('setup-sidebar/penalties/create', [PenaltyController::class, 'create'])
            ->name('setup-sidebar.penalty.create');

        Route::post('setup-sidebar/penalties/store', [PenaltyController::class, 'store'])
            ->name('setup-sidebar.penalty.store');
    });

    Route::middleware(['permission:penalties.edit'])->group(function () {

        Route::get('setup-sidebar/penalties/edit/{id}', [PenaltyController::class, 'edit'])
            ->name('setup-sidebar.penalty.edit');

        Route::put('setup-sidebar/penalties/update/{id}', [PenaltyController::class, 'update'])
            ->name('setup-sidebar.penalty.update');
    });

    Route::middleware(['permission:penalties.delete'])->group(function () {
        Route::delete('setup-sidebar/penalties/delete/{id}', [PenaltyController::class, 'delete'])
            ->name('setup-sidebar.penalty.delete');
    });
    Route::post('setup-sidebar/penalties/update-setting', [PenaltyController::class, 'updateSetting'])
        ->middleware(['permission:penalties.edit'])
        ->name('setup-sidebar.penalty.update-setting');
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/cancel', [CancelReasonController::class, 'index'])
        ->middleware(['permission:cancel_reason.add|cancel_reason.edit|cancel_reason.delete'])
        ->name('setup-sidebar.cancel_reason.index');

    Route::middleware(['permission:cancel_reason.add'])->group(function () {
        Route::get('setup-sidebar/cancel/create', [CancelReasonController::class, 'create'])
            ->name('setup-sidebar.cancel_reason.create');
        Route::post('setup-sidebar/cancel', [CancelReasonController::class, 'store'])
            ->name('setup-sidebar.cancel_reason.store');
    });

    Route::middleware(['permission:cancel_reason.edit'])->group(function () {

        Route::get('setup-sidebar/cancel/{id}/edit', [CancelReasonController::class, 'edit'])
            ->name('setup-sidebar.cancel_reason.edit');
        Route::put('setup-sidebar/cancel/{id}', [CancelReasonController::class, 'update'])
            ->name('setup-sidebar.cancel_reason.update');
    });

    Route::middleware(['permission:cancel_reason.delete'])->group(function () {
        Route::delete('setup-sidebar/cancel/{id}', [CancelReasonController::class, 'destroy'])
            ->name('setup-sidebar.cancel_reason.destroy');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/setup-reservation', [SetupReservationController::class, 'index'])
        ->middleware(['permission:setup_reservation.edit'])
        ->name('setup-sidebar.setup_reservation.index');

    Route::middleware(['permission:setup_reservation.edit'])->group(function () {

        Route::put('setup-sidebar/setup-reservation', [SetupReservationController::class, 'update'])
            ->name('setup-sidebar.setup_reservation.update');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/unit-reason', [UnitReasonController::class, 'index'])
        ->middleware(['permission:unit_reason.add|unit_reason.edit|unit_reason.delete'])
        ->name('setup-sidebar.unit_reason.index');

    Route::middleware(['permission:unit_reason.add'])->group(function () {
        Route::post('setup-sidebar/unit-reason', [UnitReasonController::class, 'store'])
            ->name('setup-sidebar.unit_reason.store');
    });

    Route::middleware(['permission:unit_reason.edit'])->group(function () {
        Route::put('setup-sidebar/unit-reason/{id}', [UnitReasonController::class, 'update'])
            ->name('setup-sidebar.unit_reason.update');
    });

    Route::middleware(['permission:unit_reason.delete'])->group(function () {
        Route::delete('setup-sidebar/unit-reason/{id}', [UnitReasonController::class, 'destroy'])
            ->name('setup-sidebar.unit_reason.destroy');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/night-audit', [NightAuditController::class, 'index'])
        ->middleware(['permission:night_audit.edit'])
        ->name('setup-sidebar.night_audit.index');

    Route::middleware(['permission:night_audit.edit'])->group(function () {
        Route::put('setup-sidebar/night-audit', [NightAuditController::class, 'update'])
            ->name('setup-sidebar.night_audit.update');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/guest-feedback', [GuestFeedbackController::class, 'index'])
        ->middleware(['permission:guest_feedback.view|feedback_metric.add|feedback_metric.edit|feedback_metric.delete'])
        ->name('setup-sidebar.guest_feedback.index');

    Route::middleware(['permission:feedback_metric.add'])->group(function () {
        Route::post('setup-sidebar/guest-feedback', [GuestFeedbackController::class, 'store'])
            ->name('setup-sidebar.guest_feedback.store');
    });

    Route::middleware(['permission:feedback_metric.edit'])->group(function () {
        Route::put('setup-sidebar/guest-feedback/{id}', [GuestFeedbackController::class, 'update'])
            ->name('setup-sidebar.guest_feedback.update');
    });

    Route::middleware(['permission:feedback_metric.delete'])->group(function () {
        Route::delete('setup-sidebar/guest-feedback/{id}', [GuestFeedbackController::class, 'destroy'])
            ->name('setup-sidebar.guest_feedback.destroy');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/housekeeper-setting', [HouseKeeperController::class, 'index'])
        ->middleware(['permission:housekeeper_list.view'])
        ->name('setup-sidebar.housekeeping_setting.index');

    Route::middleware(['permission:housekeeper_list.add'])->group(function () {
        Route::post('setup-sidebar/housekeeper-setting', [HouseKeeperController::class, 'store'])
            ->name('setup-sidebar.housekeeping_setting.store');
    });

    Route::middleware(['permission:housekeeper_list.edit'])->group(function () {
        Route::put('setup-sidebar/housekeeper-setting/{id}', [HouseKeeperController::class, 'update'])
            ->name('setup-sidebar.housekeeping_setting.update');
    });

    Route::middleware(['permission:housekeeper_list.delete'])->group(function () {
        Route::delete('setup-sidebar/housekeeper-setting/{id}', [HouseKeeperController::class, 'destroy'])
            ->name('setup-sidebar.housekeeping_setting.destroy');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/staff-attendance', [StaffAttendanceController::class, 'index'])
        ->middleware(['permission:staff_attendance.view'])
        ->name('setup-sidebar.staff_attendance.index');

    Route::middleware(['permission:housekeeper_list.edit'])->group(function () {
        Route::put('setup-sidebar/staff-attendance/{attendance}', [StaffAttendanceController::class, 'update'])
            ->name('setup-sidebar.staff_attendance.update');
    });

    Route::middleware(['permission:housekeeper_list.delete'])->group(function () {
        Route::delete('setup-sidebar/staff-attendance/{attendance}', [StaffAttendanceController::class, 'destroy'])
            ->name('setup-sidebar.staff_attendance.destroy');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/housekeeper-task', [TaskTypeController::class, 'index'])
        ->middleware(['permission:task_type.view|housekeeper_task.view'])
        ->name('setup-sidebar.task_type.index');

    Route::middleware(['permission:housekeeper_task.add'])->group(function () {
        Route::post('setup-sidebar/housekeeper-task', [TaskTypeController::class, 'store'])
            ->name('setup-sidebar.task_type.store');
    });

    Route::middleware(['permission:housekeeper_task.edit'])->group(function () {
        Route::put('setup-sidebar/housekeeper-task/{id}', [TaskTypeController::class, 'update'])
            ->name('setup-sidebar.task_type.update');
    });

    Route::middleware(['permission:housekeeper_task.delete'])->group(function () {
        Route::delete('setup-sidebar/housekeeper-task/{id}', [TaskTypeController::class, 'destroy'])
            ->name('setup-sidebar.task_type.destroy');
    });

    Route::middleware(['permission:housekeeper_task.status'])->group(function () {
        Route::post('setup-sidebar/housekeeper-task/{id}/toggle', [TaskTypeController::class, 'toggleStatus'])
            ->name('setup-sidebar.task_type.toggle');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/customers-guest', [GuestController::class, 'index'])
        ->middleware(['permission:guest.view'])
        ->name('dashboard.guest.index');

    Route::get('dashboard/customers-guest/search', [GuestController::class, 'search'])
        ->middleware(['permission:guest.view'])
        ->name('dashboard.guest.search');

    Route::get('dashboard/sms-manual', [ManualSMSController::class, 'index'])
        ->middleware(['permission:sms.send'])
        ->name('dashboard.manual_sms.index');

    Route::post('dashboard/sms-manual/send', [ManualSMSController::class, 'send'])
        ->middleware(['permission:sms.send'])
        ->name('dashboard.manual_sms.send');

    Route::middleware(['permission:guest.add'])->group(function () {
        Route::post('dashboard/customers-guest', [GuestController::class, 'store'])
            ->name('dashboard.guest.store');
    });

    Route::middleware(['permission:guest.edit'])->group(function () {
        Route::put('dashboard/customers-guest/{guest}', [GuestController::class, 'update'])
            ->name('dashboard.guest.update');
    });

    Route::middleware(['permission:guest.delete'])->group(function () {
        Route::delete('dashboard/customers-guest/{guest}', [GuestController::class, 'destroy'])
            ->name('dashboard.guest.destroy');
    });

    Route::middleware(['permission:guest.view'])->group(function () {
        Route::get('dashboard/customers-guest/{guest}', [GuestController::class, 'show'])
            ->name('dashboard.guest.show');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/customers-corporate', [CorporateController::class, 'index'])
        ->middleware(['permission:corporate.view'])
        ->name('dashboard.corporate.index');

    Route::get('dashboard/customers-corporate/search', [CorporateController::class, 'search'])
        ->middleware(['permission:corporate.view'])
        ->name('dashboard.corporate.search');

    Route::middleware(['permission:corporate.add'])->group(function () {
        Route::post('dashboard/customers-corporate', [CorporateController::class, 'store'])
            ->name('dashboard.corporate.store');
    });

    Route::middleware(['permission:corporate.edit'])->group(function () {
        Route::put('dashboard/customers-corporate/{corporate}', [CorporateController::class, 'update'])
            ->name('dashboard.corporate.update');
    });

    Route::middleware(['permission:corporate.delete'])->group(function () {
        Route::delete('dashboard/customers-corporate/{corporate}', [CorporateController::class, 'destroy'])
            ->name('dashboard.corporate.destroy');
    });

    Route::middleware(['permission:corporate.view'])->group(function () {
        Route::get('dashboard/customers-corporate/{corporate}', [CorporateController::class, 'show'])
            ->name('dashboard.corporate.show');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/customers-vendors', [\App\Http\Controllers\Customers\VendorController::class, 'index'])
        ->middleware(['permission:vendor.view'])
        ->name('dashboard.vendor.index');

    Route::middleware(['permission:vendor.add'])->group(function () {
        Route::post('dashboard/customers-vendors', [\App\Http\Controllers\Customers\VendorController::class, 'store'])
            ->name('dashboard.vendor.store');
    });

    Route::middleware(['permission:vendor.edit'])->group(function () {
        Route::put('dashboard/customers-vendors/{vendor}', [\App\Http\Controllers\Customers\VendorController::class, 'update'])
            ->name('dashboard.vendor.update');
    });

    Route::middleware(['permission:vendor.delete'])->group(function () {
        Route::delete('dashboard/customers-vendors/{vendor}', [\App\Http\Controllers\Customers\VendorController::class, 'destroy'])
            ->name('dashboard.vendor.destroy');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/reservation', [ReservationController::class, 'index'])
        ->middleware(['permission:reservation.view'])
        ->name('dashboard.reservation.index');

    Route::get('dashboard/reservation/calendar-events', [ReservationController::class, 'calendarEvents'])
        ->middleware(['permission:reservation.view'])
        ->name('dashboard.reservation.calendar_events');

    Route::middleware(['permission:reservation.contract'])->group(function () {
        Route::get('dashboard/reservation/contract-template', [ReservationController::class, 'downloadContractTemplate'])
            ->name('dashboard.reservation.contract_template');

        Route::get('dashboard/reservation/{reservation}/contract', [ReservationController::class, 'downloadContract'])
            ->name('dashboard.reservation.contract');
    });
    Route::get('dashboard/reservation/{reservation}/contract-modal', [ReservationController::class, 'contractModal'])
        ->middleware(['permission:reservation.contract'])
        ->name('dashboard.reservation.contract_modal');

    Route::middleware(['permission:reservation.add'])->group(function () {
        Route::get('dashboard/reservation/create', [ReservationController::class, 'create'])
            ->name('dashboard.reservation.create');

        Route::post('dashboard/reservation/store', [ReservationController::class, 'store'])
            ->name('dashboard.reservation.store');
    });

    Route::middleware(['permission:reservation.edit'])->group(function () {
        Route::get('dashboard/reservation/{reservation}/edit', [ReservationController::class, 'edit'])
            ->name('dashboard.reservation.edit');

        Route::put('dashboard/reservation/{reservation}', [ReservationController::class, 'update'])
            ->name('dashboard.reservation.update');
    });

    Route::post('dashboard/reservation/cancel', [ReservationController::class, 'cancelReservation'])
        ->middleware(['permission:reservation.cancel'])
        ->name('dashboard.reservation.cancel');

    Route::get('/cancel-reason/{id}/penalties', [ReservationController::class, 'getPenalties'])
        ->middleware(['permission:reservation.view|reservation.add|reservation.edit']);

    Route::get('dashboard/reservation/available-units', [ReservationController::class, 'getAvailableUnits'])
        ->middleware(['permission:reservation.view|reservation.add'])
        ->name('dashboard.reservation.available_units');

    Route::get('dashboard/reservation/get-unavailable-units', [ReservationController::class, 'getUnavailableUnits'])
        ->middleware(['permission:reservation.view'])
        ->name('dashboard.reservation.get_unavailable_units');

    Route::get('dashboard/reservation/notifications', [ReservationController::class, 'getNotifications'])
        ->middleware(['permission:dashboard.view|reservation.view|unit_status.view|housekeeping_task.view|receipt.view|outlet_setup.view|guest.view|sms.send|cash_drawer_balance.view|reports.view|logs.view|night_audit.edit'])
        ->name('dashboard.reservation.notifications');

    Route::post('dashboard/reservation/notifications/mark-read', [ReservationController::class, 'markNotificationRead'])
        ->middleware(['permission:dashboard.view|reservation.view|unit_status.view|housekeeping_task.view|receipt.view|outlet_setup.view|guest.view|sms.send|cash_drawer_balance.view|reports.view|logs.view|night_audit.edit'])
        ->name('dashboard.reservation.notifications.mark_read');

    Route::post('dashboard/reservation/notifications/mark-all-read', [ReservationController::class, 'markAllNotificationsRead'])
        ->middleware(['permission:dashboard.view|reservation.view|unit_status.view|housekeeping_task.view|receipt.view|outlet_setup.view|guest.view|sms.send|cash_drawer_balance.view|reports.view|logs.view|night_audit.edit'])
        ->name('dashboard.reservation.notifications.mark_all_read');

    Route::get('dashboard/notifications', [ReservationController::class, 'allNotifications'])
        ->middleware(['permission:dashboard.view|reservation.view|unit_status.view|housekeeping_task.view|receipt.view|outlet_setup.view|guest.view|sms.send|cash_drawer_balance.view|reports.view|logs.view|night_audit.edit'])
        ->name('dashboard.notifications.index');

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/unit-status', [UnitStatusController::class, 'index'])
        ->middleware(['permission:unit_status.view'])
        ->name('dashboard.unit_status.index');

    Route::put('/dashboard/unit-status/{unit}/update-status', [UnitStatusController::class, 'updateStatus'])
        ->middleware(['permission:unit_status.view'])
        ->name('dashboard.unit_status.update');

    Route::get('dashboard/housekeeping-status', [StatusController::class, 'index'])
        ->middleware(['permission:unit_status.view'])
        ->name('dashboard.housekeeping_status.index');

    Route::put('/dashboard/housekeeping-status/{unit}/update-status',
        [StatusController::class, 'updateStatus']
    )->middleware(['permission:unit_status.view'])->name('housekeeping.updateStatus');

    Route::get('dashboard/housekeeping-status-print', [StatusController::class, 'print'])
        ->middleware(['permission:unit_status.view'])
        ->name('dashboard.housekeeping_status.print');

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/housekeeping-task', [TaskController::class, 'index'])
        ->middleware(['permission:housekeeping_task.view'])
        ->name('dashboard.housekeeping_task.index');

    Route::get('dashboard/housekeeping-task/units', [TaskController::class, 'getUnits'])
        ->middleware(['permission:housekeeping_task.view|housekeeping_task.add|housekeeping_task.edit'])
        ->name('dashboard.housekeeping_task.get_units');

    Route::get('dashboard/housekeeping-task/print', [TaskController::class, 'print'])
        ->middleware(['permission:housekeeping_task.view'])
        ->name('dashboard.housekeeping_task.print');

    Route::middleware(['permission:housekeeping_task.add'])->group(function () {
        Route::post('dashboard/housekeeping-task', [TaskController::class, 'store'])
            ->name('dashboard.housekeeping_task.store');
    });

    Route::middleware(['permission:housekeeping_task.edit'])->group(function () {
        Route::get('dashboard/housekeeping-task/{task}', [TaskController::class, 'edit'])
            ->name('dashboard.housekeeping_task.edit');

        Route::put('dashboard/housekeeping-task/{task}', [TaskController::class, 'update'])
            ->name('dashboard.housekeeping_task.update');
    });

    Route::middleware(['permission:housekeeping_task.delete'])->group(function () {
        Route::delete('dashboard/housekeeping-task/{task}', [TaskController::class, 'destroy'])
            ->name('dashboard.housekeeping_task.destroy');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/vouchers-invoice', [InvoiceController::class, 'index'])
        ->middleware(['permission:invoice.view'])
        ->name('dashboard.invoice.index');

    Route::middleware(['permission:invoice.view'])->group(function () {
        Route::get('dashboard/vouchers-invoice/{id}', [InvoiceController::class, 'show'])
            ->name('dashboard.invoice.show');
    });

    Route::middleware(['permission:invoice.print'])->group(function () {
        Route::get('dashboard/vouchers-invoice/{id}/print', [InvoiceController::class, 'print'])
            ->name('dashboard.invoice.print');

    });

    Route::middleware(['permission:invoice.edit'])->group(function () {
        Route::put('dashboard/vouchers-invoice/{id}', [InvoiceController::class, 'update'])
            ->name('dashboard.invoice.update');
    });

    Route::middleware(['permission:invoice.email'])->group(function () {
        Route::post('dashboard/vouchers-invoice/{id}/send', [InvoiceController::class, 'sendEmail'])
            ->name('dashboard.invoice.send');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/vouchers-receipt', [ReceiptController::class, 'index'])
        ->middleware(['permission:receipt.view'])
        ->name('dashboard.receipt.index');

    Route::get('dashboard/vouchers-receipt/search-guests', [App\Http\Controllers\Vouchers\ReceiptController::class, 'searchGuests'])
        ->middleware(['permission:receipt.view|receipt.add|receipt.edit'])
        ->name('dashboard.receipt.searchGuests');

    Route::get('dashboard/vouchers-receipt/search-corporates', [App\Http\Controllers\Vouchers\ReceiptController::class, 'searchCorporates'])
        ->middleware(['permission:receipt.view|receipt.add|receipt.edit'])
        ->name('dashboard.receipt.searchCorporates');
    Route::get('dashboard/vouchers-receipt/{id}', [App\Http\Controllers\Vouchers\ReceiptController::class, 'show'])
        ->middleware(['permission:receipt.view'])
        ->name('dashboard.receipt.show');

    Route::middleware(['permission:receipt.add'])->group(function () {
        Route::post('dashboard/vouchers-receipt', [App\Http\Controllers\Vouchers\ReceiptController::class, 'store'])
            ->name('dashboard.receipt.store');
    });

    Route::middleware(['permission:receipt.print'])->group(function () {
        Route::get('dashboard/vouchers-receipt/{id}/print', [App\Http\Controllers\Vouchers\ReceiptController::class, 'print'])
            ->name('dashboard.receipt.print');

    });

    Route::middleware(['permission:receipt.edit'])->group(function () {
        Route::put('dashboard/vouchers-receipt/{id}', [App\Http\Controllers\Vouchers\ReceiptController::class, 'update'])
            ->name('dashboard.receipt.update');
    });

    Route::middleware(['permission:receipt.cancel'])->group(function () {
        Route::post('dashboard/vouchers-receipt/{id}/cancel', [App\Http\Controllers\Vouchers\ReceiptController::class, 'cancel'])
            ->name('dashboard.receipt.cancel');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/vouchers-payment', [App\Http\Controllers\Vouchers\PaymentController::class, 'index'])
        ->middleware(['permission:payment.view'])
        ->name('dashboard.payment.index');

    Route::get('dashboard/vouchers-payment/{id}', [App\Http\Controllers\Vouchers\PaymentController::class, 'show'])
        ->middleware(['permission:payment.view'])
        ->name('dashboard.payment.show');

    Route::get('dashboard/vouchers-payment/search-vendors', [App\Http\Controllers\Vouchers\PaymentController::class, 'searchVendors'])
        ->middleware(['permission:payment.view|payment.add|payment.edit'])
        ->name('dashboard.payment.searchVendors');

    Route::middleware(['permission:payment.add'])->group(function () {
        Route::post('dashboard/vouchers-payment', [App\Http\Controllers\Vouchers\PaymentController::class, 'store'])
            ->name('dashboard.payment.store');
    });

    Route::middleware(['permission:payment.print'])->group(function () {
        Route::get('dashboard/vouchers-payment/{id}/print', [App\Http\Controllers\Vouchers\PaymentController::class, 'print'])
            ->name('dashboard.payment.print');
    });

    Route::middleware(['permission:payment.edit'])->group(function () {
        Route::put('dashboard/vouchers-payment/{id}', [App\Http\Controllers\Vouchers\PaymentController::class, 'update'])
            ->name('dashboard.payment.update');
    });

    Route::middleware(['permission:payment.cancel'])->group(function () {
        Route::post('dashboard/vouchers-payment/{id}/cancel', [App\Http\Controllers\Vouchers\PaymentController::class, 'cancel'])
            ->name('dashboard.payment.cancel');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/vouchers-promissory', [PromissoryController::class, 'index'])
        ->middleware(['permission:promissory_note.view'])
        ->name('dashboard.promissory.index');

    Route::get('dashboard/vouchers-promissory/{id}', [PromissoryController::class, 'show'])
        ->middleware(['permission:promissory_note.view'])
        ->name('dashboard.promissory.show');

    Route::middleware(['permission:promissory_note.add'])->group(function () {
        Route::post('dashboard/vouchers-promissory', [PromissoryController::class, 'store'])
            ->name('dashboard.promissory.store');
    });

    Route::middleware(['permission:promissory_note.print'])->group(function () {
        Route::get('dashboard/vouchers-promissory/{id}/print', [PromissoryController::class, 'print'])
            ->name('dashboard.promissory.print');
    });

    Route::middleware(['permission:promissory_note.edit'])->group(function () {
        Route::put('dashboard/vouchers-promissory/{id}', [PromissoryController::class, 'update'])
            ->name('dashboard.promissory.update');
    });

    Route::middleware(['permission:promissory_note.cancel'])->group(function () {
        Route::post('dashboard/vouchers-promissory/{id}/cancel', [PromissoryController::class, 'cancel'])
            ->name('dashboard.promissory.cancel');
    });

    Route::middleware(['permission:promissory_note.collect'])->group(function () {
        Route::post('dashboard/vouchers-promissory/{id}/collect', [PromissoryController::class, 'collect'])
            ->name('dashboard.promissory.collect');
    });

    Route::middleware(['permission:promissory_note.link'])->group(function () {
        Route::post('dashboard/vouchers-promissory/{id}/link-reservation', [PromissoryController::class, 'linkReservation'])
            ->name('dashboard.promissory.linkReservation');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/vouchers-credit', [CreditController::class, 'index'])
        ->middleware(['permission:credit_notes.print|credit_notes.whatsapp|credit_notes.sms'])
        ->name('dashboard.credit.index');

    Route::get('dashboard/vouchers-credit/{id}', [CreditController::class, 'show'])
        ->middleware(['permission:credit_notes.print|credit_notes.whatsapp|credit_notes.sms'])
        ->name('dashboard.credit.show');

    Route::middleware(['permission:credit_notes.print'])->group(function () {
        Route::get('dashboard/vouchers-credit/{id}/print', [CreditController::class, 'print'])
            ->name('dashboard.credit.print');
    });

    Route::middleware(['permission:credit_notes.whatsapp'])->group(function () {
        Route::post('dashboard/vouchers-credit/{id}/whatsapp', [CreditController::class, 'sendWhatsApp'])
            ->name('dashboard.credit.whatsapp');
    });

    Route::middleware(['permission:credit_notes.sms'])->group(function () {
        Route::post('dashboard/vouchers-credit/{id}/sms', [CreditController::class, 'sendSms'])
            ->name('dashboard.credit.sms');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/vouchers-drop', [DropController::class, 'index'])
        ->middleware(['permission:drop_cash.view'])
        ->name('dashboard.drop_cash.index');

    Route::middleware(['permission:drop_cash.add'])->group(function () {
        Route::post('dashboard/vouchers-drop', [DropController::class, 'store'])
            ->name('dashboard.drop_cash.store');
        Route::post('dashboard/vouchers-drop/calculate', [DropController::class, 'calculateAmount'])
            ->name('dashboard.drop_cash.calculate');
    });

    Route::middleware(['permission:drop_cash.edit'])->group(function () {
        Route::put('dashboard/vouchers-drop/{id}', [DropController::class, 'update'])
            ->name('dashboard.drop_cash.update');
    });

    Route::middleware(['permission:drop_cash.delete'])->group(function () {
        Route::delete('dashboard/vouchers-drop/{id}', [DropController::class, 'destroy'])
            ->name('dashboard.drop_cash.destroy');
    });

    Route::middleware(['permission:drop_cash.print'])->group(function () {
        Route::get('dashboard/vouchers-drop/{id}/print', [DropController::class, 'print'])
            ->name('dashboard.drop_cash.print');
    });

    Route::middleware(['permission:drop_cash.view'])->group(function () {
        Route::get('dashboard/vouchers-drop/{id}', [DropController::class, 'show'])
            ->name('dashboard.drop_cash.show');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/cash-drawer', [CashDrawerController::class, 'index'])
        ->middleware(['permission:cash_drawer_balance.view'])
        ->name('dashboard.cash_drawer.index');

    Route::get('dashboard/cash-drawer/export', [CashDrawerController::class, 'export'])
        ->middleware(['permission:cash_drawer_balance.view'])
        ->name('dashboard.cash_drawer.export');

});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard/night-audit', [App\Http\Controllers\NightAuditController::class, 'index'])
        ->middleware(['permission:night_audit.edit'])
        ->name('dashboard.night_audit.index');

    Route::post('dashboard/night-audit/{id}/complete', [App\Http\Controllers\NightAuditController::class, 'complete'])
        ->middleware(['permission:night_audit.edit'])
        ->name('dashboard.night_audit.complete');
    Route::post('dashboard/night-audit/{id}/fail', [App\Http\Controllers\NightAuditController::class, 'fail'])
        ->middleware(['permission:night_audit.edit'])
        ->name('dashboard.night_audit.fail');

    Route::middleware(['permission:night_audit.start'])->group(function () {
        Route::post('dashboard/night-audit/start', [App\Http\Controllers\NightAuditController::class, 'start'])
            ->name('dashboard.night_audit.start');
    });

    Route::middleware(['permission:night_audit.download'])->group(function () {
        Route::get('dashboard/night-audit/{id}/download', [App\Http\Controllers\NightAuditController::class, 'download'])
            ->name('dashboard.night_audit.download');
    });

    Route::middleware(['permission:night_audit.delete'])->group(function () {
        Route::delete('dashboard/night-audit/{id}', [App\Http\Controllers\NightAuditController::class, 'destroy'])
            ->name('dashboard.night_audit.destroy');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('setup-sidebar/setup-subscription', [SubscriptionController::class, 'index'])
    ->middleware('can:subscription.view')
    ->name('setup-sidebar.setup_subscription.index');

    Route::post('setup-sidebar/setup-subscription/send-request', [SubscriptionController::class, 'sendRequest'])
    ->middleware('can:subscription.view')
    ->name('setup-sidebar.setup_subscription.send');

    Route::get('dashboard/outlets-property', [App\Http\Controllers\Outlets\PropertyController::class, 'index'])
        ->middleware(['permission:outlet_setup.view'])
        ->name('dashboard.outlet_property.index');

    Route::get('dashboard/outlets-order', [App\Http\Controllers\Outlets\OrderController::class, 'index'])
        ->middleware(['permission:outlet_setup.view'])
        ->name('dashboard.outlet_order.index');

    Route::middleware(['permission:reports.view'])->group(function () {
        Route::get('dashboard/reports', [ReportController::class, 'index'])
            ->name('dashboard.reports.index');
    });

    Route::prefix('dashboard/reports')->name('dashboard.reports.')->middleware(['permission:reports.view'])->group(function () {
        Route::get('financial-transactions', [\App\Http\Controllers\Reports\ReportsController::class, 'financialTransactions'])->name('financial_transactions');
        Route::get('daily-transactions', [\App\Http\Controllers\Reports\ReportsController::class, 'dailyTransactions'])->name('daily_transactions');
        Route::get('trial-balance', [\App\Http\Controllers\Reports\ReportsController::class, 'trialBalance'])->name('trial_balance');
        Route::get('tax', [\App\Http\Controllers\Reports\ReportsController::class, 'tax'])->name('tax');
        Route::get('reservation-balances', [\App\Http\Controllers\Reports\ReportsController::class, 'reservationBalances'])->name('reservation_balances');
        Route::get('receipt-vouchers', [\App\Http\Controllers\Reports\ReportsController::class, 'receiptVouchers'])->name('receipt_vouchers');
        Route::get('payment-vouchers', [\App\Http\Controllers\Reports\ReportsController::class, 'paymentVouchers'])->name('payment_vouchers');
        Route::get('invoices', [\App\Http\Controllers\Reports\ReportsController::class, 'invoices'])->name('invoices');
        Route::get('credit-notes', [\App\Http\Controllers\Reports\ReportsController::class, 'creditNotes'])->name('credit_notes');
        Route::get('promissory-notes', [\App\Http\Controllers\Reports\ReportsController::class, 'promissoryNotes'])->name('promissory_notes');
        Route::get('drop-cash', [\App\Http\Controllers\Reports\ReportsController::class, 'dropCash'])->name('drop_cash');
        Route::get('guest-ledger', [\App\Http\Controllers\Reports\ReportsController::class, 'guestLedger'])->name('guest_ledger');
        Route::get('city-ledger', [\App\Http\Controllers\Reports\ReportsController::class, 'cityLedger'])->name('city_ledger');
        Route::get('revenue-by-source', [\App\Http\Controllers\Reports\ReportsController::class, 'revenueBySource'])->name('revenue_by_source');
        Route::get('reservation-revenue', [\App\Http\Controllers\Reports\ReportsController::class, 'reservationRevenue'])->name('reservation_revenue');
        Route::get('reservation-summary', [\App\Http\Controllers\Reports\ReportsController::class, 'reservationSummary'])->name('reservation_summary');
        Route::get('reservation-details', [\App\Http\Controllers\Reports\ReportsController::class, 'reservationDetails'])->name('reservation_details');
        Route::get('expected-arrivals', [\App\Http\Controllers\Reports\ReportsController::class, 'expectedArrivals'])->name('expected_arrivals');
        Route::get('expected-departures', [\App\Http\Controllers\Reports\ReportsController::class, 'expectedDepartures'])->name('expected_departures');
        Route::get('night-audit-summary', [\App\Http\Controllers\Reports\ReportsController::class, 'nightAuditSummary'])->name('night_audit_summary');
        Route::get('night-audit-history', [\App\Http\Controllers\Reports\ReportsController::class, 'nightAuditHistory'])->name('night_audit_history');
        Route::get('housekeeping-status', [\App\Http\Controllers\Reports\ReportsController::class, 'housekeepingStatus'])->name('housekeeping_status');
        Route::get('occupancy', [\App\Http\Controllers\Reports\ReportsController::class, 'occupancy'])->name('occupancy');
        Route::get('{reportType}/print', [\App\Http\Controllers\Reports\ReportsController::class, 'printReport'])
            ->middleware(['permission:reports.print'])
            ->name('print');
    });
});

Route::get('dashboard/logs', [LogController::class, 'index'])
    ->middleware('auth', 'permission:logs.view')
    ->name('dashboard.logs.index');

Route::get('dashboard/online-reservation', [OnlineReservationController::class, 'index'])
    ->middleware('auth', 'can:reservation.view')
    ->name('dashboard.online_reservation.index');

Route::get('setup-sidebar/financial-currencies', [CurrencyController::class, 'index'])
    ->middleware('auth', 'permission:bank_account.view|payment_method.view|cash_drawer_balance.view')
    ->name('setup-sidebar.currencies.index');

Route::middleware(['auth'])->group(function () {
    Route::get('setup-sidebar/manage-product', [ProductController::class, 'index'])
        ->middleware(['permission:manage_product.view'])
        ->name('setup-sidebar.manage_product.index');

    Route::get('setup-sidebar/manage-inventory', [InventoryController::class, 'index'])
        ->middleware(['permission:manage_inventory.view'])
        ->name('setup-sidebar.manage_inventory.index');
});

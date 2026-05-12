<?php

use App\Http\Controllers\PublicEntryController;
use App\Http\Controllers\Lang\LocaleController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\PlanController as SuperAdminPlanController;
use App\Http\Controllers\SuperAdmin\TenantController as SuperAdminTenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicEntryController::class)->name('booking.home');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('public.locale.switch');

Route::prefix('admin')->name('super-admin.')->middleware(['auth', 'super.admin'])->group(function () {
    Route::get('/', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('tenants', SuperAdminTenantController::class)->except('destroy');
    Route::resource('plans', SuperAdminPlanController::class);
});

Route::get('/app', function () {
    $user = auth()->user();

    if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
        return redirect()->route('super-admin.dashboard');
    }

    return redirect()->route('program');
})->middleware(['auth', 'tenant.subscription', 'current.property'])->name('app.home');

Route::redirect('/dashboard', '/app/dashboard');
Route::redirect('/program', '/app/program');

Route::prefix('app')->middleware(['tenant.subscription', 'current.property'])->group(base_path('routes/app.php'));

require __DIR__.'/booking_website.php';

require __DIR__.'/auth.php';

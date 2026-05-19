<?php

use App\Http\Controllers\Attendance\AbsentController;
use App\Http\Controllers\Attendance\LeaveRequestController;
use App\Http\Controllers\Attendance\MarkAttendanceController;
use App\Http\Controllers\Attendance\OverTimeController;
use App\Http\Controllers\AppHomeController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Branch\BranchController;
use App\Http\Controllers\Branch\DashboardController as BranchDashboard;
use App\Http\Controllers\Branch\DepartmentController;
use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\Clients\ContractController;
use App\Http\Controllers\Company\BrandController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Company\LetterController;
use App\Http\Controllers\Company\LetterSettingController;
use App\Http\Controllers\CompanyPartners\InvestmentController;
use App\Http\Controllers\CompanyPartners\PartnerController;
use App\Http\Controllers\CompanyPartners\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Documents\CompanyDocumentController;
use App\Http\Controllers\Documents\EmployeeDocumentController;
use App\Http\Controllers\Documents\ExpiredDocumentController;
use App\Http\Controllers\Documents\VerifyDocumentController;
use App\Http\Controllers\Employee\CardController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\ProfileController;
use App\Http\Controllers\Finance\BudgetController;
use App\Http\Controllers\Finance\CommissionController;
use App\Http\Controllers\Finance\DashboardController as FinanceDashboard;
use App\Http\Controllers\Finance\ExpenseController;
use App\Http\Controllers\Finance\IncomeController;
use App\Http\Controllers\Finance\TransactionController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Marketing\AgentController;
use App\Http\Controllers\Marketing\QuotationController;
use App\Http\Controllers\Payroll\PayrollDashboardController;
use App\Http\Controllers\Payroll\PayslipController;
use App\Http\Controllers\Payroll\SalaryController;
use App\Http\Controllers\Project\ExecutiveController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\TrackerController;
use App\Http\Controllers\RoleAndPermissions\RoleController;
use App\Http\Controllers\Settings\BackupController;
use App\Http\Controllers\Settings\GeneralSettingController;
use App\Http\Controllers\Settings\PropertyFacilityController;
use App\Http\Controllers\Settings\ShiftController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\Outlets\ItemsController;
use App\Http\Controllers\ReservationController as ReservationDashboardController;
use App\Http\Controllers\Auth\RegisterTenantController;
use App\Http\Controllers\SuperAdmin\ActivityController as SuperAdminActivityController;
use App\Http\Controllers\SuperAdmin\AnalyticsController as SuperAdminAnalyticsController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\PlanController as SuperAdminPlanController;
use App\Http\Controllers\SuperAdmin\SupportController as SuperAdminSupportController;
use App\Http\Controllers\SuperAdmin\TenantController as SuperAdminTenantController;
use App\Http\Controllers\Support\TicketController as SupportTicketController;
use App\Http\Controllers\Vehicles\AccidentController;
use App\Http\Controllers\Vehicles\DriverController;
use App\Http\Controllers\Vehicles\VehicleController;
use App\Http\Controllers\Warehouse\CategoryController;
use App\Http\Controllers\Warehouse\InventoryController;
use App\Http\Controllers\Warehouse\ProductController;
use App\Http\Controllers\Warehouse\RequestController;
use App\Http\Controllers\Warehouse\RoomController;
use App\Http\Controllers\Warehouse\WarehouseController;
use Illuminate\Support\Facades\Route;

// Public Tenant Registration (no auth, main domain only)
Route::get('/register', [RegisterTenantController::class, 'showRegistrationForm'])->name('tenant.register.form');
Route::post('/register', [RegisterTenantController::class, 'register'])->name('tenant.register');

// Guest Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.submit')
        ->middleware('throttle:200,10');
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('password/reset', [ResetPasswordController::class, 'reset'])
        ->name('password.store');
});

// Tenant settings
Route::middleware(['auth', 'tenant.subscription', 'current.property', 'permission:manage_setting'])->group(function () {
    Route::get('dashboard/setting/general', [GeneralSettingController::class, 'index'])->name('dashboard.setting.general.index');
    Route::post('/settings/update', [GeneralSettingController::class, 'update'])->name('settings.update');
    Route::get('dashboard/setting/role', [RoleController::class, 'index'])->name('dashboard.setting.role.index');
    Route::get('dashboard/setting/role/create', [RoleController::class, 'create'])->name('dashboard.setting.role.create');
    Route::post('/setting/role/store', [RoleController::class, 'storeRole'])->name('dashboard.setting.role.store');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('dashboard.setting.role.edit');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('dashboard.setting.role.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('dashboard.setting.role.destroy');
    Route::get('dashboard/setting/user', [UserController::class, 'index'])->name('dashboard.setting.user.index');
    Route::post('dashboard/setting/user/store', [UserController::class, 'store'])
        ->middleware('plan.limit:max_users')
        ->name('dashboard.setting.user.store');
    Route::put('dashboard/setting/user/update/{id}', [UserController::class, 'update'])->name('dashboard.setting.user.update');
    Route::delete('dashboard/setting/user/delete', [UserController::class, 'destroy'])->name('dashboard.setting.user.delete');
});

//  Super Admin Only Routes
Route::middleware(['role:super_admin', 'auth'])->group(function () {
    Route::get('dashboard/setting/backup', [BackupController::class, 'index'])->name('dashboard.setting.backup.index');

    // SaaS Super Admin Panel
    Route::prefix('admin')->name('super-admin.')->group(function () {
        Route::get('/', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('tenants', SuperAdminTenantController::class)->except('destroy');
        Route::resource('plans', SuperAdminPlanController::class)->except('show');
        Route::get('support', [SuperAdminSupportController::class, 'index'])->name('support.index');
        Route::get('support/tickets/{ticket}', [SuperAdminSupportController::class, 'show'])->name('support.show');
        Route::post('support/tickets/{ticket}/reply', [SuperAdminSupportController::class, 'reply'])->name('support.reply');
        Route::patch('support/tickets/{ticket}/status', [SuperAdminSupportController::class, 'updateStatus'])->name('support.status');
        Route::get('support/attachments/{attachment}', [SuperAdminSupportController::class, 'download'])->name('support.attachments.download');
        Route::get('analytics', [SuperAdminAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('activity', [SuperAdminActivityController::class, 'index'])->name('activity.index');
    });
});

Route::middleware(['auth', 'tenant.subscription'])
    ->prefix('support')
    ->name('support.tickets.')
    ->group(function () {
        Route::get('/', [SupportTicketController::class, 'index'])->name('index');
        Route::get('/create', [SupportTicketController::class, 'create'])->name('create');
        Route::post('/', [SupportTicketController::class, 'store'])->name('store');
        Route::get('/attachments/{attachment}', [SupportTicketController::class, 'download'])->name('attachments.download');
        Route::get('/{ticket}', [SupportTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('reply');
    });

Route::middleware(['auth', 'tenant.subscription'])
    ->prefix('dashboard/support')
    ->name('dashboard.support.tickets.')
    ->group(function () {
        Route::get('/', [SupportTicketController::class, 'hrIndex'])->name('index');
        Route::get('/create', [SupportTicketController::class, 'hrCreate'])->name('create');
        Route::post('/', [SupportTicketController::class, 'hrStore'])->name('store');
        Route::get('/attachments/{attachment}', [SupportTicketController::class, 'download'])->name('attachments.download');
        Route::get('/{ticket}', [SupportTicketController::class, 'hrShow'])->name('show');
        Route::post('/{ticket}/reply', [SupportTicketController::class, 'hrReply'])->name('reply');
    });

// Permission-Based Routes
Route::middleware([
    'auth',
    'tenant.subscription',
    'current.property',
    'permission:manage_employee',
])->group(function () {
    Route::post('/dashboard/employee/check-email', [EmployeeController::class, 'checkEmail']);
    Route::post('/dashboard/employee/check-image', [EmployeeController::class, 'checkImage']);
    Route::post('/dashboard/employee/check-phone', [EmployeeController::class, 'checkPhone']);

    Route::get('dashboard/employees', [EmployeeController::class, 'index'])->name('dashboard.employee.index');
    Route::get('/employees/filter', [EmployeeController::class, 'multipleFilter'])->name('employees.filter');
    Route::get('/employee/filter', [EmployeeController::class, 'filter'])->name('employee.filter');
    Route::get('/dashboard/income-chart', [DashboardController::class, 'incomeChartData'])
        ->name('dashboard.income.chart');
    // Shifts
    Route::post('dashboard/shift', [ShiftController::class, 'store'])->name('dashboard.shift.store');
    Route::get('dashboard/shifts', [ShiftController::class, 'index'])->name('dashboard.shift.index');
    Route::put('/shift/update', [ShiftController::class, 'update'])->name('dashboard.shift.update');
    Route::delete('dashboard/shift/{id}', [ShiftController::class, 'destroy'])->name('shift.destroy');

    // Employee
    Route::get('dashboard/employee/create', [EmployeeController::class, 'create'])->name('dashboard.employee.create');
    Route::get('dashboard/profile/{id}', [ProfileController::class, 'index'])->name('dashboard.employee.profile.index');
    Route::delete('dashboard/profile/delete/', [ProfileController::class, 'destroy'])->name('dashboard.employee.profile.destroy');
    Route::post('dashboard/employee/store', [EmployeeController::class, 'store'])->name('dashboard.employee.profile.store');
    Route::get('/dashboard/employee/{id}/edit', [EmployeeController::class, 'edit'])->name('dashboard.employee.edit');
    Route::put('/dashboard/employee/{id}', [EmployeeController::class, 'update'])->name('dashboard.employee.update');
    Route::delete('/dashboard/employee/destroy', [EmployeeController::class, 'destroy'])->name('dashboard.employee.destroy');
    Route::get('/dashboard/employee/view/{id}', [EmployeeController::class, 'show'])->name('dashboard.employee.show');
    Route::get('/get-department/{branch}', [EmployeeController::class, 'getDepartments']);
    Route::get('/dashboard/employee/{employee}/details',
        [CompanyController::class, 'getEmployeeDetails']);
    Route::post('/company/report/pdf', [CompanyController::class, 'downloadPdf'])
        ->name('company.report.pdf');
    // profile pdf download
    Route::get('/dashboard/employee/{id}/pdf',
        [ProfileController::class, 'printProfile']
    )->name('dashboard.employee.pdf');

    // ID Cards
    Route::get('dashboard/employee/card', [CardController::class, 'index'])->name('dashboard.employee.card');
    // Fetch Brands by Company
    Route::get('/dashboard/brands/{company}', [App\Http\Controllers\Employee\DropDownController::class, 'getBrands']);

    // Fetch Branches by Brand
    Route::get('/dashboard/branches/{brand}', [App\Http\Controllers\Employee\DropDownController::class, 'getBranches']);

    // Fetch Employees by Branch
    Route::get('/dashboard/employees/{branch}', [App\Http\Controllers\Employee\DropDownController::class, 'getDepartments']);
    Route::get('/dashboard/branches/{branch}/shifts', [App\Http\Controllers\Employee\DropDownController::class, 'getShifts']);
});
Route::middleware([
    'auth',
    'tenant.subscription',
    'current.property',
    'permission:manage_branch',
])->group(function () {

    // Company routes
    Route::get('dashboard/company/index', [CompanyController::class, 'index'])->name('dashboard.company.index');
    Route::post('/dashboard/company/store', [CompanyController::class, 'store'])
        ->name('dashboard.company.store');
    Route::post('/dashboard/company/update/{id}', [CompanyController::class, 'update'])
        ->name('dashboard.company.update');
    Route::delete('/companies/{id}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    Route::get('/dashboard/company/filter', [CompanyController::class, 'filter'])->name('dashboard.company.filter');
    Route::get('/dashboard/company/report', [CompanyController::class, 'reportView'])->name('dashboard.company.report');
    Route::get('/companies/{company}/reports', [CompanyController::class, 'reports'])
        ->name('companies.reports');
    Route::get('/dashboard/brand/{brand}/branches', [CompanyController::class, 'getBrandBranches']);
    Route::get('/dashboard/branch/{branch}/departments', [CompanyController::class, 'getBranchDepartments']);
    Route::get('/dashboard/department/{department}/employees', [CompanyController::class, 'getDepartmentEmployees']);
    Route::get('/dashboard/branch/{branch}/documents', [BranchController::class, 'getDocuments']);

    // Brand routes
    Route::get('dashboard/Brand/index', [BrandController::class, 'index'])->name('dashboard.brand.index');
    Route::post('dashboard/Brand/store', [BrandController::class, 'store'])->name('dashboard.brand.store');
    Route::put('dashboard/Brand/update', [BrandController::class, 'update'])->name('dashboard.brand.update');
    Route::delete('dashboard/Brand/delete', [BrandController::class, 'destroy'])->name('dashboard.brand.delete');
    Route::get('/get-brands/{company_id}', [BrandController::class, 'getBrands']);

    // Branch Management
    Route::get('dashboard/branch/dashboard', [BranchDashboard::class, 'index'])->name('dashboard.branch.dashboard');
    Route::get('dashboard/branch/department', [DepartmentController::class, 'index'])->name('dashboard.branch.department.index');
    Route::post('dashboard/branch/department/store', [App\Http\Controllers\Branch\DepartmentController::class, 'store'])->name('dashboard.branch.department.store');
    Route::put('dashboard/branch/department/update', [App\Http\Controllers\Branch\DepartmentController::class, 'update'])->name('dashboard.branch.department.update');
    Route::delete('dashboard/branch/department/delete', [App\Http\Controllers\Branch\DepartmentController::class, 'destroy'])
        ->name('dashboard.branch.department.delete');
    Route::get('/dashboard/departments/filter', [DepartmentController::class, 'filter'])->name('departments.filter');

    // Branch
    Route::get('dashboard/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::post('dashboard/store', [BranchController::class, 'store'])->name('dashboard.branch.store');
    Route::get('dashboard/list', [BranchController::class, 'index'])->name('dashboard.branch.index');
    Route::put('dashboard/update', [BranchController::class, 'update'])->name('dashboard.branch.update');
    Route::delete('dashboard/branch/delete', [BranchController::class, 'destroy'])->name('dashboard.branch.delete');
    Route::get('/dashboard/filter/branches', [BranchController::class, 'filter'])->name('branches.filter');

    // Company Letters
    Route::get('dashboard/company/letters', [LetterController::class, 'index'])->name('dashboard.company.letter.index');
    Route::post('dashboard/company/letter/store', [LetterController::class, 'store'])->name('dashboard.letters.store');
    Route::put('/dashboard/company/letter/update/{letter}', [LetterController::class, 'update'])->name('dashboard.letters.update');
    Route::delete('/dashboard/company/letter/delete/{id}', [LetterController::class, 'destroy'])
        ->name('dashboard.letters.delete');
    // Download PDF
    Route::get('/letters/{letter}/download', [LetterController::class, 'download'])->name('letters.download');

    // Letter Setting
    Route::get('dashboard/letters/setting', [LetterSettingController::class, 'index'])->name('dashboard.letter.setting.index');
    Route::post('dashboard/letters/store', [LetterSettingController::class, 'store'])->name('dashboard.letter.setting.store');
    Route::put('/dashboard/letter-setting/update/{id}', [LetterSettingController::class, 'update'])->name('dashboard.letter.setting.update');
    Route::delete('dashboard/letter/setting/delete/{letterSetting}', [LetterSettingController::class, 'destroy'])->name('dashboard.letter.setting.delete');

    // Marketing Agent
    Route::get('dashboard/company/marketing', [AgentController::class, 'index'])->name('dashboard.company.agent.index');
    Route::post('dashboard/company/marketing/store', [AgentController::class, 'store'])->name('marketing-agents.store');
    Route::put('/dashboard/company/marketing/update/{marketingAgent}', [AgentController::class, 'update'])->name('marketing-agents.update');
    Route::delete('/dashboard/company/marketing/delete/{marketingAgent}', [AgentController::class, 'destroy'])
        ->name('marketing-agents.destroy');

    // Marketing Quotations
    Route::get('dashboard/company/quotation', [QuotationController::class, 'index'])->name('dashboard.company.quotation.index');
    Route::post('dashboard/company/quotation/store', [QuotationController::class, 'store'])->name('marketing-quotations.store');
    Route::put('/dashboard/company/quotation/update/{marketingQuotation}', [QuotationController::class, 'update'])->name('marketing-quotations.update');
    Route::delete('/dashboard/company/quotation/delete/{marketingQuotation}', [QuotationController::class, 'destroy'])
        ->name('marketing-quotations.destroy');
    Route::get('/marketing-quotations/{quotation}/print', [QuotationController::class, 'print'])->name('marketing-quotations.print');

    // Marketing Commission
    Route::get('dashboard/company/commission', [App\Http\Controllers\Marketing\CommissionController::class, 'index'])->name('dashboard.company.commission.index');

    // Subscription platform
    Route::get('dashboard/company/subscription/platform', [App\Http\Controllers\Subscriptions\PlatformController::class, 'index'])->name('dashboard.company.platform.index');
    Route::post('dashboard/company/subscription/platform', [App\Http\Controllers\Subscriptions\PlatformController::class, 'store'])->name('third-party-platforms.store');
    Route::put('/dashboard/company/subscription/update/{platform}', [App\Http\Controllers\Subscriptions\PlatformController::class, 'update'])->name('third-party-platforms.update');
    Route::delete('/dashboard/company/subscription/delete/{platform}', [App\Http\Controllers\Subscriptions\PlatformController::class, 'destroy'])
        ->name('third-party-platforms.destroy');

    // Subscription
    Route::get('dashboard/company/subscription', [App\Http\Controllers\Subscriptions\SubscriptionController::class, 'index'])->name('dashboard.company.subscription.index');
    Route::post('dashboard/company/subscription/store', [App\Http\Controllers\Subscriptions\SubscriptionController::class, 'store'])->name('platform-subscriptions.store');
    Route::put('/dashboard/company/sub/update/{subscription}', [App\Http\Controllers\Subscriptions\SubscriptionController::class, 'update'])->name('platform-subscriptions.update');
    Route::delete('/dashboard/company/sub/delete/{subscription}', [App\Http\Controllers\Subscriptions\SubscriptionController::class, 'destroy'])
        ->name('platform-subscriptions.destroy');
    Route::get('/platform-subscriptions/filter', [App\Http\Controllers\Subscriptions\SubscriptionController::class, 'filter'])
        ->name('platform-subscriptions.filter');

    // Subscription Revenue
    Route::get('dashboard/company/revenue', [App\Http\Controllers\Subscriptions\RevenueController::class, 'index'])->name('dashboard.company.revenue.index');
    Route::post('dashboard/company/revenue/store', [App\Http\Controllers\Subscriptions\RevenueController::class, 'store'])->name('platform-revenues.store');
    Route::put('/dashboard/company/revenue/update/{revenue}', [App\Http\Controllers\Subscriptions\RevenueController::class, 'update'])->name('platform-revenues.update');
    Route::delete('/dashboard/company/revenue/delete/{revenue}', [App\Http\Controllers\Subscriptions\RevenueController::class, 'destroy'])
        ->name('platform-revenues.destroy');

    // Client Management
    Route::get('dashboard/company/clients', [ClientController::class, 'index'])->name('dashboard.company.client.index');
    Route::post('dashboard/company/client-store', [ClientController::class, 'store'])->name('dashboard.company.client.store');
    Route::put('/dashboard/company/client-update/{client}', [ClientController::class, 'update'])->name('dashboard.company.client.update');
    Route::delete('/dashboard/company/client-delete/{client}', [ClientController::class, 'destroy'])
        ->name('dashboard.company.client.destroy');

    // contract Management
    Route::get('dashboard/company/clients-contract', [ContractController::class, 'index'])->name('dashboard.company.contract.index');
    Route::post('dashboard/company/client-contract-store', [ContractController::class, 'store'])->name('dashboard.company.contract.store');
    Route::put('/dashboard/company/client-contract-update/{id}', [ContractController::class, 'update'])->name('dashboard.company.contract.update');
    Route::delete('/dashboard/company/client-contract-delete/{id}', [ContractController::class, 'destroy'])
        ->name('dashboard.company.contract.destroy');
    Route::get('/dashboard/company/client-contract-print/{contract}', [ContractController::class, 'print'])
        ->name('dashboard.company.contract.print');

    // vehicle Management
    Route::get('dashboard/company/vehicles', [VehicleController::class, 'index'])->name('dashboard.company.vehicle.index');
    Route::post('dashboard/company/vehicle-store', [VehicleController::class, 'store'])->name('dashboard.company.vehicle.store');
    Route::put('/dashboard/company/vehicle-update/{vehicle}', [VehicleController::class, 'update'])->name('dashboard.company.vehicle.update');
    Route::delete('/dashboard/company/vehicle-delete/{vehicle}', [VehicleController::class, 'destroy'])
        ->name('dashboard.company.vehicle.destroy');

    // driver Management
    Route::get('dashboard/company/driver', [DriverController::class, 'index'])->name('dashboard.company.driver.index');
    Route::post('dashboard/company/driver-store', [DriverController::class, 'store'])->name('dashboard.company.driver.store');
    Route::put('/dashboard/company/driver-update/{driver}', [DriverController::class, 'update'])->name('dashboard.company.driver.update');
    Route::delete('/dashboard/company/driver-delete/{driver}', [DriverController::class, 'destroy'])
        ->name('dashboard.company.driver.destroy');

    // accident Management
    Route::get('dashboard/company/accident', [AccidentController::class, 'index'])->name('dashboard.company.accident.index');
    Route::post('dashboard/company/accident-store', [AccidentController::class, 'store'])->name('dashboard.company.accident.store');
    Route::put('/dashboard/company/accident-update/{accident}', [AccidentController::class, 'update'])->name('dashboard.company.accident.update');
    Route::delete('/dashboard/company/accident-delete/{accident}', [AccidentController::class, 'destroy'])
        ->name('dashboard.company.accident.destroy');

    // project Management
    Route::get('dashboard/company/projects', [ProjectController::class, 'index'])->name('dashboard.company.project.index');
    Route::post('dashboard/company/project-store', [ProjectController::class, 'store'])->name('dashboard.company.project.store');
    Route::put('/dashboard/company/project-update/{project}', [ProjectController::class, 'update'])->name('dashboard.company.project.update');
    Route::delete('/dashboard/company/project-delete/{id}', [ProjectController::class, 'destroy'])
        ->name('dashboard.company.project.destroy');

    // project-executive
    Route::get('dashboard/company/projects-executive', [ExecutiveController::class, 'index'])->name('dashboard.company.executive.index');
    Route::post('dashboard/company/project-executive-store', [ExecutiveController::class, 'store'])->name('dashboard.company.executive.store');
    Route::put('/dashboard/company/project-executive-update/{executive}', [ExecutiveController::class, 'update'])->name('dashboard.company.executive.update');
    Route::delete('/dashboard/company/project-executive-delete/{id}', [ExecutiveController::class, 'destroy'])
        ->name('dashboard.company.executive.destroy');

    // project-executive
    Route::get('dashboard/company/projects-tracker', [TrackerController::class, 'index'])->name('dashboard.company.tracker.index');
    Route::post('dashboard/company/project-tracker-store', [TrackerController::class, 'store'])->name('dashboard.company.tracker.store');
    Route::put('/dashboard/company/project-tracker-update/{tracker}', [TrackerController::class, 'update'])->name('dashboard.company.tracker.update');

    // project-executive
    Route::get('dashboard/company/projects-expense', [App\Http\Controllers\Project\ExpenseController::class, 'index'])->name('dashboard.company.expense.index');
    Route::post('dashboard/company/project-expense-store', [App\Http\Controllers\Project\ExpenseController::class, 'store'])->name('dashboard.company.expense.store');
    Route::put('/dashboard/company/project-expense-update/{id}', [App\Http\Controllers\Project\ExpenseController::class, 'update'])->name('dashboard.company.expense.update');
    Route::delete('/dashboard/company/project-expense/{id}', [App\Http\Controllers\Project\ExpenseController::class, 'destroy'])
        ->name('dashboard.company.expense.destroy');
});

Route::middleware([
    'auth',
    'tenant.subscription',
    'current.property',
    'permission:manage_attendance',
])->group(function () {
    // Attendance
    Route::get('dashboard/attendance', [MarkAttendanceController::class, 'index'])->name('dashboard.employee.attendance.index');
    Route::get('dashboard/attendance/dashboard', [App\Http\Controllers\Attendance\DashboardController::class, 'index'])->name('dashboard.employee.attendance.dashboard');
    Route::get('dashboard/attendance/absent', [AbsentController::class, 'index'])->name('dashboard.employee.absence.index');
    Route::post('dashboard/attendance/store', [MarkAttendanceController::class, 'store'])->name('dashboard.employee.attendance.store');
    Route::put('/dashboard/employee/attendance/update', [MarkAttendanceController::class, 'update'])
        ->name('dashboard.employee.attendance.update');
    Route::delete('/dashboard/employee/attendance/{id}', [MarkAttendanceController::class, 'destroy'])
        ->name('dashboard.employee.attendance.destroy');
    Route::get('/attendance/filter', [MarkAttendanceController::class, 'filter'])->name('attendance.filter.ajax');

    Route::get('dashboard/attendance/fetch', [MarkAttendanceController::class, 'fetch'])->name('attendance.fetch');
    // Leave & Overtime
    Route::get('dashboard/leaves', [LeaveRequestController::class, 'index'])->name('dashboard.employee.leave.index');
    Route::post('dashboard/leaves/store', [LeaveRequestController::class, 'store'])->name('dashboard.leaves.store');
    Route::put('dashboard/leaves/update/', [LeaveRequestController::class, 'update'])
        ->name('dashboard.leaves.update');
    Route::delete('/dashboard/leaves/{id}/delete', [LeaveRequestController::class, 'destroy'])
        ->name('dashboard.leaves.destroy');
    Route::get('dashboard/overtime', [OverTimeController::class, 'index'])->name('dashboard.employee.overtime.index');
    Route::get('leaves/filter', [LeaveRequestController::class, 'filterAjax'])->name('leaves.filter.ajax');
});

Route::middleware([
    'auth',
    'tenant.subscription',
    'current.property',
    'permission:manage_payroll',
])->group(function () {

    // Payroll
    Route::get('dashboard/employee/payroll', [PayrollDashboardController::class, 'PayrollDashboard'])->name('dashboard.employee.payroll.dashboard');
    Route::get('dashboard/payroll/payslip', [PayslipController::class, 'index'])->name('dashboard.payroll.payslip');
    Route::post('dashboard/payroll/payslip/store', [PayslipController::class, 'store'])->name('dashboard.payroll.payslip.store');
    Route::put('dashboard/payroll/payslip/update/{id}', [PayslipController::class, 'update'])->name('dashboard.payroll.payslip.update');
    Route::delete('dashboard/payroll/payslip/delete/{id}', [PayslipController::class, 'destroy'])->name('dashboard.payroll.payslip.delete');
    Route::get('dashboard/payroll/create', [PayslipController::class, 'create'])->name('dashboard.payroll.payslip.create');
    Route::get('dashboard/payroll/edit', [PayslipController::class, 'edit'])->name('dashboard.payroll.payslip.edit');
    Route::get('dashboard/payroll/salary', [SalaryController::class, 'index'])->name('dashboard.payroll.salary.index');
    Route::get('/dashboard/payroll/payslip/download/{id}', [SalaryController::class, 'downloadSlip'])
        ->name('dashboard.payroll.payslip.download');
    Route::get('payrolls/filter', [PayrollDashboardController::class, 'filterPayrolls'])
        ->name('payrolls.filter');
    Route::get('payrolls/salary/filter', [SalaryController::class, 'filterPayrolls'])
        ->name('payrolls.salary.filter');
    Route::get('dashboard/payroll/get-employee-data/{employee}/{month}', [PayslipController::class, 'getEmployeePayrollData'])
        ->name('payroll.getEmployeeData');
    Route::get('dashboard/branch-salaries', [SalaryController::class, 'salaryView'])->name('dashboard.payroll.salary');
    Route::get('branch-salaries/', [SalaryController::class, 'branchSalaries'])->name('finance.branch-salaries.view');
    Route::post('branch-salaries/pay', [SalaryController::class, 'payBranchSalaries'])->name('finance.branch-salaries.pay');

    Route::get('/branches/{branch}/departments', [EmployeeController::class, 'getDepartments']);
});

Route::middleware([
    'auth',
    'tenant.subscription',
    'current.property',
    'permission:manage_finance',
])->group(function () {
    // Finance
    Route::get('dashboard/finance/dashboard', [FinanceDashboard::class, 'index'])->name('dashboard.finance.index');
    Route::get('/transactions/filter', [TransactionController::class, 'filter'])->name('transactions.filter');
    Route::get('dashboard/finance/transaction', [TransactionController::class, 'index'])->name('dashboard.finance.transaction.index');
    Route::post('dashboard/finance/transactions/store', [TransactionController::class, 'store'])
        ->name('dashboard.finance.transaction.store');
    Route::get('dashboard/finance/income/', [IncomeController::class, 'index'])
        ->name('dashboard.finance.income.index');
    Route::post('dashboard/finance/income/store', [IncomeController::class, 'store'])
        ->name('dashboard.finance.income.store');
    Route::put('dashboard/finance/income/update/{id}', [IncomeController::class, 'update'])->name('dashboard.finance.income.update');
    Route::delete('dashboard/finance/income/delete/{id}', [IncomeController::class, 'destroy'])
        ->name('dashboard.finance.income.destroy');
    Route::get('/incomes/filter', [IncomeController::class, 'filter'])->name('income.filter');

    Route::get('/finance/reports/commission', [CommissionController::class, 'commissionReport'])
        ->name('dashboard.finance.commission.index');

    Route::get('/finance/reports/commission/export-excel', [CommissionController::class, 'exportCommissionExcel'])
        ->name('finance.reports.commission.excel');

    Route::get('/finance/reports/commission/export-pdf', [CommissionController::class, 'exportCommissionPDF'])
        ->name('finance.reports.commission.pdf');
    Route::get('finance-report', [FinanceDashboard::class, 'report'])->name('dashboard.finance.report');
    Route::get('/finance-report/branch-data', [FinanceDashboard::class, 'branchFinanceData'])->name('finance.branch.data');
    Route::get('finance/branch/report/pdf', [FinanceDashboard::class, 'branchReportPdf'])
        ->name('finance.branch.report.pdf');

    // fetch single transaction
    Route::get('/dashboard/finance/transactions/{id}', [TransactionController::class, 'show'])
        ->name('dashboard.finance.transaction.show');

    // update transaction
    Route::put('dashboard/finance/transactions/update/{id}', [TransactionController::class, 'update'])
        ->name('dashboard.finance.transaction.update');
    Route::delete('/dashboard/finance/transactions/delete/{id}', [TransactionController::class, 'destroy'])
        ->name('dashboard.finance.transaction.delete');

    Route::get('dashboard/finance/budget', [BudgetController::class, 'index'])->name('dashboard.finance.budget.index');
    Route::post('dashboard/finance/budget/store', [BudgetController::class, 'store'])->name('dashboard.finance.budget.store');
    Route::get('dashboard/finance/budget/{id}/edit', [BudgetController::class, 'edit']);
    Route::put('dashboard/finance/budget/{id}', [BudgetController::class, 'update']);
    Route::delete('/dashboard/finance/budget/delete/{id}', [BudgetController::class, 'destroy'])->name('dashboard.finance.budget.delete');
    Route::get('/budgets/filter', [BudgetController::class, 'filter'])->name('budgets.filter');

    Route::get('dashboard/finance/expense', [ExpenseController::class, 'index'])->name('dashboard.finance.expense.index');
    Route::post('dashboard/finance/expense/store', [ExpenseController::class, 'store'])->name('dashboard.finance.expense.store');
    Route::put('/dashboard/finance/expense/update', [ExpenseController::class, 'update'])->name('dashboard.finance.expense.update');
    Route::delete('/dashboard/finance/expense/delete/{id}', [ExpenseController::class, 'destroy'])
        ->name('dashboard.finance.expense.delete');
    Route::get('/expenses/filter', [ExpenseController::class, 'filter'])->name('expenses.filter');

    // Partners
    Route::get('dashboard/finance/partner/', [PartnerController::class, 'index'])->name('dashboard.finance.partner.index');
    Route::post('dashboard/finance/partner/store', [PartnerController::class, 'store'])->name('dashboard.finance.partner.store');
    Route::put('/dashboard/finance/partner/update/{id}', [PartnerController::class, 'update'])->name('dashboard.finance.partner.update');
    Route::delete('/dashboard/finance/partner/delete', [PartnerController::class, 'destroy'])
        ->name('dashboard.finance.partner.delete');

    // Investments
    Route::get('dashboard/finance/investment', [InvestmentController::class, 'index'])->name('dashboard.finance.investment.index');

    // Reports
    Route::get('dashboard/finance/partner/report', [ReportController::class, 'index'])->name('dashboard.finance.partner.report');
    Route::get('dashboard/finance/partner/reportView/{id}', [ReportController::class, 'report'])->name('dashboard.finance.partner.reportView');
    Route::get('/dashboard/finance/partner/{partner}/pdf', [ReportController::class, 'reportPdf'])
        ->name('partners.download.pdf');

});

Route::middleware([
    'auth',
    'tenant.subscription',
    'current.property',
    'permission:manage_documents',
])->group(function () {
    // Documents
    Route::get('dashboard/document/employee', [EmployeeDocumentController::class, 'index'])->name('dashboard.document.employee.index');
    Route::post('dashboard/document/employee/store', [EmployeeDocumentController::class, 'store'])->name('dashboard.document.employee.store');
    Route::post('/dashboard/document/employee/update/{id}', [EmployeeDocumentController::class, 'update'])->name('employee-doc.update');
    Route::delete('/dashboard/document/employee/delete/{id}', [EmployeeDocumentController::class, 'destroy']);
    Route::get('/dashboard/document/employee/{document}/file', [EmployeeDocumentController::class, 'file'])
        ->name('dashboard.document.employee.file');
    Route::get('/dashboard/document/employee/{document}/image', [EmployeeDocumentController::class, 'image'])
        ->name('dashboard.document.employee.image');
    Route::get('/expiration-documents', [ExpiredDocumentController::class, 'filteredDocuments'])
        ->name('dashboard.expiration.filtered');

    Route::get('dashboard/document/company', [CompanyDocumentController::class, 'index'])->name('dashboard.document.company.index');
    Route::post('dashboard/document/company/store', [CompanyDocumentController::class, 'store'])->name('dashboard.document.company.store');
    Route::post('/dashboard/document/company/{id}', [CompanyDocumentController::class, 'update'])->name('dashboard.document.company.update');
    Route::delete('/dashboard/document/company/{id}', [CompanyDocumentController::class, 'destroy'])
        ->name('dashboard.document.company.destroy');
    Route::get('/employee-docs/filter', [EmployeeDocumentController::class, 'filter'])
        ->name('employeeDocs.filter');
    Route::get('/dashboard/company-documents/filter', [CompanyDocumentController::class, 'filter'])
        ->name('company.documents.filter');

    Route::get('dashboard/document/expire', [ExpiredDocumentController::class, 'index'])->name('dashboard.document.expired.index');
    Route::get('dashboard/document/verify', [VerifyDocumentController::class, 'index'])->name('dashboard.document.verify.index');
});
Route::middleware([
    'auth',
    'tenant.subscription',
    'current.property',
    'permission:manage_notification',
])->group(function () {
    // Notifications
    Route::get('dashboard/notifications/dashboard', [App\Http\Controllers\Notifications\DashboardController::class, 'index'])->name('dashboard.notification.dashboard');
    Route::get('dashboard/notification/custom', [App\Http\Controllers\Notifications\CustomNotificationController::class, 'index'])->name('dashboard.notification.custom.index');
    Route::post('/notifications', [App\Http\Controllers\Notifications\DashboardController::class, 'store']); // admin

    Route::put('/notifications/{id}', [App\Http\Controllers\Notifications\DashboardController::class, 'update'])->name('notifications.update');
    Route::delete('/notifications/{id}', [App\Http\Controllers\Notifications\DashboardController::class, 'destroy']);
    Route::get('/notifications/filter', [App\Http\Controllers\Notifications\DashboardController::class, 'filter'])->name('notifications.filter');
});

Route::get('locale/{locale}', [\App\Http\Controllers\Lang\LocaleController::class, 'switch'])->name('locale.switch');
Route::middleware([
    'auth',
    'tenant.subscription',
    'current.property',
    'permission:manage_warehouse',
])->group(function () {

    // Warehouse
    Route::post('dashboard/warehouse', [WarehouseController::class, 'store'])->name('dashboard.warehouse.store');
    Route::get('dashboard/warehouse', [WarehouseController::class, 'index'])->name('dashboard.warehouse.index');
    Route::put('/warehouse/update', [WarehouseController::class, 'update'])->name('dashboard.warehouse.update');
    Route::delete('dashboard/warehouse/', [WarehouseController::class, 'destroy'])->name('dashboard.warehouse.delete');

    // Rooms
    Route::post('dashboard/room', [RoomController::class, 'store'])->name('dashboard.room.store');
    Route::get('dashboard/room', [RoomController::class, 'index'])->name('dashboard.room.index');
    Route::put('/room/update', [RoomController::class, 'update'])->name('dashboard.room.update');
    Route::delete('dashboard/room/', [RoomController::class, 'destroy'])->name('dashboard.room.delete');

    // Category
    Route::post('dashboard/category/store', [CategoryController::class, 'store'])->name('dashboard.category.store');
    Route::get('dashboard/category', [CategoryController::class, 'index'])->name('dashboard.category.index');
    Route::put('/category/update', [CategoryController::class, 'update'])->name('dashboard.category.update');
    Route::delete('dashboard/category/', [CategoryController::class, 'destroy'])->name('dashboard.category.delete');

    // Products
    Route::post('dashboard/product/store', [ProductController::class, 'store'])->name('dashboard.products.store');
    Route::get('dashboard/product', [ProductController::class, 'index'])->name('dashboard.product.index');
    Route::put('/product/update', [ProductController::class, 'update'])->name('dashboard.products.update');
    Route::delete('dashboard/product/', [ProductController::class, 'destroy'])->name('dashboard.products.delete');

    // inventory
    Route::post('dashboard/inventory/store', [InventoryController::class, 'store'])->name('dashboard.inventories.store');
    Route::get('dashboard/inventory', [InventoryController::class, 'index'])->name('dashboard.inventory.index');
    Route::put('/inventories/update', [InventoryController::class, 'update'])->name('dashboard.inventories.update');
    Route::delete('dashboard/inventories/', [InventoryController::class, 'destroy'])->name('dashboard.inventories.delete');

    // requests
    Route::post('dashboard/request/store', [RequestController::class, 'store'])->name('dashboard.requests.store');
    Route::get('dashboard/request', [RequestController::class, 'index'])->name('dashboard.requests.index');
    Route::post('/request/{id}/approve', [RequestController::class, 'approve'])->name('requests.approve');
    Route::post('/request/{id}/dispatch', [RequestController::class, 'dispatch'])->name('requests.dispatch');
    Route::get('/dashboard/warehouses/{warehouse}/report', [WarehouseController::class, 'report']
    )->name('dashboard.warehouses.report');
    Route::get('/warehouse-requests/{id}/print', [RequestController::class, 'print'])->name('warehouse-request.print');

});
Route::middleware(['auth', 'tenant.subscription', 'current.property'])
    ->get('/notifications/unread', [App\Http\Controllers\Notifications\DashboardController::class, 'unreadForCurrentUser']);

// Authenticated Dashboard Routes
Route::middleware(['auth', 'tenant.subscription', 'current.property'])->prefix('dashboard')->name('dashboard.')->group(function () {
    //
});

// Logout (needed at root level for standard route('logout'))
Route::post('dashboard/logout', [LoginController::class, 'logout'])->name('logout');

// Email verification (Breeze compat)
Route::middleware(['auth', 'throttle:6,1'])->post('email/verification-notification', function () {
    $user = request()->user();
    if ($user && method_exists($user, 'sendEmailVerificationNotification')) {
        $user->sendEmailVerificationNotification();
    }
    return back()->with('status', 'verification-link-sent');
})->name('verification.send');

// Password update (Breeze compat)
Route::middleware('auth')->put('password', [UserController::class, 'updatePassword'])->name('password.update');

Route::middleware(['auth', 'tenant.subscription', 'current.property'])->group(function () {
    Route::get('dashboard/attendance/scan', [MarkAttendanceController::class, 'scanView'])
        ->middleware(['permission:manage_attendance'])
        ->name('employee.card.scan');

    Route::post('/attendance/scan', [MarkAttendanceController::class, 'scan'])
        ->middleware(['permission:manage_attendance'])
        ->name('attendance.scan');
    Route::get('dashboard/setting/password', [UserController::class, 'updatePasswordView'])->name('dashboard.setting.user.password');
    Route::post('/user/change-password', [UserController::class, 'updatePassword'])
        ->name('user.change-password');
    Route::get('view-all/notifications/', [App\Http\Controllers\Notifications\DashboardController::class, 'viewAll'])->name('notification.viewAll');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Notifications\DashboardController::class, 'markAllRead'])
        ->name('notifications.markAllRead');

    Route::get('dashboard/meetings/', [App\Http\Controllers\Meetings\MeetingController::class, 'index'])
        ->middleware(['permission:manage_dashboard'])
        ->name('dashboard.meetings.index');
    Route::post('dashboard/meetings/store', [App\Http\Controllers\Meetings\MeetingController::class, 'store'])
        ->middleware(['permission:manage_dashboard'])
        ->name('dashboard.meetings.store');
    Route::delete('dashboard/meetings/destroy/{meeting}', [App\Http\Controllers\Meetings\MeetingController::class, 'destroy'])
        ->middleware(['permission:manage_dashboard'])
        ->name('dashboard.meeting.destroy');

});
Route::get('/meetings/{meeting}/join', [App\Http\Controllers\Meetings\MeetingController::class, 'join'])
    ->name('meetings.join');

Route::get('dashboard/programs', [DashboardController::class, 'program'])
    ->middleware(['auth', 'tenant.subscription', 'current.property', 'permission:manage_dashboard|manage_employee|generate_card|manage_attendance|manage_payroll|manage_finance|manage_documents|manage_branch|manage_notification|manage_setting|manage_reports|manage_warehouse'])
    ->name('dashboard.program');
Route::middleware(['auth', 'verified', 'tenant.subscription', 'current.property'])->get('/home', function () {
    return view('landing');
})->name('home');

Route::get('/app', AppHomeController::class)
    ->middleware(['auth', 'tenant.subscription', 'current.property'])
    ->name('app.home');

// public routes
Route::domain(config('app.secondary_domain'))
    ->get('/', [HomeController::class, 'index'])
    ->name('frontend.home');
// Route::get('home/', [HomeController::class, 'index'])->name('frontend.home');
Route::get('/', \App\Http\Controllers\PublicEntryController::class)->name('booking.home');

Route::get('/login-redirect', function () {
    return redirect('/login');
});

Route::get('lang/{locale}', function ($locale) {

    if (! in_array($locale, ['en', 'ar'])) {
        abort(400);
    }

    session(['locale' => $locale]);

    return redirect()->back();
})->name('lang.switch');

// ===== Reservation System Routes =====
require __DIR__.'/booking_website.php';

Route::prefix('app')->middleware(['auth', 'tenant.subscription', 'current.property'])->group(function () {
    Route::get('admin/get-facilities', [PropertyFacilityController::class, 'getFacilities']);
    Route::get('outlet/{outlet}/categories', [ItemsController::class, 'getCategories']);
    Route::get('cancel-reason/{id}/penalties', [ReservationDashboardController::class, 'getPenalties']);
});

Route::prefix('reservation')->middleware(['auth', 'verified', 'tenant.subscription', 'current.property'])->group(function () {
    require __DIR__.'/reservation.php';
});

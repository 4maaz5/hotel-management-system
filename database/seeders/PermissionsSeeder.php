<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ===== HR Permissions =====
        $hrPermissions = [
            'manage_dashboard',
            'manage_employee',
            'generate_card',
            'manage_attendance',
            'manage_payroll',
            'manage_finance',
            'manage_documents',
            'manage_branch',
            'manage_notification',
            'manage_setting',
            'manage_reports',
            'manage_warehouse',
        ];

        // ===== Reservation Module Permissions =====
        $reservationModules = [
            'dashboard' => ['view'],
            'property' => ['view', 'add', 'edit', 'delete'],
            'property_info' => ['view', 'edit'],
            'user' => ['view', 'add', 'edit', 'delete', 'assign_outlet'],
            'role' => ['view', 'add', 'edit', 'delete', 'copy'],
            'block' => ['view', 'add', 'edit', 'delete'],
            'floor' => ['view', 'add', 'edit', 'delete'],
            'type' => ['view', 'add', 'edit', 'delete'],
            'type_customization' => ['view', 'add', 'edit', 'delete'],
            'amenity' => ['view', 'add', 'edit', 'delete', 'copy'],
            'unit' => ['view', 'add', 'edit', 'delete'],
            'unit_status' => ['view'],
            'merge_setting' => ['view', 'add', 'delete'],
            'base_rate' => ['edit'],
            'custom_rate' => ['add', 'edit', 'delete'],
            'seasonal_rate' => ['view', 'add', 'edit', 'delete'],
            'seasonal_custom_rate' => ['add', 'delete'],
            'special_rate' => ['add', 'edit', 'view', 'delete'],
            'rate_plan' => ['add', 'edit', 'view', 'delete'],
            'bank_account' => ['add', 'edit', 'view', 'delete'],
            'cost_center' => ['view', 'add', 'edit', 'delete'],
            'security_deposit' => ['update'],
            'tax_customization' => ['add', 'edit', 'view', 'delete'],
            'payment_method' => ['add', 'edit', 'view'],
            'discount_type' => ['add', 'edit', 'view'],
            'date_setting' => ['edit'],
            'reservation_source' => ['add', 'edit', 'view', 'delete'],
            'guest_class' => ['add', 'edit', 'view', 'delete'],
            'loyalty_setting' => ['add', 'edit', 'view', 'delete'],
            'property_facility' => ['add', 'edit', 'view', 'delete'],
            'numbering_option' => ['edit'],
            'printing_option' => ['edit'],
            'outlet_setup' => ['add', 'edit', 'view', 'delete'],
            'item_categories' => ['view', 'add', 'edit', 'delete'],
            'outlet_item' => ['add', 'edit', 'view', 'delete'],
            'terms_and_condition' => ['add', 'edit', 'delete'],
            'penalties' => ['add', 'edit', 'delete'],
            'theme_customization' => ['edit'],
            'cancel_reason' => ['add', 'edit', 'delete'],
            'setup_reservation' => ['edit'],
            'unit_reason' => ['add', 'edit', 'delete'],
            'feedback_metric' => ['add', 'edit', 'delete'],
            'night_audit' => ['edit', 'start', 'download', 'delete'],
            'housekeeper_list' => ['add', 'edit', 'view', 'delete'],
            'housekeeper_task' => ['add', 'edit', 'delete', 'status', 'view'],
            'housekeeping_task' => ['add', 'edit', 'delete', 'view'],
            'guest' => ['add', 'edit', 'delete', 'view'],
            'corporate' => ['add', 'edit', 'delete', 'view'],
            'vendor' => ['add', 'edit', 'delete', 'view'],
            'reservation' => ['add', 'view', 'edit', 'cancel', 'contract'],
            'invoice' => ['view', 'edit', 'print', 'email'],
            'receipt' => ['add', 'edit', 'print', 'cancel', 'view'],
            'payment' => ['add', 'edit', 'print', 'cancel', 'view'],
            'promissory_note' => ['add', 'edit', 'print', 'cancel', 'link', 'collect', 'view'],
            'credit_notes' => ['print', 'whatsapp', 'sms'],
            'drop_cash' => ['add', 'edit', 'view', 'delete', 'print'],
            'reports' => ['view', 'print'],
            'sms' => ['send'],
            'cash_drawer_balance' => ['view'],
            'logs' => ['view'],
            'shomoos_setting' => ['view'],
            'ntmp_setting' => ['view'],
            'staff_attendance' => ['view'],
            'task_type' => ['view'],
            'manage_product' => ['view'],
            'manage_inventory' => ['view'],
            'website_setting' => ['view'],
            'website_page' => ['view'],
            'website_faq' => ['view'],
            'subscription' => ['view'],
            'guest_feedback' => ['view'],
        ];

        // Build flat list of reservation permission names
        $reservationPermissions = [];
        foreach ($reservationModules as $module => $actions) {
            foreach ($actions as $action) {
                $reservationPermissions[] = "{$module}.{$action}";
            }
        }

        $allPermissions = array_merge($hrPermissions, $reservationPermissions);

        $existingPermissionCount = Permission::where('guard_name', 'web')->count();
        $fullAccessRoleIds = $existingPermissionCount > 0
            ? Role::where('guard_name', 'web')
                ->withCount('permissions')
                ->get()
                ->filter(fn ($role) => $role->permissions_count === $existingPermissionCount)
                ->pluck('id')
                ->all()
            : [];

        // Create Roles (HR roles)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $employee = Role::firstOrCreate(['name' => 'employee']);

        // Create Reservation roles
        $administrator = Role::firstOrCreate(['name' => 'Administrator', 'guard_name' => 'web']);
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'housekeeping', 'guard_name' => 'web']);

        // Create all permissions
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Assign ALL permissions to full-access roles so newly added tabs do not stay hidden.
        $superAdmin->syncPermissions(Permission::all());
        $administrator->syncPermissions(Permission::all());
        $owner->syncPermissions(Permission::all());

        Role::whereIn('id', $fullAccessRoleIds)->get()
            ->each(fn (Role $role) => $role->syncPermissions(Permission::all()));

        // Manager → No permissions (via HR)
        $manager->syncPermissions([]);

        // Employee → No permissions
        $employee->syncPermissions([]);

        // Create Super Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin@123'),
                'role' => 'super_admin',
            ]
        );

        // Assign role if not already assigned
        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        // Clear cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('Roles and permissions updated successfully!');
    }
}

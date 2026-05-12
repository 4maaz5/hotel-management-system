<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;

require_once __DIR__.'/../app/Helpers/date_time_helper.php';

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'redirect.unauthorized' => \App\Http\Middleware\RedirectIfUnauthorized::class,
            // Reservation system middleware
            'tenant.subscription' => \App\Http\Middleware\CheckTenantSubscription::class,
            'plan.feature' => \App\Http\Middleware\CheckPlanFeature::class,
            'plan.limit' => \App\Http\Middleware\CheckPlanLimit::class,
            'super.admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'current.property' => \App\Http\Middleware\SetCurrentProperty::class,
            'mobile.auth' => \App\Http\Middleware\AuthenticateMobileApi::class,
            'booking.tenant' => \App\Http\Middleware\ResolveBookingTenant::class,
        ]);
        $middleware->web([
            \App\Http\Middleware\IdentifyTenantByDomain::class,
            \App\Http\Middleware\RedirectIfUnauthorized::class,
            // \App\Http\Middleware\CheckExpiringDocuments::class,
            \App\Http\Middleware\UserSetLocale::class,
        ]);

    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

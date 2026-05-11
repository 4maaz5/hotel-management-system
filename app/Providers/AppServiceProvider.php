<?php

namespace App\Providers;

use App\Http\Middleware\CheckExpiringDocuments;
use App\Models\CompanyDocument;
use App\Models\EmployeeDocument;
use App\Models\StockRequest;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);
        $this->app->singleton(PropertyContext::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::macro('systemFormat', function () {
            return $this->format(system_date_format().' '.system_time_format());
        });

        Paginator::useBootstrapFive();

        URL::forceHttps(config('app.force_https'));

        RateLimiter::for('chatbot', function (Request $request) {
            return Limit::perMinute((int) config('chatbot.max_messages_per_minute', 20))
                ->by($request->user()?->id ?: $request->ip());
        });

        // Listen to login event — check expiring documents
        Event::listen(Login::class, function ($event) {
            $today = Carbon::today();

            $checker = new CheckExpiringDocuments;

            $checker->checkDocuments(EmployeeDocument::all(), $today, 'Employee Document');
            $checker->checkDocuments(CompanyDocument::all(), $today, 'Company Document');
        });

        // View composers

        view()->composer('*', function ($view) {
            if (Auth::check() && Auth::user()->hasRole('super_admin')) {
                $pendingRequestsCount = StockRequest::where('status', 'pending')->count();
                $view->with('pendingStockRequestsCount', $pendingRequestsCount);
            }
        });

        View::composer(['layouts.header', 'layouts.navigation'], function ($view): void {
            $user = auth()->user();
            $accessibleProperties = collect();
            $currentProperty = null;

            if ($user && method_exists($user, 'accessiblePropertiesQuery') && ! $user->isSuperAdmin()) {
                $accessibleProperties = $user->accessiblePropertiesQuery()
                    ->orderBy('property_name_en')
                    ->get();
                $currentProperty = app(PropertyContext::class)->property();
            }

            $view->with(compact('accessibleProperties', 'currentProperty'));
        });
    }
}

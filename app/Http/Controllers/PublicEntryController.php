<?php

namespace App\Http\Controllers;

use App\Support\BookingContextResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicEntryController extends Controller
{
    public function __invoke(
        Request $request,
        BookingContextResolver $bookingContextResolver,
        BookingEngineController $bookingEngineController
    ): Response|RedirectResponse|View {
        if ($bookingContextResolver->resolveAndApply($request)) {
            return $bookingEngineController->home($request);
        }

        $user = $request->user();

        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        if ($user) {
            return redirect()->route('app.home');
        }

        return view('public-entry');
    }
}

<?php

use App\Http\Controllers\BookingEngineController;
use App\Http\Controllers\BookingSiteChatController;
use App\Http\Controllers\ChannelManager\WebsiteConfigureController;
use App\Http\Controllers\ChannelManager\WebsiteFaqController;
use App\Http\Controllers\ChannelManager\WebsitePageController;
use Illuminate\Support\Facades\Route;

Route::middleware('booking.tenant')->group(function () {
    Route::prefix('book')->name('booking.')->group(function () {
        Route::get('rooms', [BookingEngineController::class, 'rooms'])->name('rooms.index');
        Route::get('rooms/{roomType}', [BookingEngineController::class, 'show'])->name('rooms.show');
        Route::get('search', [BookingEngineController::class, 'search'])->name('search');
        Route::get('checkout', [BookingEngineController::class, 'checkout'])->name('checkout');
        Route::post('checkout', [BookingEngineController::class, 'store'])->name('store');
        Route::get('confirmation/{reservationNumber}', [BookingEngineController::class, 'confirmation'])->name('confirmation');
        Route::get('api/chat/session', [BookingSiteChatController::class, 'current'])
            ->name('chat.session.current');
        Route::get('api/chat/sessions/{chatSession}/messages', [BookingSiteChatController::class, 'messages'])
            ->name('chat.messages');
        Route::post('api/chat', [BookingSiteChatController::class, 'store'])
            ->middleware('throttle:chatbot')
            ->name('chat.store');
        Route::get('faq', [BookingEngineController::class, 'page'])
            ->defaults('pageKey', 'faq')
            ->name('faq');
        Route::get('contact', [BookingEngineController::class, 'page'])
            ->defaults('pageKey', 'contact')
            ->name('contact');
    });
});

Route::prefix('app')->middleware(['auth', 'tenant.subscription', 'current.property'])->group(function () {
    Route::get('setup-sidebar/website-configure', [WebsiteConfigureController::class, 'index'])
        ->name('setup-sidebar.website_configuration.index');
    Route::put('setup-sidebar/website-configure', [WebsiteConfigureController::class, 'update'])
        ->name('setup-sidebar.website_configuration.update');

    Route::get('setup-sidebar/website-pages', [WebsitePageController::class, 'index'])
        ->name('setup-sidebar.website_pages.index');
    Route::get('setup-sidebar/website-pages/{websitePage}/edit', [WebsitePageController::class, 'edit'])
        ->name('setup-sidebar.website_pages.edit');
    Route::put('setup-sidebar/website-pages/{websitePage}', [WebsitePageController::class, 'update'])
        ->name('setup-sidebar.website_pages.update');

    Route::get('setup-sidebar/website-faq', [WebsiteFaqController::class, 'index'])
        ->name('setup-sidebar.website_faq.index');
    Route::post('setup-sidebar/website-faq', [WebsiteFaqController::class, 'store'])
        ->name('setup-sidebar.website_faq.store');
    Route::get('setup-sidebar/website-faq/{websiteFaqItem}/edit', [WebsiteFaqController::class, 'edit'])
        ->name('setup-sidebar.website_faq.edit');
    Route::put('setup-sidebar/website-faq/{websiteFaqItem}', [WebsiteFaqController::class, 'update'])
        ->name('setup-sidebar.website_faq.update');
    Route::delete('setup-sidebar/website-faq/{websiteFaqItem}', [WebsiteFaqController::class, 'destroy'])
        ->name('setup-sidebar.website_faq.destroy');
});

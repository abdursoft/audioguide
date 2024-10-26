<?php

use App\Http\Controllers\FacebookController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\Payment\CancelController;
use App\Http\Controllers\Payment\StripeController;
use App\Http\Controllers\Payment\SuccessController;
use App\Http\Controllers\SectionController;
use App\Http\Middleware\StripeWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('form');
});

Route::get("login/google", [GoogleController::class, 'redirectToGoogle']);
Route::get("auth/google", [GoogleController::class, 'handleGoogleCallback']);

Route::get('login/facebook', [FacebookController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('auth/facebook', [FacebookController::class, 'handleFacebookCallback']);

Route::prefix('payment')->group(function(){
    Route::prefix('stripe')->group(function(){
        Route::get('cancel', [CancelController::class, 'stripeCancel']);
        Route::get('success', [SuccessController::class, 'stripeSuccess']);
    });
});

Route::post('stripe/subscriptions/events', [StripeController::class, 'subscriptionEvents'])->middleware([StripeWebhook::class]);
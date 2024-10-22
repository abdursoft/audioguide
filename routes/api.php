<?php

use App\Http\Controllers\AudioContentController;
use App\Http\Controllers\AudioDescriptionController;
use App\Http\Controllers\AudioFaqController;
use App\Http\Controllers\AudioGuideController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProductCartController;
use App\Http\Controllers\ProductCouponController;
use App\Http\Controllers\ProductOfferController;
use App\Http\Controllers\ProductWishController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserBillingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserShippingController;
use App\Http\Controllers\UserSubscriptionController;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\UserAuthentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::resource('section', SectionController::class);



// user api routes 
Route::prefix('v1/users')->group(function(){
    Route::post('signup', [UserController::class, 'store']);
    Route::post('signup-otp-verify', [UserController::class, 'verifySignupOTP']);
    Route::post('signin', [UserController::class, 'login']);



    Route::middleware([UserAuthentication::class])->group(function(){
        // guide cart section start 
        Route::apiResource('cart', ProductCartController::class);
        Route::apiResource('wishlist', ProductWishController::class);
        Route::apiResource('invoice', InvoiceController::class);
        // guide wishlist section end

        // billing start 
        Route::get('/billing', [UserBillingController::class, 'show']);
        Route::post('/billing/add', [UserBillingController::class, 'store']);
        Route::post('/billing/edit', [UserBillingController::class, 'store']);
        Route::delete('/billing/delete', [UserBillingController::class, 'destroy']);
        // billing end        


        // shipping start 
        Route::get('/shipping', [UserShippingController::class, 'show']);
        Route::post('/shipping/add', [UserShippingController::class, 'store']);
        Route::post('/shipping/edit', [UserShippingController::class, 'store']);
        Route::delete('/shipping/delete', [UserShippingController::class, 'destroy']);
        // shipping end

        // user subscription start
        Route::apiResource('/subscription', UserSubscriptionController::class);
        // user subscription end
    });
});


// admin api routes 
Route::prefix('v1/admin')->group(function(){
    Route::post('signin', [UserController::class, 'login']);

    Route::middleware([AdminAuth::class])->group(function(){
        Route::get('auth', [UserController::class, 'adminAuth']);

        Route::apiResource('category', CategoryController::class);
        // category section end

        Route::apiResource('section', SectionController::class);
        Route::apiResource('settings', SettingsController::class);

        // audio guide section start 
        Route::apiResource('audio-guide', AudioGuideController::class);
        // audio guide section end 
        Route::apiResource('audio-content', AudioContentController::class);
        Route::apiResource('audio-description',AudioDescriptionController::class);
        Route::apiResource('audio-faq',AudioFaqController::class);
        Route::apiResource('product-offer',ProductOfferController::class);

        // coupon section start
        Route::post('coupon', [ProductCouponController::class, 'store']);
        Route::get('coupon/{id?}', [ProductCouponController::class, 'show']);
        Route::put('coupon/{id}', [ProductCouponController::class, 'update']);
        Route::delete('coupon/{id}', [ProductCouponController::class, 'destroy']);
        // coupon section end

        // subscription section start 
        Route::apiResource('subscription', SubscriptionController::class);
        // subscription section end
    });

});



// password controller 
Route::prefix('v1/password')->group(function(){
    Route::post('send-otp', [PasswordController::class, 'sendOTP']);
    Route::post('verify-otp', [PasswordController::class, 'verifyOTP']);
    Route::post('new', [PasswordController::class, 'passwordReset']);
});

// client controller
Route::prefix('v1/client')->group(function(){
    Route::get('category', [ CategoryController::class, 'index' ]);
    Route::get('category/{category}/audio', [ CategoryController::class, 'categoryByGuide' ]);
    Route::get('sections', [ SectionController::class, 'index' ]);
    Route::get('sections/{id}', [ SectionController::class, 'singleSection' ]);
    Route::get('settings/{id}', [ SettingsController::class, 'index' ]);
    Route::get('audio/pagination', [AudioGuideController::class, 'onlyGuide']);
    Route::get('audio/by/content/{id}', [AudioGuideController::class, 'audioByContent']);
    Route::get('audio/contents', [AudioContentController::class, 'index']);
    Route::get('audio/contents/{id}', [AudioContentController::class, 'singleContent']);
    Route::get('subscriptions', [SubscriptionController::class, 'index']);
    Route::get('subscriptions/{id?}', [SubscriptionController::class, 'singleSubScription']);
});



// s3 cloudfront domain 
// https://d281ygvypsdjur.cloudfront.net
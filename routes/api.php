<?php

use App\Http\Controllers\AdminBusiness;
use App\Http\Controllers\AdminReviewController;
use App\Http\Controllers\AudioContentController;
use App\Http\Controllers\AudioDescriptionController;
use App\Http\Controllers\AudioFaqController;
use App\Http\Controllers\AudioGuideController;
use App\Http\Controllers\AudioHistoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\FrontSectionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PersonEventController;
use App\Http\Controllers\PersonLocationController;
use App\Http\Controllers\PersonObjectController;
use App\Http\Controllers\ProductCartController;
use App\Http\Controllers\ProductCouponController;
use App\Http\Controllers\ProductOfferController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProductWishController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SpecialGuideController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\UserBillingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserShippingController;
use App\Http\Controllers\UserSubscriptionController;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\AppControll;
use App\Http\Middleware\UserAuthentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::resource('section', SectionController::class);



// user api routes
Route::prefix('v1/users')->group(function () {
    Route::post('signup', [UserController::class, 'store']);
    Route::post('signup-otp-verify', [UserController::class, 'verifySignupOTP']);
    Route::post('signin', [UserController::class, 'login']);
    Route::post('otp-resend', [UserController::class, 'otpResend']);



    Route::middleware([UserAuthentication::class])->group(function () {
        Route::get('auth', [UserController::class, 'adminAuth']);
        Route::get('signout', [UserController::class, 'logOut']);

        // guide cart section start
        Route::apiResource('cart', ProductCartController::class);
        Route::delete('cart/delete/{id}', [ProductCartController::class, 'delete']);
        Route::apiResource('wishlist', ProductWishController::class);
        Route::delete('wishlist/delete/{id}', [ProductWishController::class, 'delete']);
        Route::apiResource('audio-history', AudioHistoryController::class);
        Route::post('/complete/audio-history', [AudioHistoryController::class, 'complete']);
        Route::post('/continue/audio-history', [AudioHistoryController::class, 'continue']);
        Route::apiResource('profile', ProfileController::class);
        Route::post('profile-image', [UserController::class, 'profileImage']);
        Route::get('get-profile', [ProfileController::class, 'profile']);
        Route::apiResource('invoice', InvoiceController::class);
        // guide wishlist section end

        // coupon code
        Route::post('coupon-code', [ProductCouponController::class, 'couponApply']);

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
        Route::post('cancel/subscription', [UserSubscriptionController::class, 'cancel']);
        Route::post('resume/subscription', [UserSubscriptionController::class, 'resume']);
        // user subscription end

        // product review start
        Route::post('product-review', [ProductReviewController::class, 'store']);
        // product review end

        // special route
        Route::get('home-page', [AudioGuideController::class, 'homepage']);
        Route::post('profile-update', [UserController::class, 'update']);
        Route::get('profile-data', [UserController::class, 'profileData']);
        Route::post('delete', [UserController::class, 'delete']);
    });
});


// admin api routes
Route::prefix('v1/admin')->group(function () {
    Route::post('signin', [UserController::class, 'login']);
    Route::get('export-users', [UserController::class, 'export']);

    Route::middleware([AdminAuth::class])->group(function () {
        Route::get('auth', [UserController::class, 'adminAuth']);
        Route::get('signout', [UserController::class, 'logOut']);

        Route::apiResource('category', CategoryController::class);
        // category section end

        Route::apiResource('section', SectionController::class);
        Route::apiResource('settings', SettingsController::class);

        // audio guide section start
        Route::apiResource('audio-guide', AudioGuideController::class);
        // audio guide section end

        // special guide start
        Route::post('special-guide', [SpecialGuideController::class, 'store']);
        // special guide end

        // person section start
        Route::get('person/{id?}', [PersonController::class, 'show']);
        Route::delete('person/{id}', [PersonController::class, 'destroy']);
        // person section end

        // event section start
        Route::get('event/{id?}', [PersonEventController::class, 'show']);
        Route::delete('event/{id}', [PersonEventController::class, 'destroy']);
        // event section end

        // location section start
        Route::get('location/{id?}', [PersonLocationController::class, 'show']);
        Route::delete('location/{id}', [PersonLocationController::class, 'destroy']);
        // location section end

        // object section start
        Route::get('object/{id?}', [PersonObjectController::class, 'show']);
        Route::delete('object/{id}', [PersonObjectController::class, 'destroy']);
        // object section end

        // searching system
        Route::post('search-guide', [AudioGuideController::class, 'adminGuideSearch']);

        Route::apiResource('audio-content', AudioContentController::class);
        Route::apiResource('audio-description', AudioDescriptionController::class);
        Route::apiResource('audio-faq', AudioFaqController::class);
        Route::apiResource('product-offer', ProductOfferController::class);

        // coupon section start
        Route::post('coupon', [ProductCouponController::class, 'store']);
        Route::get('coupon/{id?}', [ProductCouponController::class, 'show']);
        Route::put('coupon/{id}', [ProductCouponController::class, 'update']);
        Route::delete('coupon/{id}', [ProductCouponController::class, 'destroy']);
        // coupon section end

        // subscription section start
        Route::apiResource('subscription', SubscriptionController::class);
        // subscription section end

        // front section start
        Route::post('front-section', [FrontSectionController::class, 'store']);
        Route::get('front-section/{id?}', [FrontSectionController::class, 'show']);
        Route::put('front-section/{id}', [FrontSectionController::class, 'update']);
        Route::delete('front-section/{id}', [FrontSectionController::class, 'destroy']);
        // front section end

        // business users start
        Route::post('business/create', [AdminBusiness::class, 'create']);
        Route::get('business/users/{id?}', [AdminBusiness::class, 'showBusinessUser']);
        Route::delete('business/delete/{id}', [AdminBusiness::class, 'delete']);

        // user sections
        Route::get('get-users', [UserController::class, 'getUsers']);

        // subscription and invoices
        Route::get('get-invoices', [InvoiceController::class, "getInvoices"]);
        Route::get('get-subscriptions', [UserSubscriptionController::class, "getSubscriptions"]);

        // statistics
        Route::get('statistic/subscription/{id?}', [AdminBusiness::class, 'subscriptions']);
        Route::get('site-statistics', [AdminBusiness::class, 'statistics']);
        Route::post('subscription-status', [SubscriptionController::class, 'deactiveSubscription']);
        Route::get('revenue', [AdminBusiness::class, 'revenue']);

        // contact message
        Route::get('get-message', [ContactMessageController::class, 'getMessage']);
        Route::get('sent-message', [ContactMessageController::class, 'sentMessage']);
        Route::post('contact-message', [ContactMessageController::class, 'messageReplay']);
        Route::get('contact-message/{id}', [ContactMessageController::class, 'seenMessage']);
        Route::delete('contact-message/{id}', [ContactMessageController::class, 'destroy']);

        // get visitor
        Route::get('visitor', [DeviceController::class, 'report']);

        //change password
        Route::post('password-change', [AdminBusiness::class, 'adminPassword']);

        // admin reviews
        Route::prefix('review')->controller(AdminReviewController::class)->group(function () {
            Route::post('create', 'store');
            Route::post('update', 'update');
            Route::get('{id?}', 'show');
            Route::delete('{id}', 'destroy');
        });
    });
});



// password controller
Route::prefix('v1/password')->group(function () {
    Route::post('send-otp', [PasswordController::class, 'sendOTP']);
    Route::post('verify-otp', [PasswordController::class, 'verifyOTP']);
    Route::post('new', [PasswordController::class, 'passwordReset']);
});

// client controller
Route::prefix('v1/client')->group(function () {
    Route::get('device', [DeviceController::class, 'device']);
    Route::get('category', [CategoryController::class, 'index']);
    Route::get('category/{category}/audio', [CategoryController::class, 'categoryByGuide']);
    Route::get('sections', [SectionController::class, 'index']);
    Route::get('sections/{id}', [SectionController::class, 'singleSection']);
    Route::get('settings/{id}', [SettingsController::class, 'index']);
    Route::get('audio-guide', [AudioGuideController::class, 'index']);
    Route::get('audio-guide-by/{id}', [AudioGuideController::class, 'getAudioGuide']);
    Route::get('audio/pagination', [AudioGuideController::class, 'onlyGuide']);
    Route::get('audio/by/content/{id}', [AudioGuideController::class, 'audioByContent']);
    Route::get('audio/contents', [AudioContentController::class, 'index']);
    Route::get('audio/contents/{id}', [AudioContentController::class, 'singleContent']);
    Route::get('subscriptions', [SubscriptionController::class, 'index']);
    Route::get('subscriptions/{id?}', [SubscriptionController::class, 'singleSubScription']);
    // update routes
    Route::get('updates/{id?}', [UpdateController::class, 'show']);
    Route::get('front-section/{id?}', [ProductCouponController::class, 'show']);

    Route::post('contact-message', [ContactMessageController::class, 'store']);

    // searching system
    Route::post('search', [AudioGuideController::class, 'guideSearch']);
    // audio guide review
    Route::get('reviews/{id?}', [ProductReviewController::class, 'show']);

    // special guide section start
    Route::get('person/{id?}', [PersonController::class, 'show']);
    Route::get('event/{id?}', [PersonEventController::class, 'show']);
    Route::get('location/{id?}', [PersonLocationController::class, 'show']);
    Route::get('object/{id?}', [PersonObjectController::class, 'show']);
    Route::get('sell', [AudioGuideController::class, 'topSell']);
    // special guide section end
});

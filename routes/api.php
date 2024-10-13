<?php

use App\Http\Controllers\AudioContentController;
use App\Http\Controllers\AudioDescriptionController;
use App\Http\Controllers\AudioFaqController;
use App\Http\Controllers\AudioGuideController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProductCartController;
use App\Http\Controllers\ProductOfferController;
use App\Http\Controllers\ProductWishController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\UserAuthentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::resource('section', SectionController::class);



// user api routes 
// Route::prefix('v1/users')->group(function(){
//     Route::post('signup', [UserController::class, 'store']);
//     Route::post('signup-otp-verify', [UserController::class, 'verifySignupOTP']);
//     Route::post('signin', [UserController::class, 'login']);



//     Route::middleware([UserAuthentication::class])->group(function(){
//         // guide cart section start 
//         Route::prefix('cart')->group(function(){
//             Route::post('add', [ProductCartController::class, 'store']);
//             Route::post('edit', [ProductCartController::class, 'update']);
//             Route::get('get/{id?}', [ProductCartController::class, 'show']);
//             Route::delete('delete/{id?}', [ProductCartController::class, 'destroy']);
//         });
//         // guide cart section end

//         // guide wishlist section start 
//         Route::prefix('wishlist')->group(function(){
//             Route::post('add', [ProductWishController::class, 'store']);
//             Route::post('edit', [ProductWishController::class, 'update']);
//             Route::get('get/{id?}', [ProductWishController::class, 'show']);
//             Route::delete('delete/{id?}', [ProductWishController::class, 'destroy']);
//         });
//         // guide wishlist section end

//         // guide wishlist section start 
//         Route::prefix('invoice')->group(function(){
//             Route::post('create', [InvoiceController::class, 'store']);
//             Route::get('get/{id?}', [InvoiceController::class, 'show']);
//         });
//         // guide wishlist section end
//     });
// });


// admin api routes 
Route::prefix('v1/admin')->group(function(){
    Route::post('signin', [UserController::class, 'login']);

    Route::middleware([AdminAuth::class])->group(function(){
        Route::post('auth', [UserController::class, 'adminAuth']);

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
        Route::apiResource('audio-offer',ProductOfferController::class);

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
    Route::get('sections/{id?}', [ SectionController::class, 'show' ]);
    Route::get('settings', [ SettingsController::class, 'show' ]);
    Route::get('audio/pagination', [AudioGuideController::class, 'onlyGuide']);
    Route::get('audio/by/content/{id}', [AudioGuideController::class, 'audioByContent']);
    Route::get('audio/contents/{id?}', [AudioContentController::class, 'show']);
    Route::get('subscriptions/{id?}', [SubscriptionController::class, 'show']);
});



// s3 cloudfront domain 
// https://d281ygvypsdjur.cloudfront.net
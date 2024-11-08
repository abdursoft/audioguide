<?php

use App\Http\Controllers\FacebookController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\Payment\CancelController;
use App\Http\Controllers\Payment\StripeController;
use App\Http\Controllers\Payment\SuccessController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserController;
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

Route::get('export-users', [UserController::class, 'export']);

Route::get('aws',function(){
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_ENCODING => '', // Warning: if we don't say "Accept-Encoding: gzip", the SOB's at Amazon will send it gzip-compressed anyway.
        CURLOPT_URL => 'https://www.amazon.com/SAMSUNG-Unlocked-Smartphone-Expandable-Security/dp/B0CN1QSH8Q/?th=1'
    ));
    $html = curl_exec($ch);
    @($domd = new DOMDocument())->loadHTML($html);
    $xp=new DOMXPath($domd);
    // $product=[];
    // $product["productName"]=trim($domd->getElementById("productTitle")->textContent);
    // $product["stock"]=trim($domd->getElementById("availability")->textContent);
    // $prodInfo=$xp->query("//*[@id='productOverview_feature_div']//tr[contains(@class,'a-spacing-small')]");
    // foreach($prodInfo as $info){
    //     $product[trim($info->getElementsByTagName("td")->item(0)->textContent)]=trim($info->getElementsByTagName("td")->item(1)->textContent);
    // }
    var_export($html);
});

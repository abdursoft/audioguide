<?php

use App\Http\Controllers\FacebookController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\SectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('form');
});

Route::get("login/google", [GoogleController::class, 'redirectToGoogle']);
Route::get("auth/google", [GoogleController::class, 'handleGoogleCallback']);

Route::get('login/facebook', [FacebookController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('auth/facebook', [FacebookController::class, 'handleFacebookCallback']);
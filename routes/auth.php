<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Route;

Route::post('login',[AuthController::class, 'login']);
Route::post('profile',[AuthController::class, 'profile']);

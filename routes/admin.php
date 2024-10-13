<?php

use App\Http\Controllers\AudioGuideController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Route::resource('section', SectionController::class);

Route::prefix('section')->group(function(){
    Route::get('', [SectionController::class, 'show']);
    Route::get('/{id?}', [SectionController::class, 'show']);
    Route::post('/edit', [SectionController::class, 'sectionEdit']);
    Route::delete('/{id?}', [SectionController::class, 'destroy']);
});

Route::prefix('settings')->group(function(){
    Route::get('',[SettingsController::class, 'show']);
    Route::post('',[SettingsController::class, 'store']);
});

Route::prefix('audio-guide')->group(function(){
    Route::post('add',[AudioGuideController::class, 'store']);
});
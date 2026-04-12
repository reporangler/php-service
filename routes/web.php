<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\ComposerController;
use App\Http\Controllers\PackageController;

Route::get('/', [DefaultController::class, 'healthz']);
Route::options('/{path}', [DefaultController::class, 'cors'])->where('path', '.*');

Route::middleware(['cors'])->group(function () {
    Route::middleware(['auth:repo'])->group(function () {
        Route::get('/packages.json', [ComposerController::class, 'repository']);
        Route::get('/include/{hash}', [ComposerController::class, 'packages']);
    });

    Route::middleware(['auth:token'])->group(function () {
        Route::post('/', [PackageController::class, 'publish']);
        Route::put('/', [PackageController::class, 'update']);
        Route::delete('/', [PackageController::class, 'remove']);
    });
});

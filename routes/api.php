<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RefreshTokenController;
use App\Http\Controllers\Structure\BranchesController;
use App\Http\Controllers\Structure\LevelsController;
use App\Http\Controllers\UssdController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(static function () {
    Route::middleware('force.accept-header:application/json')->group(static function () {
        Route::post('/ussd', [UssdController::class, 'interact']);
        Route::post('/fulfillment', [UssdController::class, 'fulfilment']);
    });
});

Route::post('/login', LoginController::class);
Route::post('/token/refresh', RefreshTokenController::class);

Route::middleware(['auth:sanctum'])->group(static function () {
    Route::post('/logout', LogoutController::class);
    Route::get('/user', static fn (Request $request) => $request->user());

    Route::apiResource('/levels', LevelsController::class);
    Route::apiResource('/branches', BranchesController::class);
});

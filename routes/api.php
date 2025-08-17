<?php

declare(strict_types=1);

use App\Http\Controllers\Api\UssdController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(static function () {
    Route::middleware('force.accept-header:application/json')->group(static function () {
        Route::post('/ussd', [UssdController::class, 'interact']);
        Route::post('/fulfillment', [UssdController::class, 'fulfilment']);
    });
});

Route::middleware(['auth:sanctum'])->group(static function () {
    Route::get('/user', static fn (Request $request) => $request->user());
});

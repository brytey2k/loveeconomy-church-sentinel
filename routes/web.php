<?php

declare(strict_types=1);

use App\Http\Controllers\UsersController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\LogoutController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LevelsController;
use App\Http\Controllers\Web\PermissionsController;
use App\Http\Controllers\Web\RolesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(static function () {
    Route::get('/', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware(['auth'])->group(static function () {
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/roles', RolesController::class)->names('roles');
    Route::get('/roles/{role}/permissions', [RolesController::class, 'permissionsForm'])->name('roles.permissions-for');
    Route::post('/roles/{role}/permissions', [RolesController::class, 'savePermissions'])->name('roles.save-permissions');
    Route::resource('/permissions', PermissionsController::class)->names('permissions')
        ->except(['show', 'destroy']);
    Route::resource('/users', UsersController::class)->names('users');
    Route::resource('/levels', LevelsController::class)->names('levels');
});

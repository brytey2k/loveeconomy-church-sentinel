<?php

declare(strict_types=1);

use App\Http\Controllers\UsersController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\LogoutController;
use App\Http\Controllers\Web\BranchesController;
use App\Http\Controllers\Web\CountriesController;
use App\Http\Controllers\Web\CurrenciesController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\GivingTypesController;
use App\Http\Controllers\Web\GivingTypeSystemsController;
use App\Http\Controllers\Web\LevelsController;
use App\Http\Controllers\Web\MemberGivingsController;
use App\Http\Controllers\Web\MembersController;
use App\Http\Controllers\Web\PermissionsController;
use App\Http\Controllers\Web\PositionsController;
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
    Route::resource('/branches', BranchesController::class)->names('branches');
    Route::resource('/countries', CountriesController::class)->names('countries');
    Route::resource('/currencies', CurrenciesController::class)->names('currencies');
    Route::resource('/members', MembersController::class)->names('members');

    // Payments
    Route::get('/payments', [App\Http\Controllers\Web\TransactionsController::class, 'index'])->name('payments.index');

    // Member payments
    Route::get('/members/{member}/payments/create', [MembersController::class, 'createPayment'])->name('members.payments.create');
    Route::post('/members/{member}/payments', [MembersController::class, 'storePayment'])->name('members.payments.store');

    // Member-specific giving management routes
    Route::get('/members/{member}/givings', [MemberGivingsController::class, 'show'])->name('members.givings');
    Route::post('/members/{member}/giving-types', [MemberGivingsController::class, 'updateGivingTypes'])->name('members.giving-types.update');
    Route::post('/members/{member}/giving-types/{givingType}/systems', [MemberGivingsController::class, 'updateSystems'])->name('members.giving-types.systems.update');

    // Branch-specific giving management routes
    Route::get('/branches/{branch}/givings', [App\Http\Controllers\Web\BranchGivingsController::class, 'show'])->name('branches.givings');
    Route::post('/branches/{branch}/giving-types', [App\Http\Controllers\Web\BranchGivingsController::class, 'updateGivingTypes'])->name('branches.giving-types.update');
    Route::post('/branches/{branch}/giving-types/{givingType}/systems', [App\Http\Controllers\Web\BranchGivingsController::class, 'updateSystems'])->name('branches.giving-types.systems.update');

    Route::resource('/positions', PositionsController::class)->names('positions');
    Route::post('/giving-types/{givingType}/restore', [GivingTypesController::class, 'restore'])->name('giving-types.restore');
    Route::resource('/giving-types', GivingTypesController::class)->names('giving-types');

    // Nested routes for Giving Type Systems scoped by Giving Type (route param)
    Route::get('/giving-types/{givingType}/giving-type-systems', [GivingTypeSystemsController::class, 'index'])->name('giving-type-systems.index-for-type');
    Route::get('/giving-types/{givingType}/giving-type-systems/create', [GivingTypeSystemsController::class, 'create'])->name('giving-type-systems.create-for-type');

    // Resource routes for Giving Type Systems (unscoped)
    Route::resource('/giving-type-systems', GivingTypeSystemsController::class)
        ->names('giving-type-systems')
        ->except(['create']);
});

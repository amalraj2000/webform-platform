<?php

use App\Http\Controllers\SuperAdmin\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('superadmin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/accounts', [SuperAdminController::class, 'accounts'])->name('superadmin.accounts');
    Route::get('/accounts/{account}', [SuperAdminController::class, 'showAccount'])->name('superadmin.account.show');
    Route::get('/forms/{form}', [SuperAdminController::class, 'showForm'])->name('superadmin.form.show');
});

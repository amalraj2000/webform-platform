<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\SuperAdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isSuperAdmin()) {
            return redirect('/superadmin/dashboard');
        }
        return redirect('/admin/forms');
    })->name('dashboard');

    Route::prefix('superadmin')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard']);
        Route::get('/accounts/{account}', [SuperAdminController::class, 'showAccount']);
        Route::get('/forms/{form}', [SuperAdminController::class, 'showForm']);
    });

    Route::prefix('admin')->group(function () {
        Route::get('/forms', [FormController::class, 'index']);
        Route::post('/forms/{form}/publish', [FormController::class, 'publishVersion']);
        Route::get('/forms/{form}/submissions', [FormController::class, 'getSubmissions']);
        Route::get('/forms/{form}/export', [FormController::class, 'export']);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/forms/{uuid}', [PublicFormController::class, 'show']);

require __DIR__.'/auth.php';

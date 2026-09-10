<?php

use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isSuperAdmin()) {
            return redirect('/superadmin/dashboard');
        }

        return redirect('/admin/dashboard');
    })->name('dashboard');

    Route::prefix('superadmin')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard']);
        Route::get('/accounts', [SuperAdminController::class, 'accounts']);
        Route::get('/accounts/{account}', [SuperAdminController::class, 'showAccount']);
        Route::get('/forms/{form}', [SuperAdminController::class, 'showForm']);
    });

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [FormController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/forms', [FormController::class, 'index'])->name('admin.forms.index');
        Route::post('/forms', [FormController::class, 'store'])->name('admin.forms.store');
        Route::put('/forms/{form}', [FormController::class, 'update'])->name('admin.forms.update');
        Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('admin.forms.destroy');
        Route::get('/forms/{form}/builder', [FormController::class, 'builder'])->name('admin.forms.builder');
        Route::post('/forms/{form}/publish', [FormController::class, 'publishVersion'])->name('admin.forms.publish');
        Route::get('/forms/{form}/responses', [FormController::class, 'responses'])->name('admin.forms.responses');
        Route::get('/forms/{form}/submissions', [FormController::class, 'getSubmissions'])->name('admin.forms.submissions');
        Route::get('/forms/{form}/export', [FormController::class, 'export'])->name('admin.forms.export');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/forms/{uuid}', [PublicFormController::class, 'show']);

require __DIR__.'/auth.php';

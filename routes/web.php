<?php

use App\Http\Controllers\Web\PublicFormController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isSuperAdmin()) {
            return redirect('/superadmin/dashboard');
        }

        return redirect('/company/dashboard');
    })->name('dashboard');
});

// Load Modular Routes
require __DIR__.'/superadmin.php';
require __DIR__.'/company.php';
require __DIR__.'/auth.php';

// Public Web Routes
Route::get('/forms/{uuid}', [PublicFormController::class, 'show'])->name('web.form.show');

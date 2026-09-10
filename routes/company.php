<?php

use App\Http\Controllers\Company\FormController;
use Illuminate\Support\Facades\Route;

Route::prefix('company')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [FormController::class, 'dashboard'])->name('company.dashboard');
    Route::get('/forms', [FormController::class, 'index'])->name('company.forms.index');
    Route::post('/forms', [FormController::class, 'store'])->name('company.forms.store');
    Route::put('/forms/{form}', [FormController::class, 'update'])->name('company.forms.update');
    Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('company.forms.destroy');
    Route::get('/forms/{form}/builder', [FormController::class, 'builder'])->name('company.forms.builder');
    Route::post('/forms/{form}/publish', [FormController::class, 'publishVersion'])->name('company.forms.publish');
    Route::get('/forms/{form}/responses', [FormController::class, 'responses'])->name('company.forms.responses');
    Route::get('/forms/{form}/submissions', [FormController::class, 'getSubmissions'])->name('company.forms.submissions');
    Route::get('/forms/{form}/export', [FormController::class, 'export'])->name('company.forms.export');
});

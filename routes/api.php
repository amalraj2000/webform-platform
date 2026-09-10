<?php

use App\Http\Controllers\Api\SubmitFormController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/forms/{uuid}/submissions', [SubmitFormController::class, 'store']);

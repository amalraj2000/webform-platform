<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SubmitFormController;

Route::post('/v1/forms/{uuid}/submissions', [SubmitFormController::class, 'store']);

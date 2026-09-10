<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessFormSubmission;
use App\Models\Form;
use App\Services\DynamicFormValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class SubmitFormController extends Controller
{
    public function store(string $uuid, Request $request)
    {
        $form = Form::where('uuid', $uuid)->firstOrFail();

        $key = 'submit-form:'.$form->id.':'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) {
            return response()->json(['error' => 'Too many requests.'], 429);
        }
        RateLimiter::hit($key);

        $activeVersion = $form->publishedVersion;

        if (! $activeVersion) {
            return response()->json(['error' => 'Form not published.'], 403);
        }

        $validator = DynamicFormValidator::validate($request->all(), $activeVersion->schema);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $submissionId = (string) Str::uuid();

        ProcessFormSubmission::dispatch($submissionId, $activeVersion->id, $validator->validated(), $request->ip());

        return response()->json([
            'status' => 'accepted',
            'submission_id' => $submissionId,
        ], 202);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Form;

class PublicFormController extends Controller
{
    public function show(string $uuid)
    {
        $form = Form::where('uuid', $uuid)->firstOrFail();
        
        if (!$form->publishedVersion) {
            abort(404, 'Form not published.');
        }

        return view('public.form', compact('form'));
    }
}

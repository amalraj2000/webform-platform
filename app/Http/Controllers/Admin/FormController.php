<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormController extends Controller
{
    public function dashboard()
    {
        $accountId = auth()->user()->account_id;
        $formsCount = Form::where('account_id', $accountId)->count();
        $submissionsCount = Form::where('account_id', $accountId)
            ->join('form_versions', 'forms.published_version_id', '=', 'form_versions.id')
            ->join('submissions', 'form_versions.id', '=', 'submissions.form_version_id')
            ->count();

        return view('admin.statistics', compact('formsCount', 'submissionsCount'));
    }

    public function index()
    {
        $accountId = auth()->user()->account_id;
        $forms = Form::where('account_id', $accountId)->get();
        return view('admin.forms', compact('forms'));
    }

    public function show(Form $form)
    {
        $accountId = auth()->user()->account_id;
        if ($form->account_id !== $accountId) {
            abort(403);
        }
        
        $forms = Form::where('account_id', $accountId)->get();
        $form->load('publishedVersion');
        
        return view('admin.forms', compact('forms', 'form'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $form = Form::create([
            'account_id' => auth()->user()->account_id,
            'title' => $validated['title'],
            'description' => $validated['description']
        ]);

        return redirect()->route('admin.forms.show', $form->id);
    }

    public function publishVersion(Request $request, Form $form)
    {
        if ($form->account_id !== auth()->user()->account_id) {
            abort(403);
        }

        $validated = $request->validate([
            'schema' => 'required|array'
        ]);

        $nextVersion = $form->versions()->max('version_number') + 1;

        $version = $form->versions()->create([
            'version_number' => $nextVersion,
            'schema' => $validated['schema'],
            'is_published' => true
        ]);

        $form->update(['published_version_id' => $version->id]);

        return response()->json([
            'message' => 'Version published successfully!',
            'form' => $form->load('publishedVersion'),
            'uuid' => $form->uuid
        ]);
    }

    public function getSubmissions(Form $form)
    {
        if ($form->account_id !== auth()->user()->account_id) {
            abort(403);
        }
        return response()->json($form->submissions()->latest()->paginate(50));
    }

    public function export(Form $form)
    {
        if ($form->account_id !== auth()->user()->account_id && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $activeVersion = $form->publishedVersion;
        if (!$activeVersion) {
            abort(404, 'No published version to export.');
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="submissions.csv"',
        ];

        $columns = collect($activeVersion->schema)->pluck('name')->toArray();
        array_unshift($columns, 'id', 'ip_address', 'created_at');

        $callback = function() use ($form, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $form->submissions()->chunk(1000, function ($submissions) use ($file, $columns) {
                foreach ($submissions as $submission) {
                    $row = [
                        $submission->id,
                        $submission->ip_address,
                        $submission->created_at->toDateTimeString(),
                    ];
                    
                    foreach (array_slice($columns, 3) as $col) {
                        $row[] = $submission->data[$col] ?? '';
                    }

                    fputcsv($file, $row);
                }
            });

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;
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
        $forms = Form::where('account_id', $accountId)->withCount('submissions')->latest()->get();
        
        $formsCount = $forms->count();
        $publishedCount = $forms->whereNotNull('published_version_id')->count();
        $draftCount = $forms->whereNull('published_version_id')->count();

        return view('admin.forms.index', compact('forms', 'formsCount', 'publishedCount', 'draftCount'));
    }

    public function show(Form $form)
    {
        return redirect()->route('admin.forms.builder', $form->id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $form = Form::create([
            'account_id' => auth()->user()->account_id,
            'title' => $validated['title'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('admin.forms.builder', $form->id)->with('success', 'Form created successfully.');
    }

    public function update(Request $request, Form $form)
    {
        if ($form->account_id !== auth()->user()->account_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $form->update($validated);

        return redirect()->route('admin.forms.index')->with('success', 'Form details updated.');
    }

    public function destroy(Form $form)
    {
        if ($form->account_id !== auth()->user()->account_id) {
            abort(403);
        }

        if ($form->submissions()->count() > 0) {
            return redirect()->route('admin.forms.index')->with('error', 'Cannot delete a form that has submissions.');
        }

        $form->versions()->delete();
        $form->delete();

        return redirect()->route('admin.forms.index')->with('success', 'Form deleted successfully.');
    }

    public function builder(Form $form)
    {
        if ($form->account_id !== auth()->user()->account_id) {
            abort(403);
        }

        $form->load('publishedVersion');

        return view('admin.forms.builder', compact('form'));
    }

    public function responses(Form $form)
    {
        if ($form->account_id !== auth()->user()->account_id) {
            abort(403);
        }

        $form->load('publishedVersion');

        return view('admin.forms.responses', compact('form'));
    }

    public function publishVersion(Request $request, Form $form)
    {
        if ($form->account_id !== auth()->user()->account_id) {
            abort(403);
        }

        $validated = $request->validate([
            'schema' => 'required|array',
            'schema.*.name' => 'required|string|distinct',
            'schema.*.label' => 'required|string',
            'schema.*.type' => 'required|string|in:text,email,number,date,select,radio,checkbox',
            'schema.*.required' => 'nullable|boolean',
            'schema.*.help_text' => 'nullable|string',
            'schema.*.condition_field' => 'nullable|string',
            'schema.*.condition_value' => 'nullable|string',
            'schema.*.options' => 'nullable|array',
        ]);

        $nextVersion = $form->versions()->max('version_number') + 1;

        $version = $form->versions()->create([
            'version_number' => $nextVersion,
            'schema' => $validated['schema'],
            'is_published' => true,
        ]);

        $form->update(['published_version_id' => $version->id]);

        return response()->json([
            'message' => 'Version published successfully!',
            'form' => $form->load('publishedVersion'),
            'uuid' => $form->uuid,
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
        if ($form->account_id !== auth()->user()->account_id && ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $activeVersion = $form->publishedVersion;
        if (! $activeVersion) {
            abort(404, 'No published version to export.');
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="submissions.csv"',
        ];

        $columns = collect($activeVersion->schema)->pluck('name')->toArray();
        array_unshift($columns, 'id', 'ip_address', 'created_at');

        $callback = function () use ($form, $columns) {
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
                        $val = $submission->data[$col] ?? '';
                        // Prevent CSV Injection
                        if (preg_match('/^[=\+\-@\t\r]/', $val)) {
                            $val = "'".$val;
                        }
                        $row[] = $val;
                    }

                    fputcsv($file, $row);
                }
            });

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}

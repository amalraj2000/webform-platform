<?php

namespace Tests\Feature;

use App\Jobs\ProcessFormSubmission;
use App\Models\Account;
use App\Models\Form;
use App\Models\FormVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class VersionIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_submissions_link_to_exact_published_version()
    {
        Queue::fake();

        $account = Account::create(['name' => 'Acme', 'slug' => 'acme']);
        $form = Form::create(['account_id' => $account->id, 'title' => 'Test Form']);

        $version1 = FormVersion::create([
            'form_id' => $form->id,
            'version_number' => 1,
            'schema' => [['name' => 'field1', 'type' => 'text', 'required' => true]],
            'is_published' => true,
        ]);
        $form->update(['published_version_id' => $version1->id]);

        $this->postJson("/api/v1/forms/{$form->uuid}/submissions", ['field1' => 'v1']);

        Queue::assertPushed(ProcessFormSubmission::class, function ($job) use ($version1) {
            return $job->formVersionId === $version1->id;
        });

        // Publish version 2
        $version2 = FormVersion::create([
            'form_id' => $form->id,
            'version_number' => 2,
            'schema' => [['name' => 'field2', 'type' => 'text', 'required' => true]],
            'is_published' => true,
        ]);
        $form->update(['published_version_id' => $version2->id]);

        $this->postJson("/api/v1/forms/{$form->uuid}/submissions", ['field2' => 'v2']);

        Queue::assertPushed(ProcessFormSubmission::class, function ($job) use ($version2) {
            return $job->formVersionId === $version2->id;
        });
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Account;
use App\Models\Form;
use App\Models\FormVersion;

class DynamicValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dynamic_validation_rejects_invalid_inputs()
    {
        $account = Account::create(['name' => 'Acme', 'slug' => 'acme']);
        $form = Form::create(['account_id' => $account->id, 'title' => 'Test Form']);
        $version = FormVersion::create([
            'form_id' => $form->id,
            'version_number' => 1,
            'schema' => [
                ['name' => 'age', 'type' => 'number', 'required' => true]
            ],
            'is_published' => true
        ]);
        $form->update(['published_version_id' => $version->id]);

        $response = $this->postJson("/api/v1/forms/{$form->uuid}/submissions", [
            'age' => 'Not a number'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['age']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Form;
use App\Models\FormVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
                ['name' => 'age', 'type' => 'number', 'required' => true],
            ],
            'is_published' => true,
        ]);
        $form->update(['published_version_id' => $version->id]);

        $response = $this->postJson("/api/v1/forms/{$form->uuid}/submissions", [
            'age' => 'Not a number',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['age']);
    }

    public function test_dynamic_validation_ignores_hidden_conditional_fields()
    {
        $account = Account::create(['name' => 'Acme', 'slug' => 'acme']);
        $form = Form::create(['account_id' => $account->id, 'title' => 'Test Form']);
        $version = FormVersion::create([
            'form_id' => $form->id,
            'version_number' => 1,
            'schema' => [
                ['name' => 'subscribe', 'type' => 'radio', 'options' => ['Yes', 'No'], 'required' => true],
                ['name' => 'email', 'type' => 'email', 'required' => true, 'condition_field' => 'subscribe', 'condition_value' => 'Yes'],
            ],
            'is_published' => true,
        ]);
        $form->update(['published_version_id' => $version->id]);

        // Submit with subscribe = Yes, but no email (should fail because email is required and visible)
        $response1 = $this->postJson("/api/v1/forms/{$form->uuid}/submissions", [
            'subscribe' => 'Yes',
        ]);
        $response1->assertStatus(422);
        $response1->assertJsonValidationErrors(['email']);

        // Submit with subscribe = No, and no email (should pass because email is hidden)
        $response2 = $this->postJson("/api/v1/forms/{$form->uuid}/submissions", [
            'subscribe' => 'No',
        ]);
        $response2->assertStatus(202);
    }
}

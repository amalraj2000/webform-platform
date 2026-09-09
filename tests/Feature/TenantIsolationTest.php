<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Form;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_admin_cannot_access_other_tenant_forms()
    {
        $account1 = Account::create(['name' => 'Acme', 'slug' => 'acme']);
        $user1 = User::factory()->create(['account_id' => $account1->id, 'role' => 'company_admin']);
        $form1 = Form::create(['account_id' => $account1->id, 'title' => 'Form 1']);

        $account2 = Account::create(['name' => 'Beta', 'slug' => 'beta']);
        $user2 = User::factory()->create(['account_id' => $account2->id, 'role' => 'company_admin']);
        $form2 = Form::create(['account_id' => $account2->id, 'title' => 'Form 2']);

        $response = $this->actingAs($user1)->get("/admin/forms/{$form2->id}/submissions");
        $response->assertStatus(403);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_accounts(): void
    {
        $response = $this->get('/accounts');
        $response->assertStatus(200);

        $response = $this->post('/accounts', [
            'account_name' => 'Test Account',
            'fb_mail' => 'fb@test.com',
            'gmail' => 'gmail@test.com',
            'ms_mail' => 'ms@test.com',
            'account_status' => 'active',
        ]);

        $response->assertRedirect('/accounts');
        $this->assertDatabaseHas('accounts', [
            'account_name' => 'Test Account',
            'gmail' => 'gmail@test.com',
        ]);
    }
}

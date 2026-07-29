<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Element;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_account_seeder_from_database(): void
    {
        Account::factory()->create([
            'account_name' => 'Seeder Test',
            'fb_mail' => 'fb@example.com',
            'gmail' => 'gmail@example.com',
            'ms_mail' => 'ms@example.com',
            'account_status' => 'active',
        ]);

        $response = $this->post(route('accounts.update-seeder'));

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success', 'AccountSeeder berhasil diperbarui.');

        $seederPath = base_path('database/seeders/AccountSeeder.php');
        $this->assertFileExists($seederPath);
        $this->assertStringContainsString("'account_name' => 'Seeder Test'", file_get_contents($seederPath));
    }

    public function test_user_can_update_element_seeder_from_database(): void
    {
        Element::factory()->create([
            'name' => 'Seeder Element',
            'alias' => 'se',
        ]);

        $response = $this->post(route('elements.update-seeder'));

        $response->assertRedirect(route('elements.index'));
        $response->assertSessionHas('success', 'ElementSeeder berhasil diperbarui.');

        $seederPath = base_path('database/seeders/ElementSeeder.php');
        $this->assertFileExists($seederPath);
        $this->assertStringContainsString("'name' => 'Seeder Element'", file_get_contents($seederPath));
    }

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

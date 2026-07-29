<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_seeder_can_run_multiple_times_without_creating_duplicates(): void
    {
        $this->assertDatabaseCount('accounts', 0);

        $firstResponse = $this->post(route('home.run-seeder'), ['seed' => 'AccountSeeder']);
        $firstResponse->assertRedirect(route('home'));

        $this->assertDatabaseCount('accounts', 16);

        $secondResponse = $this->post(route('home.run-seeder'), ['seed' => 'AccountSeeder']);
        $secondResponse->assertRedirect(route('home'));

        $this->assertDatabaseCount('accounts', 16);
    }

    public function test_account_seeder_creates_separate_rows_when_gmail_is_placeholder_dash(): void
    {
        $this->artisan('db:seed', ['--class' => 'AccountSeeder']);

        $accounts = Account::whereIn('account_name', ['laravue', 'django', 'mongodb'])
            ->orderBy('account_name')
            ->get();

        $this->assertCount(3, $accounts);
        $this->assertSame(['django', 'laravue', 'mongodb'], $accounts->pluck('account_name')->all());
    }
}

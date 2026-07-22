<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\OrbOwning;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrbOwningTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_orb_ownings_index(): void
    {
        $response = $this->get('/orb-ownings');
        $response->assertStatus(200);
    }

    public function test_user_can_create_and_store_orb_owning_entry(): void
    {
        $account = Account::factory()->create();
        $dragon = Dragon::factory()->create();

        $response = $this->post(route('orb-ownings.store'), [
            'account_id' => $account->id,
            'dragon_id' => $dragon->id,
            'jumlah_orb' => 15,
        ]);

        $response->assertRedirect(route('orb-ownings.index'));
        $this->assertDatabaseHas('orb_ownings', [
            'account_id' => $account->id,
            'dragon_id' => $dragon->id,
            'jumlah_orb' => 15,
        ]);
    }

    public function test_user_can_update_orb_owning_entry(): void
    {
        $account = Account::factory()->create();
        $dragon = Dragon::factory()->create();
        $orbOwning = OrbOwning::factory()->create(['account_id' => $account->id, 'dragon_id' => $dragon->id, 'jumlah_orb' => 10]);

        $response = $this->put(route('orb-ownings.update', $orbOwning), [
            'account_id' => $account->id,
            'dragon_id' => $dragon->id,
            'jumlah_orb' => 20,
        ]);

        $response->assertRedirect(route('orb-ownings.index'));
        $this->assertDatabaseHas('orb_ownings', [
            'id' => $orbOwning->id,
            'jumlah_orb' => 20,
        ]);
    }

    public function test_user_can_delete_orb_owning_entry(): void
    {
        $orbOwning = OrbOwning::factory()->create(['jumlah_orb' => 5]);

        $response = $this->delete(route('orb-ownings.destroy', $orbOwning));

        $response->assertRedirect(route('orb-ownings.index'));
        $this->assertDatabaseMissing('orb_ownings', [
            'id' => $orbOwning->id,
        ]);
    }
}

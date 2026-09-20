<?php

namespace Tests\Feature;

use App\Models\Rarity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RarityCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_rarity_seeder_from_database(): void
    {
        Rarity::factory()->create([
            'name' => 'Seeder Rarity',
            'alias' => 'sr',
            'key_need_to_summon' => 'seed-key',
        ]);

        $response = $this->post(route('rarities.update-seeder'));

        $response->assertRedirect(route('rarities.index'));
        $response->assertSessionHas('success', 'RaritySeeder berhasil diperbarui.');

        $seederPath = base_path('database/seeders/RaritySeeder.php');
        $this->assertFileExists($seederPath);
        $this->assertStringContainsString("'name' => 'Seeder Rarity'", file_get_contents($seederPath));
        $this->assertStringContainsString("'id' =>", file_get_contents($seederPath));
    }

    public function test_user_can_create_and_view_rarities(): void
    {
        $response = $this->get('/rarities');
        $response->assertStatus(200);

        $response = $this->post('/rarities', [
            'name' => 'Legendary',
            'alias' => 'Leg',
            'key_need_to_summon' => 'legendary-key',
            'orb_per_trade' => 25,
        ]);

        $response->assertRedirect('/rarities');
        $this->assertDatabaseHas('rarities', [
            'name' => 'Legendary',
            'alias' => 'Leg',
            'orb_per_trade' => 25,
        ]);
    }

    public function test_user_can_update_orb_per_trade(): void
    {
        $rarity = Rarity::factory()->create(['orb_per_trade' => 10]);

        $response = $this->put(route('rarities.update', $rarity), [
            'name' => $rarity->name,
            'alias' => $rarity->alias,
            'key_need_to_summon' => $rarity->key_need_to_summon,
            'orb_per_trade' => 30,
        ]);

        $response->assertRedirect('/rarities');
        $this->assertDatabaseHas('rarities', [
            'id' => $rarity->id,
            'orb_per_trade' => 30,
        ]);
    }
}

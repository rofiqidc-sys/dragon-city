<?php

namespace Tests\Feature;

use App\Models\Dragon;
use App\Models\Element;
use App\Models\Rarity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DragonTruncateTest extends TestCase
{
    use RefreshDatabase;

    public function test_truncate_endpoint_clears_dragons_and_resets_the_sequence(): void
    {
        Storage::fake('local');

        $rarity = Rarity::create(['name' => 'Common', 'alias' => 'common']);
        $element = Element::create(['name' => 'Fire', 'alias' => 'fire']);

        Dragon::create([
            'dragon_book' => '0001',
            'dragon_name' => 'Fire Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
            'orb_to_summon' => 0,
            'hatching_time' => 0,
        ]);

        $this->assertDatabaseCount('dragons', 1);

        $response = $this->post('/dragons/truncate', [
            'confirmation' => 'TRUNCATE DRAGONS',
        ]);

        $response->assertRedirect(route('dragons.index'));
        $this->assertDatabaseCount('dragons', 0);

        $newDragon = Dragon::create([
            'dragon_book' => '0002',
            'dragon_name' => 'Water Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
            'orb_to_summon' => 0,
            'hatching_time' => 0,
        ]);

        $this->assertSame(1, $newDragon->id);
        Storage::disk('local')->assertExists('dragon-backups');
    }

    public function test_latest_backup_can_restore_truncated_dragon_data(): void
    {
        Storage::fake('local');

        $rarity = Rarity::create(['name' => 'Common', 'alias' => 'common']);
        $element = Element::create(['name' => 'Fire', 'alias' => 'fire']);
        $dragon = Dragon::create([
            'dragon_book' => '0001',
            'dragon_name' => 'Fire Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
            'orb_to_summon' => 0,
            'hatching_time' => 0,
        ]);

        $this->post(route('dragons.truncate'), ['confirmation' => 'TRUNCATE DRAGONS']);
        $response = $this->post(route('dragons.restore-latest'), ['confirmation' => 'RESTORE DRAGONS']);

        $response->assertRedirect(route('dragons.index'));
        $this->assertDatabaseHas('dragons', [
            'id' => $dragon->id,
            'dragon_name' => 'Fire Dragon',
        ]);
    }

    public function test_truncate_requires_explicit_server_side_confirmation(): void
    {
        $dragon = Dragon::factory()->create();

        $response = $this->post(route('dragons.truncate'));

        $response->assertSessionHasErrors('confirmation');
        $this->assertDatabaseHas('dragons', ['id' => $dragon->id]);
    }
}

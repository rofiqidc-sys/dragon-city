<?php

namespace Tests\Feature;

use App\Models\Dragon;
use App\Models\Element;
use App\Models\Rarity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DragonTruncateTest extends TestCase
{
    use RefreshDatabase;

    public function test_truncate_endpoint_clears_dragons_and_resets_the_sequence(): void
    {
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

        $response = $this->post('/dragons/truncate');

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
    }
}

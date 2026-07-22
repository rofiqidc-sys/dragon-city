<?php

namespace Tests\Feature;

use App\Models\Dragon;
use App\Models\Element;
use App\Models\Rarity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DragonCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_dragons(): void
    {
        $rarity = Rarity::factory()->create();
        $element1 = Element::factory()->create();
        $element2 = Element::factory()->create();

        $response = $this->get('/dragons');
        $response->assertStatus(200);

        $response = $this->post('/dragons', [
            'dragon_name' => 'Fire Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element1->id,
            'element_2_id' => $element2->id,
            'element_3_id' => null,
            'element_4_id' => null,
            'summon_time' => null,
            'orb_to_summon' => 50,
            'hatching_time' => 3600,
        ]);

        $response->assertRedirect('/dragons');
        $this->assertDatabaseHas('dragons', [
            'dragon_name' => 'Fire Dragon',
            'rarity_id' => $rarity->id,
            'orb_to_summon' => 50,
        ]);
    }

    public function test_aliases_are_generated_from_names_and_are_unique(): void
    {
        $rarity = Rarity::factory()->create();
        $element = Element::factory()->create();

        Dragon::factory()->count(2)->create([
            'dragon_name' => 'Fire Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
        ]);

        $response = $this->post(route('dragons.generate-aliases'));

        $response->assertRedirect(route('dragons.index'));

        $aliases = Dragon::pluck('alias');

        $this->assertCount(2, $aliases);
        $this->assertCount(2, $aliases->unique());
        $aliases->each(fn ($alias) => $this->assertMatchesRegularExpression('/^[A-Z0-9]{7}$/', $alias));
        $this->assertTrue($aliases->every(fn ($alias) => str_starts_with($alias, 'FIRE')));
    }

    public function test_user_can_create_dragon_without_rarity(): void
    {
        $element = Element::factory()->create();

        $response = $this->post('/dragons', [
            'dragon_name' => 'Unknown Dragon',
            'rarity_id' => null,
            'element_1_id' => $element->id,
            'element_2_id' => null,
            'element_3_id' => null,
            'element_4_id' => null,
            'summon_time' => null,
            'orb_to_summon' => 0,
            'hatching_time' => 0,
        ]);

        $response->assertRedirect('/dragons');
        $this->assertDatabaseHas('dragons', [
            'dragon_name' => 'Unknown Dragon',
            'rarity_id' => null,
        ]);
    }
}

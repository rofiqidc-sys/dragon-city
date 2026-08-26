<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Dragon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_dragon_can_belong_to_two_collections(): void
    {
        $dragon = Dragon::factory()->create();
        $firstCollection = Collection::create(['collection_name' => 'First Collection', 'gem_reward' => 10]);
        $secondCollection = Collection::create(['collection_name' => 'Second Collection', 'gem_reward' => 20]);

        $this->post(route('collections.add-dragon', $firstCollection), ['dragon_id' => $dragon->id]);
        $this->post(route('collections.add-dragon', $secondCollection), ['dragon_id' => $dragon->id]);

        $this->assertDatabaseHas('collection_dragon_members', [
            'collection_id' => $firstCollection->id,
            'dragon_id' => $dragon->id,
        ]);
        $this->assertDatabaseHas('collection_dragon_members', [
            'collection_id' => $secondCollection->id,
            'dragon_id' => $dragon->id,
        ]);
        $this->assertCount(1, $firstCollection->fresh()->dragons);
        $this->assertCount(1, $secondCollection->fresh()->dragons);
    }

    public function test_removing_dragon_from_one_collection_keeps_other_membership(): void
    {
        $dragon = Dragon::factory()->create();
        $firstCollection = Collection::create(['collection_name' => 'First Collection', 'gem_reward' => 10]);
        $secondCollection = Collection::create(['collection_name' => 'Second Collection', 'gem_reward' => 20]);

        $firstCollection->dragons()->attach($dragon);
        $secondCollection->dragons()->attach($dragon);

        $response = $this->delete(route('collections.remove-dragon', [$firstCollection, $dragon]));

        $response->assertRedirect(route('collections.show', $firstCollection));
        $this->assertDatabaseMissing('collection_dragon_members', [
            'collection_id' => $firstCollection->id,
            'dragon_id' => $dragon->id,
        ]);
        $this->assertDatabaseHas('collection_dragon_members', [
            'collection_id' => $secondCollection->id,
            'dragon_id' => $dragon->id,
        ]);
    }
}
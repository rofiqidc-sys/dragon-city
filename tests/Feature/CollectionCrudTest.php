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

    public function test_collection_index_can_filter_by_member_dragon_name_or_book_with_minimum_three_characters(): void
    {
        $forestCollection = Collection::create(['collection_name' => 'Forest Collection', 'gem_reward' => 10]);
        $skyCollection = Collection::create(['collection_name' => 'Sky Collection', 'gem_reward' => 20]);

        $aquaDragon = Dragon::factory()->create([
            'dragon_name' => 'Aqua Dragon',
            'dragon_book' => '0010',
        ]);
        $fireDragon = Dragon::factory()->create([
            'dragon_name' => 'Fire Dragon',
            'dragon_book' => '0005',
        ]);

        $forestCollection->dragons()->attach($aquaDragon);
        $skyCollection->dragons()->attach($fireDragon);

        $response = $this->get(route('collections.index', ['search' => 'AQU']));

        $response->assertOk();
        $response->assertSee('Forest Collection');
        $response->assertDontSee('Sky Collection');

        $shortResponse = $this->get(route('collections.index', ['search' => 'AQ']));
        $shortResponse->assertOk();
        $shortResponse->assertSee('Forest Collection');
        $shortResponse->assertSee('Sky Collection');
    }

    public function test_collection_show_page_has_number_pad_for_adding_member(): void
    {
        $collection = Collection::create(['collection_name' => 'Forest Collection', 'gem_reward' => 10]);

        $response = $this->get(route('collections.show', $collection));

        $response->assertOk();
        $response->assertSee('Dragon Book Number Pad');
        $response->assertSee('data-key="1"');
        $response->assertSee('data-action="clear"');
        $response->assertSee('data-action="backspace"');
    }
}
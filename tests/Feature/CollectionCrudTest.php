<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Dragon;
use App\Models\DragonOwningDetail;
use App\Models\Account;
use App\Models\OrbOwning;
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

    public function test_achievement_is_calculated_from_account_one_ownership(): void
    {
        Account::factory()->create(['id' => 1]);
        $collection = Collection::create(['collection_name' => 'Forest Collection', 'gem_reward' => 10]);
        $ownedDragon = Dragon::factory()->create();
        $unownedDragon = Dragon::factory()->create();

        $collection->dragons()->attach([$ownedDragon->id, $unownedDragon->id]);
        DragonOwningDetail::create(['account_id' => 1, 'dragon_id' => $ownedDragon->id]);

        $response = $this->post(route('collections.calculate-achievement'));

        $response->assertRedirect(route('collections.index'));
        $this->assertDatabaseHas('collections', [
            'id' => $collection->id,
            'achievement' => '50.00',
        ]);
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

    public function test_unowned_collection_members_page_filters_by_is_rescue_status(): void
    {
        Account::factory()->create(['id' => 1]);
        $collection = Collection::create(['collection_name' => 'Rescue Collection', 'gem_reward' => 10]);
        $rarity = \App\Models\Rarity::factory()->create(['name' => 'Rare']);

        $rescueDragon = Dragon::factory()->create([
            'dragon_name' => 'Rescue Dragon',
            'rarity_id' => $rarity->id,
            'is_rescue' => true,
        ]);
        $normalDragon = Dragon::factory()->create([
            'dragon_name' => 'Normal Dragon',
            'rarity_id' => $rarity->id,
            'is_rescue' => false,
        ]);

        $collection->dragons()->attach([$rescueDragon->id, $normalDragon->id]);

        $response = $this->get(route('collections.unowned-members', ['is_rescue' => '1']));

        $response->assertOk();
        $response->assertSee('Rescue Dragon');
        $response->assertDontSee('Normal Dragon');
    }

    public function test_unowned_collection_members_page_filters_by_rarity_and_account_one_ownership(): void
    {
        Account::factory()->create(['id' => 1]);
        Account::factory()->create(['id' => 2, 'account_name' => 'Account Two']);
        $rarity = \App\Models\Rarity::factory()->create(['name' => 'Rare']);
        $otherRarity = \App\Models\Rarity::factory()->create(['name' => 'Epic']);
        $collection = Collection::create(['collection_name' => 'Forest Collection', 'gem_reward' => 10]);

        $eligibleDragon = Dragon::factory()->create([
            'dragon_name' => 'Aqua Dragon',
            'rarity_id' => $rarity->id,
        ]);
        $ownedDragon = Dragon::factory()->create([
            'dragon_name' => 'Fire Dragon',
            'rarity_id' => $rarity->id,
        ]);
        $otherRarityDragon = Dragon::factory()->create([
            'dragon_name' => 'Stone Dragon',
            'rarity_id' => $otherRarity->id,
        ]);
        $notMemberDragon = Dragon::factory()->create([
            'dragon_name' => 'Wind Dragon',
            'rarity_id' => $rarity->id,
        ]);

        $collection->dragons()->attach([$eligibleDragon->id, $ownedDragon->id, $otherRarityDragon->id]);
        OrbOwning::create([
            'account_id' => 1,
            'dragon_id' => $eligibleDragon->id,
            'jumlah_orb' => 18,
        ]);
        OrbOwning::create([
            'account_id' => 2,
            'dragon_id' => $eligibleDragon->id,
            'jumlah_orb' => 27,
        ]);
        DragonOwningDetail::create([
            'account_id' => 2,
            'dragon_id' => $eligibleDragon->id,
        ]);
        DragonOwningDetail::create(['account_id' => 1, 'dragon_id' => $ownedDragon->id]);

        $response = $this->get(route('collections.unowned-members', [
            'rarity' => $rarity->id,
            'account_id' => 2,
        ]));

        $response->assertOk();
        $response->assertSee('Aqua Dragon');
        $response->assertSee('18');
        $response->assertSee('27');
        $response->assertSee('account-comparison-owned');
        $response->assertDontSee('Fire Dragon');
        $response->assertDontSee('Stone Dragon');
        $response->assertDontSee('Wind Dragon');
    }

    public function test_dragon_rewards_includes_account_one_orb_count(): void
    {
        $account = Account::factory()->create(['id' => 1]);
        $dragon = Dragon::factory()->create(['dragon_name' => 'Orb Dragon']);
        $collection = Collection::create([
            'collection_name' => 'Orb Collection',
            'gem_reward' => 10,
            'dragon_reward_id' => $dragon->id,
        ]);
        $collection->dragons()->attach($dragon);
        OrbOwning::create([
            'account_id' => $account->id,
            'dragon_id' => $dragon->id,
            'jumlah_orb' => 42,
        ]);

        $response = $this->getJson(route('collections.dragon-rewards'));

        $response->assertOk()
            ->assertJsonPath('data.0.dragon_name', 'Orb Dragon')
            ->assertJsonPath('data.0.jumlah_orb', 42);
    }
}
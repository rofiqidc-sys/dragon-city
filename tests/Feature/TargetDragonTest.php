<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\DragonOwningDetail;
use App\Models\Element;
use App\Models\OrbOwning;
use App\Models\Rarity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TargetDragonTest extends TestCase
{
    use RefreshDatabase;

    public function test_target_dragon_page_lists_unique_dragons_owned_by_accounts_other_than_account_one(): void
    {
        $rarity = Rarity::factory()->create();
        $element = Element::factory()->create();

        $accountOne = Account::factory()->create([
            'account_name' => 'Account One',
        ]);
        $accountTwo = Account::factory()->create([
            'account_name' => 'Account Two',
        ]);
        $accountThree = Account::factory()->create([
            'account_name' => 'Account Three',
        ]);

        $accountOne->forceFill(['id' => 1])->saveQuietly();

        $dragonA = Dragon::factory()->create([
            'dragon_book' => '0001',
            'dragon_name' => 'Aqua Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
        ]);
        $dragonB = Dragon::factory()->create([
            'dragon_book' => '0002',
            'dragon_name' => 'Fire Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
        ]);

        DragonOwningDetail::create([
            'account_id' => $accountOne->id,
            'dragon_id' => $dragonA->id,
        ]);
        DragonOwningDetail::create([
            'account_id' => $accountTwo->id,
            'dragon_id' => $dragonA->id,
        ]);
        DragonOwningDetail::create([
            'account_id' => $accountThree->id,
            'dragon_id' => $dragonB->id,
        ]);

        $response = $this->get(route('target-dragons.index'));

        $response->assertStatus(200);
        $response->assertViewHas('dragons', function ($dragons) {
            return $dragons->count() === 1;
        });
        $response->assertSee('Fire Dragon');
        $response->assertDontSee('Aqua Dragon');
    }

    public function test_target_dragon_page_can_filter_by_account(): void
    {
        $rarity = Rarity::factory()->create();
        $element = Element::factory()->create();

        $accountOne = Account::factory()->create([
            'account_name' => 'Account One',
        ]);
        $accountTwo = Account::factory()->create([
            'account_name' => 'Account Two',
        ]);
        $accountThree = Account::factory()->create([
            'account_name' => 'Account Three',
        ]);

        $accountOne->forceFill(['id' => 1])->saveQuietly();

        $dragonA = Dragon::factory()->create([
            'dragon_book' => '0001',
            'dragon_name' => 'Aqua Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
        ]);
        $dragonB = Dragon::factory()->create([
            'dragon_book' => '0002',
            'dragon_name' => 'Fire Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
        ]);
        $dragonC = Dragon::factory()->create([
            'dragon_book' => '0003',
            'dragon_name' => 'Wind Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
        ]);

        DragonOwningDetail::create([
            'account_id' => $accountTwo->id,
            'dragon_id' => $dragonA->id,
        ]);
        DragonOwningDetail::create([
            'account_id' => $accountTwo->id,
            'dragon_id' => $dragonB->id,
        ]);
        DragonOwningDetail::create([
            'account_id' => $accountThree->id,
            'dragon_id' => $dragonC->id,
        ]);

        $response = $this->get(route('target-dragons.index', ['account' => $accountTwo->id]));

        $response->assertStatus(200);
        $response->assertViewHas('dragons', function ($dragons) {
            return $dragons->count() === 2;
        });
        $response->assertSee('Aqua Dragon');
        $response->assertSee('Fire Dragon');
        $response->assertDontSee('Wind Dragon');
    }

    public function test_target_dragon_page_filters_by_rarity_rescue_and_collection_and_shows_owners_and_orbs(): void
    {
        $accountOne = Account::factory()->create(['account_name' => 'Account One']);
        $owner = Account::factory()->create(['account_name' => 'Target Owner']);
        $accountOne->forceFill(['id' => 1])->saveQuietly();
        $rarity = Rarity::factory()->create(['name' => 'Legendary']);
        $element = Element::factory()->create();

        $matchingDragon = Dragon::factory()->create([
            'dragon_name' => 'Matching Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
            'is_rescue' => true,
            'is_collection' => true,
        ]);
        $nonMatchingDragon = Dragon::factory()->create([
            'dragon_name' => 'Non Matching Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
            'is_rescue' => false,
            'is_collection' => true,
        ]);

        DragonOwningDetail::create(['account_id' => $owner->id, 'dragon_id' => $matchingDragon->id]);
        DragonOwningDetail::create(['account_id' => $owner->id, 'dragon_id' => $nonMatchingDragon->id]);
        OrbOwning::create(['account_id' => $owner->id, 'dragon_id' => $matchingDragon->id, 'jumlah_orb' => 42]);

        $response = $this->get(route('target-dragons.index', [
            'account' => $owner->id,
            'rarity' => $rarity->id,
            'is_rescue' => '1',
            'is_collection' => '1',
        ]));

        $response->assertStatus(200);
        $response->assertSee('id="target-dragons-table"', false);
        $response->assertSee('<th>Pemilik</th>', false);
        $response->assertSee('<th>Jumlah Orb</th>', false);
        $response->assertSee('Matching Dragon');
        $response->assertSee('Legendary');
        $response->assertSee('Target Owner');
        $response->assertSee('<td>42</td>', false);
        $response->assertDontSee('Non Matching Dragon');
    }

    public function test_master_dragon_page_can_filter_by_rescue_and_collection_flags(): void
    {
        $account = Account::factory()->create();
        $account->forceFill(['id' => 1])->saveQuietly();

        $rarity = Rarity::factory()->create();
        $element = Element::factory()->create();

        $rescueDragon = Dragon::factory()->create([
            'dragon_name' => 'Rescue Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
            'is_rescue' => true,
            'is_collection' => false,
        ]);
        $collectionDragon = Dragon::factory()->create([
            'dragon_name' => 'Collection Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
            'is_rescue' => false,
            'is_collection' => true,
        ]);
        $normalDragon = Dragon::factory()->create([
            'dragon_name' => 'Normal Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
            'is_rescue' => false,
            'is_collection' => false,
        ]);

        $response = $this->get(route('master-dragons.index', [
            'is_rescue' => '1',
            'is_collection' => '0',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('dragons', function ($dragons) use ($rescueDragon, $collectionDragon, $normalDragon) {
            $ids = $dragons->pluck('id')->all();

            return count($ids) === 1
                && in_array($rescueDragon->id, $ids, true)
                && ! in_array($collectionDragon->id, $ids, true)
                && ! in_array($normalDragon->id, $ids, true);
        });
        $response->assertSee('Rescue Dragon');
        $response->assertDontSee('Collection Dragon');
        $response->assertDontSee('Normal Dragon');
    }
}

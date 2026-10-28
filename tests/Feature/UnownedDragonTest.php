<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\DragonOwningDetail;
use App\Models\Element;
use App\Models\Rarity;
use App\Models\UnownedDragon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnownedDragonTest extends TestCase
{
    use RefreshDatabase;

    public function test_unowned_dragons_page_lists_dragons_not_owned_by_any_account(): void
    {
        $account = Account::factory()->create();
        $unowned = Dragon::factory()->create(['dragon_name' => 'Unowned Dragon']);
        $owned = Dragon::factory()->create(['dragon_name' => 'Owned Dragon']);
        DragonOwningDetail::create([
            'account_id' => $account->id,
            'dragon_id' => $owned->id,
        ]);

        $response = $this->get(route('unowned-dragons.index'));

        $response->assertOk()
            ->assertSee('Unowned Dragon')
            ->assertDontSee('Owned Dragon');
        $this->assertDatabaseHas('unowned_dragons', ['dragon_id' => $unowned->id]);
        $this->assertDatabaseMissing('unowned_dragons', ['dragon_id' => $owned->id]);
    }

    public function test_unowned_dragon_filters_can_be_combined(): void
    {
        $rarity = Rarity::factory()->create();
        $element = Element::factory()->create();
        $matchingDragon = Dragon::factory()->create([
            'dragon_name' => 'Matching Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
            'is_rescue' => true,
            'is_collection' => true,
        ]);
        Dragon::factory()->create([
            'dragon_name' => 'Other Dragon',
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
            'is_rescue' => false,
            'is_collection' => true,
        ]);

        $response = $this->get(route('unowned-dragons.index', [
            'rarity' => $rarity->id,
            'is_rescue' => '1',
            'is_collection' => '1',
        ]));

        $response->assertOk()
            ->assertSee('Matching Dragon')
            ->assertDontSee('Other Dragon');
        $this->assertDatabaseHas('unowned_dragons', ['dragon_id' => $matchingDragon->id]);
    }

    public function test_unowned_dragons_table_tracks_ownership_changes_for_all_accounts(): void
    {
        $firstAccount = Account::factory()->create();
        $secondAccount = Account::factory()->create();
        $dragon = Dragon::factory()->create();

        $firstOwnership = DragonOwningDetail::create([
            'account_id' => $firstAccount->id,
            'dragon_id' => $dragon->id,
        ]);
        $this->assertDatabaseMissing('unowned_dragons', ['dragon_id' => $dragon->id]);

        $secondOwnership = DragonOwningDetail::create([
            'account_id' => $secondAccount->id,
            'dragon_id' => $dragon->id,
        ]);
        $firstOwnership->delete();
        $this->assertDatabaseMissing('unowned_dragons', ['dragon_id' => $dragon->id]);

        $secondOwnership->delete();
        $this->assertDatabaseHas('unowned_dragons', ['dragon_id' => $dragon->id]);
    }

    public function test_deleting_an_account_synchronizes_its_dragons_as_unowned(): void
    {
        $account = Account::factory()->create();
        $dragon = Dragon::factory()->create();
        DragonOwningDetail::create([
            'account_id' => $account->id,
            'dragon_id' => $dragon->id,
        ]);

        $account->delete();

        $this->assertDatabaseHas('unowned_dragons', ['dragon_id' => $dragon->id]);
    }
}

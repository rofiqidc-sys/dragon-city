<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\DragonOwningDetail;
use App\Models\Element;
use App\Models\Rarity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DragonOwningTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_dragon_ownings_page(): void
    {
        $account = Account::factory()->create();
        
        $response = $this->get('/dragon-ownings');
        $response->assertStatus(200);
        $response->assertViewHas('accounts');
    }

    public function test_user_can_view_add_dragon_form(): void
    {
        $account = Account::factory()->create();
        $dragon = Dragon::factory()->create();

        $response = $this->get(route('dragon-ownings.create', $account));
        $response->assertStatus(200);
        $response->assertViewHas('account');
        $response->assertViewHas('dragons');
    }

    public function test_add_dragon_form_contains_touch_number_pad(): void
    {
        $account = Account::factory()->create();
        Dragon::factory()->create(['dragon_book' => '0001']);

        $response = $this->get(route('dragon-ownings.create', $account));

        $response->assertStatus(200);
        $response->assertSee('Dragon Book Number Pad');
        $response->assertSee('data-key="1"', false);
        $response->assertSee('data-key="0"', false);
        $response->assertSee('data-action="backspace"', false);
        $response->assertSee('data-action="clear"', false);
    }

    public function test_user_can_view_account_cards_with_dragons(): void
    {
        $rarity = Rarity::factory()->create();
        $element = Element::factory()->create();
        $account = Account::factory()->create();
        $dragon = Dragon::factory()->create([
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
        ]);

        DragonOwningDetail::create([
            'account_id' => $account->id,
            'dragon_id' => $dragon->id,
        ]);

        $response = $this->get('/dragon-ownings');
        $response->assertStatus(200);
        $response->assertSee($account->account_name);
    }

    public function test_account_dragon_show_page_contains_touch_number_pad(): void
    {
        $account = Account::factory()->create();
        Dragon::factory()->create(['dragon_book' => '0001']);

        $response = $this->get(route('dragon-ownings.show', $account));

        $response->assertStatus(200);
        $response->assertSee('Dragon Book Number Pad');
        $response->assertSee('data-key="1"', false);
        $response->assertSee('data-key="0"', false);
        $response->assertSee('data-action="backspace"', false);
        $response->assertSee('data-action="clear"', false);
    }

    public function test_add_dragon_form_sorts_dragons_by_dragon_book(): void
    {
        $account = Account::factory()->create();

        $dragonLater = Dragon::factory()->create([
            'dragon_book' => '0050',
        ]);
        $dragonEarlier = Dragon::factory()->create([
            'dragon_book' => '0001',
        ]);

        $response = $this->get(route('dragon-ownings.create', $account));

        $response->assertStatus(200);
        $response->assertViewHas('dragons', function ($dragons) use ($dragonEarlier, $dragonLater) {
            $orderedIds = $dragons->pluck('id')->all();

            return $orderedIds[0] === $dragonEarlier->id && $orderedIds[1] === $dragonLater->id;
        });
    }

    public function test_user_can_view_quick_add_form_with_unowned_dragons(): void
    {
        $account = Account::factory()->create();
        $availableDragon = Dragon::factory()->create(['dragon_name' => 'Available Dragon']);
        $ownedDragon = Dragon::factory()->create(['dragon_name' => 'Owned Dragon']);

        DragonOwningDetail::create([
            'account_id' => $account->id,
            'dragon_id' => $ownedDragon->id,
        ]);

        $response = $this->get(route('dragon-ownings.quick-create', $account));

        $response->assertOk();
        $response->assertSee('Available Dragon');
        $response->assertDontSee('Owned Dragon');
        $response->assertSee('name="dragon_ids[]"', false);
    }

    public function test_user_can_quick_add_multiple_dragons_to_account(): void
    {
        $account = Account::factory()->create();
        $firstDragon = Dragon::factory()->create();
        $secondDragon = Dragon::factory()->create();

        $response = $this->post(route('dragon-ownings.quick-store', $account), [
            'dragon_ids' => [$firstDragon->id, $secondDragon->id],
        ]);

        $response->assertRedirect(route('dragon-ownings.quick-create', $account));
        $this->assertDatabaseHas('dragon_owning_details', [
            'account_id' => $account->id,
            'dragon_id' => $firstDragon->id,
        ]);
        $this->assertDatabaseHas('dragon_owning_details', [
            'account_id' => $account->id,
            'dragon_id' => $secondDragon->id,
        ]);
    }

    public function test_quick_add_filters_by_rarity_and_paginates_by_fifteen(): void
    {
        $account = Account::factory()->create();
        $rarity = Rarity::factory()->create(['name' => 'Quick Rare']);
        $otherRarity = Rarity::factory()->create(['name' => 'Other Rarity']);

        Dragon::factory()->count(16)->create(['rarity_id' => $rarity->id]);
        $otherDragon = Dragon::factory()->create([
            'rarity_id' => $otherRarity->id,
            'dragon_name' => 'Other Rarity Dragon',
        ]);

        $response = $this->get(route('dragon-ownings.quick-create', [
            'account' => $account,
            'rarity' => $rarity->id,
        ]));

        $response->assertOk();
        $response->assertViewHas('dragons', function ($dragons) use ($otherDragon) {
            return $dragons->total() === 16
                && $dragons->perPage() === 15
                && !$dragons->getCollection()->contains('id', $otherDragon->id);
        });
    }

    public function test_user_can_add_dragon_to_account(): void
    {
        $rarity = Rarity::factory()->create();
        $element = Element::factory()->create();
        $account = Account::factory()->create();
        $dragon = Dragon::factory()->create([
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
        ]);

        $response = $this->post(route('dragon-owning-details.store', $account), [
            'dragon_id' => $dragon->id,
        ]);

        $response->assertRedirect(route('dragon-ownings.show', $account));
        $this->assertDatabaseHas('dragon_owning_details', [
            'account_id' => $account->id,
            'dragon_id' => $dragon->id,
        ]);
    }

    public function test_user_cannot_add_duplicate_dragon_to_account(): void
    {
        $rarity = Rarity::factory()->create();
        $element = Element::factory()->create();
        $account = Account::factory()->create();
        $dragon = Dragon::factory()->create([
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
        ]);

        DragonOwningDetail::create([
            'account_id' => $account->id,
            'dragon_id' => $dragon->id,
        ]);

        $response = $this->post(route('dragon-owning-details.store', $account), [
            'dragon_id' => $dragon->id,
        ]);

        $response->assertRedirect(route('dragon-ownings.show', $account));
        $response->assertSessionHas('warning', "Dragon '{$dragon->dragon_name}' is already assigned to this account.");
    }

    public function test_user_can_remove_dragon_from_account(): void
    {
        $rarity = Rarity::factory()->create();
        $element = Element::factory()->create();
        $account = Account::factory()->create();
        $dragon = Dragon::factory()->create([
            'rarity_id' => $rarity->id,
            'element_1_id' => $element->id,
        ]);

        $detail = DragonOwningDetail::create([
            'account_id' => $account->id,
            'dragon_id' => $dragon->id,
        ]);

        $response = $this->delete(route('dragon-owning-details.destroy', [$account, $detail]));

        $response->assertRedirect(route('dragon-ownings.index'));
        $this->assertDatabaseMissing('dragon_owning_details', [
            'id' => $detail->id,
        ]);
    }
}

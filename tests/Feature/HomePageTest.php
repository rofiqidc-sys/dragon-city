<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\DragonOwningDetail;
use App\Models\OrbOwning;
use App\Models\Rarity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_theme_template(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Flash Able - Most Trusted Admin Template');
        $response->assertSee('Dashboard');
        $response->assertSee('style="background-color: #F4BE38; color: #212529;"', false);
    }

    public function test_home_page_shows_rarity_totals_and_account_ownership_statuses(): void
    {
        Account::factory()->create(['id' => 1]);
        Account::factory()->create(['id' => 2, 'account_name' => 'Other Account']);
        $heroicRarity = Rarity::factory()->create([
            'name' => 'Heroic',
            'color' => '#F4BE38',
        ]);

        $accountOneDragon = Dragon::factory()->create([
            'rarity_id' => $heroicRarity->id,
            'dragon_name' => 'Owned Heroic',
        ]);
        $otherAccountDragon = Dragon::factory()->create([
            'rarity_id' => $heroicRarity->id,
            'dragon_name' => 'Ongoing Heroic',
            'orb_to_summon' => 50,
        ]);
        $zeroOrbDragon = Dragon::factory()->create([
            'rarity_id' => $heroicRarity->id,
            'dragon_name' => 'Ongoing Heroic Zero Orb',
            'orb_to_summon' => 50,
        ]);
        $enoughOrbDragon = Dragon::factory()->create([
            'rarity_id' => $heroicRarity->id,
            'dragon_name' => 'Ongoing Heroic Enough Orb',
            'orb_to_summon' => 50,
        ]);
        Dragon::factory()->createMany([
            ['rarity_id' => $heroicRarity->id, 'dragon_name' => 'Soon Heroic One'],
            ['rarity_id' => $heroicRarity->id, 'dragon_name' => 'Soon Heroic Two'],
        ]);

        DragonOwningDetail::create(['account_id' => 1, 'dragon_id' => $accountOneDragon->id]);
        DragonOwningDetail::create(['account_id' => 2, 'dragon_id' => $otherAccountDragon->id]);
        DragonOwningDetail::create(['account_id' => 2, 'dragon_id' => $zeroOrbDragon->id]);
        DragonOwningDetail::create(['account_id' => 2, 'dragon_id' => $enoughOrbDragon->id]);
        OrbOwning::create(['account_id' => 1, 'dragon_id' => $otherAccountDragon->id, 'jumlah_orb' => 20]);
        OrbOwning::create(['account_id' => 1, 'dragon_id' => $enoughOrbDragon->id, 'jumlah_orb' => 50]);
        OrbOwning::create(['account_id' => 2, 'dragon_id' => $otherAccountDragon->id, 'jumlah_orb' => 12]);

        $response = $this->get('/');

        $response->assertOk();
        foreach (['Heroic', 'Mythical', 'Legendary', 'Epic', 'Very Rare', 'Rare', 'Common'] as $name) {
            $response->assertSee('data-rarity="'.$name.'"', false);
        }
        $response->assertSee('style="background-color: #F4BE38; color: #212529;"', false);
        $response->assertSee('data-status="owned_dragons" data-count="1"', false);
        $response->assertSee('data-status="ongoing_dragons" data-count="3"', false);
        $response->assertSee('data-status="soon_dragons" data-count="2"', false);
        $response->assertSee('background-color: #06D6A0;', false);
        $response->assertSee('background-color: #FFD166;', false);
        $response->assertSee('background-color: #FF7F50;', false);
        $response->assertSee(route('dashboard.rarity-details', [
            'rarityId' => $heroicRarity->id,
            'status' => 'ongoing_dragons',
        ]), false);

        $this->get(route('dashboard.rarity-details', [$heroicRarity->id, 'owned_dragons']))
            ->assertOk()
            ->assertSee('Owned Heroic')
            ->assertDontSee('Ongoing Heroic');

        $this->get(route('dashboard.rarity-details', [$heroicRarity->id, 'ongoing_dragons']))
            ->assertOk()
            ->assertSee('id="rarity-dragons-table"', false)
            ->assertSee('Cari dragon atau account...')
            ->assertSee("$('#rarity-dragons-table').DataTable", false)
            ->assertSee('<col style="width: 45%;">', false)
            ->assertSee('style="width: 100%; min-width: 0; white-space: normal; padding: .25rem;"', false)
            ->assertSee('class="d-flex flex-wrap"', false)
            ->assertSee('Ongoing Heroic')
            ->assertSee('Update Orb')
            ->assertSee('Other Account: 12 orb')
            ->assertSee('data-account-id="2"', false)
            ->assertSee('name="account_id" value="1"', false)
            ->assertSee(route('orb-ownings.upsert'), false)
            ->assertSee('Ongoing Heroic Zero Orb')
            ->assertSee('Ongoing Heroic Enough Orb')
            ->assertSee('Other Account')
            ->assertSee('style="background-color: #F7D6D0;"', false)
            ->assertSee('style="background-color: #FFEDB9;"', false)
            ->assertSee('style="background-color: #C4F7CA;"', false)
            ->assertSee('20 / 50')
            ->assertSee('50 / 50')
            ->assertDontSee('Owned Heroic');

        $this->get(route('dashboard.rarity-details', [$heroicRarity->id, 'soon_dragons']))
            ->assertOk()
            ->assertSee('Soon Heroic One')
            ->assertSee('Soon Heroic Two')
            ->assertDontSee('Ongoing Heroic');

        $this->postJson(route('orb-ownings.upsert'), [
            'account_id' => 1,
            'dragon_id' => $otherAccountDragon->id,
            'jumlah_orb' => 30,
        ])
            ->assertOk()
            ->assertJsonPath('data.jumlah_orb', 30);

        $this->assertDatabaseHas('orb_ownings', [
            'account_id' => 1,
            'dragon_id' => $otherAccountDragon->id,
            'jumlah_orb' => 30,
        ]);

        $this->postJson(route('orb-ownings.upsert'), [
            'account_id' => 2,
            'dragon_id' => $otherAccountDragon->id,
            'jumlah_orb' => 25,
        ])
            ->assertOk()
            ->assertJsonPath('data.jumlah_orb', 25);

        $this->assertDatabaseHas('orb_ownings', [
            'account_id' => 2,
            'dragon_id' => $otherAccountDragon->id,
            'jumlah_orb' => 25,
        ]);
    }
}

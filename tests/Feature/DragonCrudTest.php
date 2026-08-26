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

    public function test_user_can_mark_dragon_as_best_heroic(): void
    {
        $dragon = Dragon::factory()->create(['is_best_heroic' => false]);

        $response = $this->post(route('dragons.markBestHeroic', $dragon));

        $response->assertRedirect(route('dragons.index'));
        $this->assertDatabaseHas('dragons', [
            'id' => $dragon->id,
            'is_best_heroic' => true,
        ]);
    }

    public function test_dragons_page_displays_current_best_heroic_dragons(): void
    {
        $dragon = Dragon::factory()->create([
            'dragon_name' => 'History Dragon',
            'is_best_heroic' => true,
        ]);

        $response = $this->get(route('dragons.index'));

        $response->assertStatus(200);
        $response->assertSee('History Best Heroic');
        $response->assertSee('History Dragon');
    }

    public function test_dragons_page_sorts_by_dragon_book(): void
    {
        Dragon::factory()->create([
            'dragon_book' => '0050',
            'dragon_name' => 'Zeta Dragon',
        ]);
        Dragon::factory()->create([
            'dragon_book' => '0001',
            'dragon_name' => 'Alpha Dragon',
        ]);

        $response = $this->get(route('dragons.index', ['sort' => 'asc']));

        $response->assertStatus(200);
        $response->assertViewHas('dragons', function ($dragons) {
            $ids = $dragons->pluck('id')->all();

            return $ids[0] === Dragon::where('dragon_name', 'Alpha Dragon')->value('id')
                && $ids[1] === Dragon::where('dragon_name', 'Zeta Dragon')->value('id');
        });
    }

    public function test_dragons_page_shows_account_ownership_and_orb_details(): void
    {
        $dragon = Dragon::factory()->create(['dragon_name' => 'Account Detail Dragon']);
        $owner = Account::factory()->create(['account_name' => 'Owner Account']);
        $nonOwner = Account::factory()->create(['account_name' => 'Other Account']);

        DragonOwningDetail::create([
            'account_id' => $owner->id,
            'dragon_id' => $dragon->id,
        ]);
        OrbOwning::create([
            'account_id' => $owner->id,
            'dragon_id' => $dragon->id,
            'jumlah_orb' => 27,
        ]);

        $response = $this->get(route('dragons.index'));

        $response->assertStatus(200);
        $response->assertSee('Detail account');
        $response->assertSee('Owner Account');
        $response->assertSee('Other Account');
        $response->assertSee('Dimiliki');
        $response->assertSee('Belum dimiliki');
        $response->assertSee('27');
    }

    public function test_update_dragon_seeder_action_writes_static_payload_to_seeder_file(): void
    {
        $dragon = Dragon::factory()->create([
            'dragon_book' => '9999',
            'dragon_name' => 'Seeder Update Dragon',
            'alias' => 'SEED99',
            'orb_to_summon' => 42,
        ]);

        $seederPath = base_path('database/seeders/DragonSeeder.php');
        $originalContent = file_get_contents($seederPath);

        try {
            $response = $this->post(route('dragons.export-seeder-array'));

            $response->assertRedirect(route('dragons.index'));

            $updatedContent = file_get_contents($seederPath);
            $this->assertStringContainsString("'dragon_book' => '9999'", $updatedContent);
            $this->assertStringContainsString("'dragon_name' => 'Seeder Update Dragon'", $updatedContent);
        } finally {
            file_put_contents($seederPath, $originalContent);
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\Dragon;
use App\Models\Element;
use App\Models\Rarity;
use App\Services\DragonBookScraper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DragonScrapePersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_scrape_persists_dragons_and_related_reference_data(): void
    {
        $this->app->instance(DragonBookScraper::class, new class extends DragonBookScraper
        {
            public function fetch(string $parameter): array
            {
                return [
                    [
                        'number' => '0101',
                        'name' => 'Fire Dragon',
                        'link' => '/wiki/Fire_Dragon',
                        'element' => ['Fire', 'Wind'],
                        'rarity' => 'Epic',
                    ],
                    [
                        'number' => '0102',
                        'name' => 'Water Dragon',
                        'link' => '/wiki/Water_Dragon',
                        'element' => ['Water'],
                        'rarity' => 'Very Rare',
                    ],
                ];
            }
        });

        $response = $this->getJson('/dragons/scrape?parameter=0101-0200');

        $response->assertOk();
        $this->assertDatabaseHas('dragons', ['dragon_book' => '0101', 'dragon_name' => 'Fire Dragon']);
        $this->assertDatabaseHas('dragons', ['dragon_book' => '0102', 'dragon_name' => 'Water Dragon']);
        $this->assertDatabaseHas('rarities', ['name' => 'Epic']);
        $this->assertDatabaseHas('rarities', ['name' => 'Very Rare']);
        $this->assertDatabaseHas('rarities', ['name' => 'Epic', 'alias' => 'epic']);
        $this->assertSame(2, Rarity::count());
        $this->assertDatabaseHas('elements', ['name' => 'Fire']);
        $this->assertDatabaseHas('elements', ['name' => 'Wind']);
        $this->assertDatabaseHas('elements', ['name' => 'Water']);

        $dragon = Dragon::where('dragon_book', '0101')->first();
        $this->assertNotNull($dragon);
        $this->assertNotNull($dragon->element_1_id);
        $this->assertNotNull($dragon->element_2_id);
        $this->assertEquals('Epic', Rarity::find($dragon->rarity_id)->name);
    }
}

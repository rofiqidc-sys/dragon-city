<?php

namespace Tests\Unit;

use App\Services\DragonBookScraper;
use PHPUnit\Framework\TestCase;

class DragonBookScraperTest extends TestCase
{
    public function test_it_parses_dragon_rows_from_wiki_table_html(): void
    {
        $html = <<<'HTML'
<div class="mw-content-ltr mw-parser-output">
<h3>Dragons 0001 - 0100</h3>
<table id="tpt-1" class="table-progress-tracking wikitable sortable mw-datatable">
<tbody>
<tr><th>#</th><th>Picture</th><th>Name</th><th>Element</th><th>Rarity</th></tr>
<tr><td>0001</td><td></td><td><a href="/wiki/Fire_Dragon" title="Fire Dragon">Fire Dragon</a></td><td><a href="/wiki/Category:Fire_Dragons" title="Category:Fire Dragons"></a><a href="/wiki/Category:Wind_Dragons" title="Category:Wind Dragons"></a></td><td><img alt="Common" title="Category:Common Dragons"></td></tr>
<tr><td>0002</td><td></td><td><a href="/wiki/Water_Dragon" title="Water Dragon">Water Dragon</a></td><td><a href="/wiki/Category:Water_Dragons" title="Category:Water Dragons"></a></td><td><img alt="Rare" title="Category:Rare Dragons"></td></tr>
</tbody>
</table>
</div>
HTML;

        $scraper = new DragonBookScraper();

        $dragons = $scraper->parseDragonData($html);

        $this->assertCount(2, $dragons);
        $this->assertSame('Fire Dragon', $dragons[0]['name']);
        $this->assertSame('/wiki/Fire_Dragon', $dragons[0]['link']);
        $this->assertSame(['Fire', 'Wind'], $dragons[0]['element']);
        $this->assertSame('Common', $dragons[0]['rarity']);
        $this->assertSame('Water Dragon', $dragons[1]['name']);
        $this->assertSame(['Water'], $dragons[1]['element']);
        $this->assertSame('Rare', $dragons[1]['rarity']);
        $this->assertArrayHasKey('number', $dragons[0]);
    }

    public function test_it_keeps_very_rare_as_rarity_not_element(): void
    {
        $html = <<<'HTML'
<div class="mw-content-ltr mw-parser-output">
<h3>Dragons 0101 - 0200</h3>
<table id="tpt-1" class="table-progress-tracking wikitable sortable mw-datatable">
<tbody>
<tr><th>#</th><th>Picture</th><th>Name</th><th>Element</th><th>Rarity</th></tr>
<tr><td>0101</td><td></td><td><a href="/wiki/Fire_Dragon" title="Fire Dragon">Fire Dragon</a></td><td><a href="/wiki/Category:Fire_Dragons" title="Category:Fire Dragons"></a></td><td><img alt="Very Rare" title="Category:Very Rare Dragons"></td></tr>
</tbody>
</table>
</div>
HTML;

        $scraper = new DragonBookScraper();

        $dragons = $scraper->parseDragonData($html);

        $this->assertSame(['Fire'], $dragons[0]['element']);
        $this->assertSame('Very Rare', $dragons[0]['rarity']);
    }
}

<?php

namespace App\Console\Commands;

use App\Services\DragonBookScraper;
use Illuminate\Console\Command;

class ScrapeDragonBook extends Command
{
    protected $signature = 'dragon-book:scrape {parameter=0001-0100}';

    protected $description = 'Scrape dragon entries from the Dragon Book page for the given range.';

    public function handle(DragonBookScraper $scraper): int
    {
        $parameter = $this->argument('parameter');
        $dragons = $scraper->fetch($parameter);

        if ($dragons === []) {
            $this->error('Tidak ada data yang berhasil diambil.');
            return self::FAILURE;
        }

        $this->info('Jumlah dragon: ' . count($dragons));

        foreach ($dragons as $index => $dragon) {
            $this->line(($index + 1) . '. ' . $dragon['name'] . ' -> ' . $dragon['link']);
        }

        return self::SUCCESS;
    }
}

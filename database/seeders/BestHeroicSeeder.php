<?php

namespace Database\Seeders;

use App\Models\Dragon;
use Illuminate\Database\Seeder;

class BestHeroicSeeder extends Seeder
{
    public function run(): void
    {
        $bestHeroicDragonBooks = [
            '1430',
            '1706',
            '1769',
            '1815',
            '1840',
            '1883',
            '1908',
            '1936',
            '1959',
            '1984',
            '2004',
        ];

        Dragon::query()->update(['is_best_heroic' => false]);

        Dragon::query()
            ->whereIn('dragon_book', $bestHeroicDragonBooks)
            ->update(['is_best_heroic' => true]);
    }
}
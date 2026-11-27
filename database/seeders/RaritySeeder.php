<?php

namespace Database\Seeders;

use App\Models\Rarity;
use Illuminate\Database\Seeder;

class RaritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rarities = [
            ['id' => 1, 'name' => 'Common', 'alias' => 'c', 'key_need_to_summon' => null, 'orb_per_trade' => null, 'color' => '#F3F6F4'],
            ['id' => 2, 'name' => 'Rare', 'alias' => 'r', 'key_need_to_summon' => null, 'orb_per_trade' => null, 'color' => '#FFCA88'],
            ['id' => 3, 'name' => 'Very Rare', 'alias' => 'vr', 'key_need_to_summon' => null, 'orb_per_trade' => null, 'color' => '#66BB6A'],
            ['id' => 4, 'name' => 'Epic', 'alias' => 'e', 'key_need_to_summon' => null, 'orb_per_trade' => null, 'color' => '#FFCC4D'],
            ['id' => 5, 'name' => 'Legendary', 'alias' => 'l', 'key_need_to_summon' => null, 'orb_per_trade' => null, 'color' => '#792CA2'],
            ['id' => 6, 'name' => 'Mythical', 'alias' => 'm', 'key_need_to_summon' => null, 'orb_per_trade' => null, 'color' => '#FA699E'],
            ['id' => 7, 'name' => 'Heroic', 'alias' => 'h', 'key_need_to_summon' => null, 'orb_per_trade' => null, 'color' => '#F4BE38'],
        ];

        foreach ($rarities as $rarity) {
            Rarity::updateOrCreate(
                ['id' => $rarity['id']],
                $rarity
            );
        }
    }
}
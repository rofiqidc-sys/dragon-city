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
        $rarities = array (
  0 => 
  array (
    'id' => 1,
    'name' => 'Common',
    'alias' => 'c',
    'key_need_to_summon' => NULL,
  ),
  1 => 
  array (
    'id' => 2,
    'name' => 'Rare',
    'alias' => 'r',
    'key_need_to_summon' => NULL,
  ),
  2 => 
  array (
    'id' => 3,
    'name' => 'Very Rare',
    'alias' => 'vr',
    'key_need_to_summon' => NULL,
  ),
  3 => 
  array (
    'id' => 4,
    'name' => 'Epic',
    'alias' => 'e',
    'key_need_to_summon' => NULL,
  ),
  4 => 
  array (
    'id' => 5,
    'name' => 'Legendary',
    'alias' => 'l',
    'key_need_to_summon' => NULL,
  ),
  5 => 
  array (
    'id' => 6,
    'name' => 'Mythical',
    'alias' => 'm',
    'key_need_to_summon' => NULL,
  ),
  6 => 
  array (
    'id' => 7,
    'name' => 'Heroic',
    'alias' => 'h',
    'key_need_to_summon' => NULL,
  ),
);

        foreach ($rarities as $rarity) {
            Rarity::updateOrCreate(
                ['id' => $rarity['id']],
                $rarity
            );
        }
    }
}
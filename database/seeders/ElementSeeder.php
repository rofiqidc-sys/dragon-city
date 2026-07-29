<?php

namespace Database\Seeders;

use App\Models\Element;
use Illuminate\Database\Seeder;

class ElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $elements = [
            ['id' => 1, 'name' => 'Terra', 'alias' => 'terra'],
            ['id' => 2, 'name' => 'Flame', 'alias' => 'flame'],
            ['id' => 3, 'name' => 'Sea', 'alias' => 'sea'],
            ['id' => 4, 'name' => 'Nature', 'alias' => 'nature'],
            ['id' => 5, 'name' => 'Electric', 'alias' => 'electric'],
            ['id' => 6, 'name' => 'Ice', 'alias' => 'ice'],
            ['id' => 7, 'name' => 'Metal', 'alias' => 'metal'],
            ['id' => 8, 'name' => 'Dark', 'alias' => 'dark'],
            ['id' => 9, 'name' => 'Light', 'alias' => 'light'],
            ['id' => 10, 'name' => 'War', 'alias' => 'war'],
            ['id' => 11, 'name' => 'Pure', 'alias' => 'pure'],
            ['id' => 12, 'name' => 'Legend', 'alias' => 'legend'],
            ['id' => 13, 'name' => 'Beauty', 'alias' => 'beauty'],
            ['id' => 14, 'name' => 'Ancient', 'alias' => 'ancient'],
            ['id' => 15, 'name' => 'Chaos', 'alias' => 'chaos'],
            ['id' => 16, 'name' => 'Magic', 'alias' => 'magic'],
            ['id' => 17, 'name' => 'Dream', 'alias' => 'dream'],
            ['id' => 18, 'name' => 'Soul', 'alias' => 'soul'],
            ['id' => 19, 'name' => 'Happy', 'alias' => 'happy'],
            ['id' => 20, 'name' => 'Primal', 'alias' => 'primal'],
            ['id' => 21, 'name' => 'Wind', 'alias' => 'wind'],
            ['id' => 22, 'name' => 'Time', 'alias' => 'time'],
        ];

        foreach ($elements as $element) {
            Element::updateOrCreate(
                ['id' => $element['id']],
                $element
            );
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\Dragon;
use Illuminate\Database\Seeder;

class DragonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dragons = array (
);

        foreach ($dragons as $dragon) {
            Dragon::updateOrCreate(
                ['dragon_book' => $dragon['dragon_book']],
                $dragon
            );
        }
    }
}
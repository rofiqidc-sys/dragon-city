<?php

namespace Database\Factories;

use App\Models\Dragon;
use App\Models\Rarity;
use App\Models\Element;
use Illuminate\Database\Eloquent\Factories\Factory;

class DragonFactory extends Factory
{
    protected $model = Dragon::class;

    public function definition(): array
    {
        return [
            'dragon_name' => $this->faker->word(),
            'rarity_id' => Rarity::factory(),
            'element_1_id' => Element::factory(),
            'element_2_id' => null,
            'element_3_id' => null,
            'element_4_id' => null,
            'summon_time' => null,
            'orb_to_summon' => $this->faker->numberBetween(10, 100),
            'hatching_time' => $this->faker->numberBetween(600, 3600),
        ];
    }
}

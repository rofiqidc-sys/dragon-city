<?php

namespace Database\Factories;

use App\Models\Rarity;
use Illuminate\Database\Eloquent\Factories\Factory;

class RarityFactory extends Factory
{
    protected $model = Rarity::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'alias' => $this->faker->word(),
            'key_need_to_summon' => $this->faker->word(),
        ];
    }
}

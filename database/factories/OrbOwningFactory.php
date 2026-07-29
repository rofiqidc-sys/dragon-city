<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\OrbOwning;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrbOwningFactory extends Factory
{
    protected $model = OrbOwning::class;

    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'dragon_id' => Dragon::factory(),
            'jumlah_orb' => $this->faker->numberBetween(0, 100),
        ];
    }
}
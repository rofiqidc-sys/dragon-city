<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'account_name' => $this->faker->word(),
            'fb_mail' => $this->faker->email(),
            'gmail' => $this->faker->email(),
            'ms_mail' => $this->faker->email(),
            'account_status' => 'active',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\TradingTask;
use Illuminate\Database\Eloquent\Factories\Factory;

class TradingTaskFactory extends Factory
{
    protected $model = TradingTask::class;

    public function definition(): array
    {
        return [
            'dragon_id' => Dragon::factory(),
            'trader_id' => Account::factory(),
            'reciever_id' => Account::factory(),
            'jumlah_orb' => 10,
            'status_trade' => 'recalling',
        ];
    }
}
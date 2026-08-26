<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\OrbOwning;
use App\Models\TradingTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradingTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_trading_tasks_index(): void
    {
        $response = $this->get(route('trading-tasks.index'));

        $response->assertStatus(200);
        $response->assertSee('Trading Task List');
    }

    public function test_create_and_edit_forms_use_dragon_autocomplete(): void
    {
        $task = TradingTask::factory()->create();

        $createResponse = $this->get(route('trading-tasks.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('id="trading_dragon_name"', false);
        $createResponse->assertSee('id="trading_dragon_id"', false);
        $createResponse->assertSee('id="trading_dragon_suggestions"', false);

        $editResponse = $this->get(route('trading-tasks.edit', $task));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('id="trading_dragon_name"', false);
        $editResponse->assertSee('value="' . $task->dragon_id . '"', false);
    }

    public function test_done_trade_moves_orbs_from_trader_to_reciever(): void
    {
        $dragon = Dragon::factory()->create();
        $trader = Account::factory()->create();
        $reciever = Account::factory()->create();
        OrbOwning::create(['account_id' => $trader->id, 'dragon_id' => $dragon->id, 'jumlah_orb' => 50]);

        $response = $this->post(route('trading-tasks.store'), [
            'dragon_id' => $dragon->id,
            'trader_id' => $trader->id,
            'reciever_id' => $reciever->id,
            'jumlah_orb' => 15,
            'status_trade' => 'done',
        ]);

        $response->assertRedirect(route('trading-tasks.index'));
        $this->assertDatabaseHas('orb_ownings', ['account_id' => $trader->id, 'dragon_id' => $dragon->id, 'jumlah_orb' => 35]);
        $this->assertDatabaseHas('orb_ownings', ['account_id' => $reciever->id, 'dragon_id' => $dragon->id, 'jumlah_orb' => 15]);
    }

    public function test_trade_in_non_done_status_does_not_move_orbs(): void
    {
        $dragon = Dragon::factory()->create();
        $trader = Account::factory()->create();
        $reciever = Account::factory()->create();
        OrbOwning::create(['account_id' => $trader->id, 'dragon_id' => $dragon->id, 'jumlah_orb' => 20]);

        $this->post(route('trading-tasks.store'), [
            'dragon_id' => $dragon->id,
            'trader_id' => $trader->id,
            'reciever_id' => $reciever->id,
            'jumlah_orb' => 10,
            'status_trade' => 'ready',
        ]);

        $this->assertDatabaseHas('orb_ownings', ['account_id' => $trader->id, 'jumlah_orb' => 20]);
        $this->assertDatabaseMissing('orb_ownings', ['account_id' => $reciever->id, 'dragon_id' => $dragon->id]);
    }

    public function test_done_trade_requires_enough_trader_orbs(): void
    {
        $task = TradingTask::factory()->create(['status_trade' => 'recalling']);
        $response = $this->put(route('trading-tasks.update', $task), [
            'dragon_id' => $task->dragon_id,
            'trader_id' => $task->trader_id,
            'reciever_id' => $task->reciever_id,
            'jumlah_orb' => 10,
            'status_trade' => 'done',
        ]);

        $response->assertSessionHasErrors('jumlah_orb');
        $this->assertDatabaseHas('trading_tasks', ['id' => $task->id, 'status_trade' => 'recalling']);
    }
}
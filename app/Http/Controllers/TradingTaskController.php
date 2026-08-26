<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\OrbOwning;
use App\Models\TradingTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TradingTaskController extends Controller
{
    public function index()
    {
        $tradingTasks = TradingTask::with(['dragon', 'trader', 'reciever'])
            ->latest()
            ->paginate(15);

        return view('trading-tasks.index', compact('tradingTasks'));
    }

    public function create()
    {
        $dragons = Dragon::orderBy('dragon_name')->get();

        return view('trading-tasks.create', [
            'accounts' => Account::orderBy('account_name')->get(),
            'dragons' => $dragons,
            'dragonData' => $this->dragonAutocompleteData($dragons),
            'statuses' => TradingTask::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateTask($request);

        DB::transaction(function () use ($validated) {
            $task = TradingTask::create($validated);

            if ($task->status_trade === 'done') {
                $this->transferOrbs($task->trader_id, $task->reciever_id, $task->dragon_id, $task->jumlah_orb);
            }
        });

        return redirect()->route('trading-tasks.index')->with('success', 'Trading task created successfully.');
    }

    public function edit(TradingTask $tradingTask)
    {
        $dragons = Dragon::orderBy('dragon_name')->get();

        return view('trading-tasks.edit', [
            'tradingTask' => $tradingTask,
            'accounts' => Account::orderBy('account_name')->get(),
            'dragons' => $dragons,
            'dragonData' => $this->dragonAutocompleteData($dragons),
            'statuses' => TradingTask::STATUSES,
        ]);
    }

    public function update(Request $request, TradingTask $tradingTask)
    {
        $validated = $this->validateTask($request);

        DB::transaction(function () use ($validated, $tradingTask) {
            if ($tradingTask->status_trade === 'done') {
                $this->transferOrbs($tradingTask->reciever_id, $tradingTask->trader_id, $tradingTask->dragon_id, $tradingTask->jumlah_orb);
            }

            $tradingTask->update($validated);

            if ($tradingTask->status_trade === 'done') {
                $this->transferOrbs($tradingTask->trader_id, $tradingTask->reciever_id, $tradingTask->dragon_id, $tradingTask->jumlah_orb);
            }
        });

        return redirect()->route('trading-tasks.index')->with('success', 'Trading task updated successfully.');
    }

    public function destroy(TradingTask $tradingTask)
    {
        DB::transaction(function () use ($tradingTask) {
            if ($tradingTask->status_trade === 'done') {
                $this->transferOrbs($tradingTask->reciever_id, $tradingTask->trader_id, $tradingTask->dragon_id, $tradingTask->jumlah_orb);
            }

            $tradingTask->delete();
        });

        return redirect()->route('trading-tasks.index')->with('success', 'Trading task deleted successfully.');
    }

    private function validateTask(Request $request): array
    {
        $validated = $request->validate([
            'dragon_id' => ['required', 'exists:dragons,id'],
            'trader_id' => ['required', 'exists:accounts,id', 'different:reciever_id'],
            'reciever_id' => ['required', 'exists:accounts,id'],
            'jumlah_orb' => ['required', 'integer', 'min:1'],
            'status_trade' => ['required', 'in:' . implode(',', TradingTask::STATUSES)],
        ]);

        return $validated;
    }

    private function dragonAutocompleteData($dragons): array
    {
        return $dragons->map(function ($dragon) {
            return [
                'id' => $dragon->id,
                'name' => $dragon->dragon_name,
                'dragon_book' => $dragon->dragon_book,
            ];
        })->values()->all();
    }

    private function transferOrbs(int $fromAccountId, int $toAccountId, int $dragonId, int $amount): void
    {
        $source = OrbOwning::where('account_id', $fromAccountId)
            ->where('dragon_id', $dragonId)
            ->lockForUpdate()
            ->first();

        if (!$source || $source->jumlah_orb < $amount) {
            throw ValidationException::withMessages([
                'jumlah_orb' => 'Saldo orb trader tidak mencukupi untuk menyelesaikan trade ini.',
            ]);
        }

        $source->decrement('jumlah_orb', $amount);

        $destination = OrbOwning::where('account_id', $toAccountId)
            ->where('dragon_id', $dragonId)
            ->lockForUpdate()
            ->first();

        if ($destination) {
            $destination->increment('jumlah_orb', $amount);
        } else {
            OrbOwning::create([
                'account_id' => $toAccountId,
                'dragon_id' => $dragonId,
                'jumlah_orb' => $amount,
            ]);
        }
    }
}
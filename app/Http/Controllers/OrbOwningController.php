<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\OrbOwning;
use App\Models\Rarity;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrbOwningController extends Controller
{
    public function index(Request $request)
    {
        $selectedAccountId = $request->query('account_id');
        $selectedRarityId = $request->query('rarity_id');
        $selectedOwnershipStatus = $request->query('owned_status');
        $accounts = Account::orderBy('account_name')->get();
        $rarities = Rarity::orderBy('name')->get();

        $dragons = Dragon::select([
                'dragons.id',
                'dragons.dragon_name',
                'dragons.summon_time',
                'rarities.name as rarity_name',
                DB::raw('COALESCE(orb_ownings.jumlah_orb, 0) as jumlah_orb'),
                DB::raw('CASE WHEN dragon_owning_details.id IS NOT NULL THEN 1 ELSE 0 END as owned')
            ])
            ->leftJoin('rarities', 'dragons.rarity_id', '=', 'rarities.id')
            ->leftJoin('orb_ownings', function ($join) use ($selectedAccountId) {
                $join->on('dragons.id', '=', 'orb_ownings.dragon_id');

                if ($selectedAccountId) {
                    $join->where('orb_ownings.account_id', '=', $selectedAccountId);
                } else {
                    $join->whereRaw('1 = 0');
                }
            })
            ->leftJoin('dragon_owning_details', function ($join) use ($selectedAccountId) {
                $join->on('dragons.id', '=', 'dragon_owning_details.dragon_id');

                if ($selectedAccountId) {
                    $join->where('dragon_owning_details.account_id', '=', $selectedAccountId);
                } else {
                    $join->whereRaw('1 = 0');
                }
            })
            ->when($selectedRarityId, function ($query) use ($selectedRarityId) {
                $query->where('dragons.rarity_id', $selectedRarityId);
            })
            ->when($selectedOwnershipStatus === 'owned', function ($query) {
                $query->whereNotNull('dragon_owning_details.id');
            })
            ->when($selectedOwnershipStatus === 'not_owned', function ($query) {
                $query->whereNull('dragon_owning_details.id');
            })
            ->orderBy('dragons.dragon_name')
            ->get();

        return view('orb-ownings.index', compact('dragons', 'accounts', 'rarities', 'selectedAccountId', 'selectedRarityId', 'selectedOwnershipStatus'));
    }

    public function create()
    {
        $accounts = Account::all();
        $dragons = Dragon::all();

        return view('orb-ownings.create', compact('accounts', 'dragons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'dragon_id' => 'required|exists:dragons,id',
            'jumlah_orb' => 'required|integer|min:0',
        ]);

        OrbOwning::create($request->all());

        return redirect()->route('orb-ownings.index')->with('success', 'Orb owning entry created successfully.');
    }

    public function storeBestHeroic(Request $request)
    {
        $bestHeroicDragonIds = Dragon::where('is_best_heroic', true)->pluck('id');

        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'orbs' => 'required|array',
            'orbs.*' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $bestHeroicDragonIds) {
            foreach ($bestHeroicDragonIds as $dragonId) {
                OrbOwning::updateOrCreate(
                    [
                        'account_id' => $validated['account_id'],
                        'dragon_id' => $dragonId,
                    ],
                    ['jumlah_orb' => $validated['orbs'][$dragonId] ?? 0]
                );
            }
        });

        return redirect()->route('home')->with('success', 'Best Heroic orbs saved successfully.');
    }

    public function edit(OrbOwning $orbOwning)
    {
        $accounts = Account::all();
        $dragons = Dragon::all();

        return view('orb-ownings.edit', compact('orbOwning', 'accounts', 'dragons'));
    }

    public function update(Request $request, OrbOwning $orbOwning)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'dragon_id' => 'required|exists:dragons,id',
            'jumlah_orb' => 'required|integer|min:0',
        ]);

        $orbOwning->update($request->all());

        return redirect()->route('orb-ownings.index')->with('success', 'Orb owning entry updated successfully.');
    }

    public function destroy(OrbOwning $orbOwning)
    {
        $orbOwning->delete();

        return redirect()->route('orb-ownings.index')->with('success', 'Orb owning entry deleted successfully.');
    }

    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'dragon_id' => 'required|exists:dragons,id',
            'jumlah_orb' => 'required|integer|min:0',
        ]);

        $orbOwning = OrbOwning::updateOrCreate(
            [
                'account_id' => $validated['account_id'],
                'dragon_id' => $validated['dragon_id'],
            ],
            ['jumlah_orb' => $validated['jumlah_orb']]
        );

        return response()->json([
            'success' => true,
            'message' => 'Orb owning updated successfully.',
            'data' => [
                'id' => $orbOwning->id,
                'jumlah_orb' => $orbOwning->jumlah_orb,
            ],
        ]);
    }
}

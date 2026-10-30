<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Rarity;
use App\Models\TradingTask;
use App\Models\UnownedDragon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnownedDragonController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'rarity' => ['nullable', 'integer', 'exists:rarities,id'],
            'is_rescue' => ['nullable', 'in:0,1'],
            'is_collection' => ['nullable', 'in:0,1'],
        ]);
        $rarities = Rarity::orderBy('name')->get();
        $accounts = Account::orderBy('account_name')->get();
        $accountIds = $accounts->pluck('id')->all();
        $orbCounts = DB::table('orb_ownings')
            ->whereIn('account_id', $accountIds)
            ->get(['account_id', 'dragon_id', 'jumlah_orb'])
            ->groupBy('dragon_id');

        $unownedDragons = UnownedDragon::with([
            'dragon.rarity',
            'dragon.element1',
            'dragon.element2',
            'dragon.element3',
            'dragon.element4',
        ])
            ->whereNotIn('dragon_id', TradingTask::select('dragon_id'))
            ->whereNotIn('dragon_id', function ($query) {
                $query->select('dragon_id')
                    ->from('orb_ownings')
                    ->groupBy('dragon_id')
                    ->havingRaw('SUM(jumlah_orb) > ?', [79]);
            })
            ->whereHas('dragon', function (Builder $query) use ($filters) {
                $query
                    ->when(isset($filters['rarity']), fn (Builder $query) => $query->where('rarity_id', $filters['rarity']))
                    ->when(isset($filters['is_rescue']), fn (Builder $query) => $query->where('is_rescue', $filters['is_rescue']))
                    ->when(isset($filters['is_collection']), fn (Builder $query) => $query->where('is_collection', $filters['is_collection']));
            })
            ->orderBy('dragon_id')
            ->paginate(12)
            ->withQueryString();

        $unownedDragons->getCollection()->transform(function ($unownedDragon) use ($accounts, $orbCounts) {
            $dragon = $unownedDragon->dragon;
            $dragonOrbRecords = collect($orbCounts->get($dragon->id, []));
            $dragon->orb_details_by_account = $accounts
                ->map(function ($account) use ($dragonOrbRecords) {
                    $record = $dragonOrbRecords->firstWhere('account_id', $account->id);

                    return [
                        'account_id' => $account->id,
                        'account_name' => $account->account_name,
                        'jumlah_orb' => (int) ($record->jumlah_orb ?? 0),
                    ];
                })
                ->values()
                ->all();

            return $unownedDragon;
        });

        return view('unowned-dragons.index', [
            'unownedDragons' => $unownedDragons,
            'rarities' => $rarities,
            'selectedRarity' => $filters['rarity'] ?? '',
            'selectedIsRescue' => $filters['is_rescue'] ?? '',
            'selectedIsCollection' => $filters['is_collection'] ?? '',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\Rarity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::with('orbOwnings.dragon')
            ->orderBy('account_name')
            ->get();

        $bestHighDragons = Dragon::where('is_best_heroic', true)
            ->orderBy('dragon_name')
            ->get(['id', 'alias', 'dragon_name']);

        $rarityOrder = ['Heroic', 'Mythical', 'Legendary', 'Epic', 'Very Rare', 'Rare', 'Common'];
        $defaultRarityColors = [
            'Heroic' => '#F4BE38',
            'Mythical' => '#FA699E',
            'Legendary' => '#792CA2',
            'Epic' => '#FFCC4D',
            'Very Rare' => '#66BB6A',
            'Rare' => '#FFCA88',
            'Common' => '#F3F6F4',
        ];
        $raritiesByName = Rarity::whereIn('name', $rarityOrder)->get()->keyBy('name');

        $ownershipByDragon = DB::table('dragon_owning_details')
            ->select('dragon_id')
            ->selectRaw('MAX(CASE WHEN account_id = ? THEN 1 ELSE 0 END) AS owned_by_account_one', [1])
            ->selectRaw('MAX(CASE WHEN account_id <> ? THEN 1 ELSE 0 END) AS owned_by_other_account', [1])
            ->groupBy('dragon_id');

        $dragonCountsByRarity = DB::table('dragons')
            ->leftJoinSub($ownershipByDragon, 'ownership', function ($join) {
                $join->on('dragons.id', '=', 'ownership.dragon_id');
            })
            ->select('dragons.rarity_id')
            ->selectRaw('COUNT(*) AS total_dragons')
            ->selectRaw('SUM(CASE WHEN COALESCE(ownership.owned_by_account_one, 0) = 1 THEN 1 ELSE 0 END) AS owned_dragons')
            ->selectRaw('SUM(CASE WHEN COALESCE(ownership.owned_by_account_one, 0) = 0 AND COALESCE(ownership.owned_by_other_account, 0) = 1 THEN 1 ELSE 0 END) AS ongoing_dragons')
            ->selectRaw('SUM(CASE WHEN COALESCE(ownership.owned_by_account_one, 0) = 0 AND COALESCE(ownership.owned_by_other_account, 0) = 0 THEN 1 ELSE 0 END) AS soon_dragons')
            ->groupBy('dragons.rarity_id')
            ->get()
            ->keyBy('rarity_id');

        $rarities = collect($rarityOrder)->map(function ($name) use ($raritiesByName, $dragonCountsByRarity, $defaultRarityColors) {
            $rarity = $raritiesByName->get($name);
            $counts = $rarity ? $dragonCountsByRarity->get($rarity->id) : null;
            $color = trim((string) ($rarity?->color ?? ''));

            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                $color = $defaultRarityColors[$name] ?? '#6c757d';
            }

            $rgb = array_map('hexdec', str_split(substr($color, 1), 2));
            $textColor = ($rgb[0] * 299 + $rgb[1] * 587 + $rgb[2] * 114) / 1000 >= 140
                ? '#212529'
                : '#ffffff';

            return [
                'id' => $rarity?->id,
                'name' => $name,
                'color' => $color,
                'text_color' => $textColor,
                'total_dragons' => (int) ($counts->total_dragons ?? 0),
                'owned_dragons' => (int) ($counts->owned_dragons ?? 0),
                'ongoing_dragons' => (int) ($counts->ongoing_dragons ?? 0),
                'soon_dragons' => (int) ($counts->soon_dragons ?? 0),
            ];
        });

        return view('home', compact('accounts', 'bestHighDragons', 'rarities'));
    }

    public function rarityDetails(int $rarityId, string $status)
    {
        $statusLabels = [
            'owned_dragons' => 'Dimiliki',
            'ongoing_dragons' => 'On Going',
            'soon_dragons' => 'Soon',
        ];

        abort_unless(array_key_exists($status, $statusLabels), 404);

        $rarity = Rarity::findOrFail($rarityId);
        $dragonsQuery = Dragon::query()
            ->where('rarity_id', $rarity->id)
            ->with('dragonOwningDetails.account')
            ->withSum([
                'orbOwnings as account_one_orb_total' => function ($query) {
                    $query->where('account_id', 1);
                },
            ], 'jumlah_orb');

        if ($status === 'owned_dragons') {
            $dragonsQuery->whereHas('dragonOwningDetails', function ($query) {
                $query->where('account_id', 1);
            });
        } elseif ($status === 'ongoing_dragons') {
            $dragonsQuery
                ->whereHas('dragonOwningDetails', function ($query) {
                    $query->where('account_id', '<>', 1);
                })
                ->whereDoesntHave('dragonOwningDetails', function ($query) {
                    $query->where('account_id', 1);
                });
        } else {
            $dragonsQuery->whereDoesntHave('dragonOwningDetails');
        }

        $dragons = $dragonsQuery->orderBy('dragon_name')->get();

        if ($status === 'ongoing_dragons' && $dragons->isNotEmpty()) {
            $ownerAccountIds = $dragons
                ->flatMap(fn ($dragon) => $dragon->dragonOwningDetails->pluck('account_id'))
                ->unique()
                ->values();

            $ownerOrbCounts = DB::table('orb_ownings')
                ->whereIn('dragon_id', $dragons->modelKeys())
                ->whereIn('account_id', $ownerAccountIds)
                ->get(['dragon_id', 'account_id', 'jumlah_orb'])
                ->keyBy(fn ($orbOwning) => $orbOwning->dragon_id . ':' . $orbOwning->account_id);

            foreach ($dragons as $dragon) {
                foreach ($dragon->dragonOwningDetails as $owningDetail) {
                    $orbOwning = $ownerOrbCounts->get($dragon->id . ':' . $owningDetail->account_id);
                    $owningDetail->setAttribute('owner_orb_total', (int) ($orbOwning->jumlah_orb ?? 0));
                }
            }
        }

        return view('rarities.dashboard-details', compact('rarity', 'status', 'statusLabels', 'dragons'));
    }

    public function runSeeder(Request $request)
    {
        $request->validate([
            'seed' => 'required|string',
        ]);

        $seed = trim($request->input('seed'));

        $seeders = [];

        if ($seed === 'all') {
            $seeders = ['AccountSeeder', 'ElementSeeder', 'RaritySeeder', 'BestHeroicSeeder'];
        } else {
            $class = $seed;
            if (!str_ends_with($class, 'Seeder')) {
                $class = $class . 'Seeder';
            }

            $seeders = [$class];
        }

        foreach ($seeders as $class) {
            $fullClass = 'Database\\Seeders\\' . $class;

            if (!class_exists($fullClass)) {
                return redirect()->route('home')->with('seed_status', 'Seeder ' . $class . ' tidak ditemukan.');
            }

            Artisan::call('db:seed', ['--class' => $class]);
        }

        $message = $seed === 'all'
            ? 'Semua seeder dijalankan.'
            : 'Seeder ' . $seeders[0] . ' dijalankan.';

        return redirect()->route('home')->with('seed_status', $message);
    }
}

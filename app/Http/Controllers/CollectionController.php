<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Dragon;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $collections = Collection::with('dragons')
            ->when(mb_strlen($search) >= 3, function ($query) use ($search) {
                $pattern = '%' . Str::lower($search) . '%';

                $query->whereHas('dragons', function ($dragonQuery) use ($pattern) {
                    $dragonQuery->whereRaw('LOWER(dragon_name) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(dragon_book) LIKE ?', [$pattern]);
                });
            })
            ->orderBy('id')
            ->get();

        return view('collections.index', compact('collections', 'search'));
    }

    public function calculateAchievement()
    {
        $ownedDragonIds = DB::table('dragon_owning_details')
            ->where('account_id', 1)
            ->pluck('dragon_id');

        DB::transaction(function () use ($ownedDragonIds) {
            Collection::with('dragons:id')
                ->get()
                ->each(function (Collection $collection) use ($ownedDragonIds) {
                    $totalDragons = $collection->dragons->count();
                    $ownedDragons = $collection->dragons->whereIn('id', $ownedDragonIds)->count();
                    $achievement = $totalDragons === 0 ? 0 : ($ownedDragons / $totalDragons) * 100;

                    $collection->update(['achievement' => $achievement]);
                });
        });

        return redirect()
            ->route('collections.index')
            ->with('success', 'Achievement berhasil dihitung dari account ID 1.');
    }

    public function unownedMembers(Request $request)
    {
        $selectedRarity = $request->query('rarity');
        $selectedAccountId = $request->query('account_id');
        $selectedIsRescue = $request->query('is_rescue');
        $rarities = \App\Models\Rarity::orderBy('name')->get();
        $accounts = Account::orderBy('account_name')->get();
        $orbAccountIds = array_filter([1, (int) $selectedAccountId]);
        $orbCounts = DB::table('orb_ownings')
            ->whereIn('account_id', $orbAccountIds)
            ->get(['account_id', 'dragon_id', 'jumlah_orb'])
            ->keyBy(function ($orb) {
                return $orb->account_id . ':' . $orb->dragon_id;
            });

        $dragons = Dragon::with([
            'rarity',
            'collections',
            'dragonOwningDetails' => function ($query) use ($selectedAccountId) {
                $query->when($selectedAccountId, function ($query) use ($selectedAccountId) {
                    $query->where('account_id', $selectedAccountId);
                });
            },
        ])
            ->whereHas('collections')
            ->whereDoesntHave('dragonOwningDetails', function ($query) {
                $query->where('account_id', 1);
            })
            ->when($selectedRarity, function ($query) use ($selectedRarity) {
                $query->where('rarity_id', $selectedRarity);
            })
            ->when($selectedIsRescue !== null && $selectedIsRescue !== '', function ($query) use ($selectedIsRescue) {
                $query->where('is_rescue', (bool) $selectedIsRescue);
            })
            ->orderBy('dragon_name')
            ->paginate(24)
            ->withQueryString();

        $dragons->getCollection()->transform(function ($dragon) use ($orbCounts, $selectedAccountId) {
            $dragon->jumlah_orb_account_one = (int) ($orbCounts['1:' . $dragon->id]->jumlah_orb ?? 0);
            $dragon->jumlah_orb_selected_account = $selectedAccountId
                ? (int) ($orbCounts[(int) $selectedAccountId . ':' . $dragon->id]->jumlah_orb ?? 0)
                : 0;
            $dragon->is_owned_by_selected_account = (bool) $selectedAccountId
                && $dragon->dragonOwningDetails->isNotEmpty();

            return $dragon;
        });

        return view('collections.unowned-members', compact('dragons', 'rarities', 'selectedRarity', 'accounts', 'selectedAccountId', 'selectedIsRescue'));
    }

    public function create()
    {
        $dragons = Dragon::with('rarity', 'element1', 'element2', 'element3', 'element4')
            ->orderBy('dragon_name')
            ->get();
        return view('collections.create', compact('dragons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'collection_name' => 'required|string|max:255|unique:collections',
            'gem_reward' => 'required|integer|min:0',
            'dragon_reward_id' => 'nullable|exists:dragons,id',
        ]);

        $collection = Collection::create($validated);

        return redirect()
            ->route('collections.show', $collection)
            ->with('success', 'Collection created successfully.');
    }

    public function show(Collection $collection)
    {
        $collection->load('dragons.rarity');
        $allDragons = Dragon::with('rarity')
            ->whereDoesntHave('collections', function ($query) use ($collection) {
                $query->where('collections.id', $collection->id);
            })
            ->orderBy('dragon_name')
            ->get();
        
        return view('collections.show', compact('collection', 'allDragons'));
    }

    public function edit(Collection $collection)
    {
        $collection->load('dragons');
        $dragons = Dragon::with('rarity', 'element1', 'element2', 'element3', 'element4')
            ->whereDoesntHave('collections', function ($query) use ($collection) {
                $query->where('collections.id', $collection->id);
            })
            ->orderBy('dragon_name')
            ->get();
        $allDragons = Dragon::with('rarity')
            ->orderBy('dragon_name')
            ->get();
        
        return view('collections.edit', compact('collection', 'dragons', 'allDragons'));
    }

    public function update(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'collection_name' => 'required|string|max:255|unique:collections,collection_name,' . $collection->id,
            'gem_reward' => 'required|integer|min:0',
            'dragon_reward_id' => 'nullable|exists:dragons,id',
        ]);

        $collection->update($validated);

        return redirect()
            ->route('collections.show', $collection)
            ->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();
        return redirect()
            ->route('collections.index')
            ->with('success', 'Collection deleted successfully.');
    }

    public function addDragon(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'dragon_id' => 'required|exists:dragons,id',
        ]);

        $dragon = Dragon::findOrFail($validated['dragon_id']);
        $collection->dragons()->syncWithoutDetaching([$dragon->id]);
        $dragon->update(['is_collection' => true]);

        return redirect()
            ->route('collections.show', $collection)
            ->with('success', 'Dragon added to collection.');
    }

    public function removeDragon(Collection $collection, Dragon $dragon)
    {
        $collection->dragons()->detach($dragon->id);
        $dragon->update(['is_collection' => $dragon->collections()->exists()]);

        return redirect()
            ->route('collections.show', $collection)
            ->with('success', 'Dragon removed from collection.');
    }

    public function data(Collection $collection)
    {
        $ownedDragonIds = DB::table('dragon_owning_details')
            ->where('account_id', 1)
            ->pluck('dragon_id');

        $dragons = $collection->dragons()
            ->with('rarity')
            ->get()
            ->map(function ($dragon) use ($ownedDragonIds) {
                return [
                    'dragon_id' => $dragon->id,
                    'dragon_book' => $dragon->dragon_book ?? '-',
                    'dragon_name' => $dragon->dragon_name,
                    'rarity' => $dragon->rarity->name ?? '-',
                    'is_owned' => $ownedDragonIds->contains($dragon->id),
                ];
            });

        return response()->json([
            'data' => $dragons,
        ]);
    }

    public function dragonRewards()
    {
        $accountId = 1;
        $orbCounts = DB::table('orb_ownings')
            ->where('account_id', $accountId)
            ->pluck('jumlah_orb', 'dragon_id');
        
        $dragonRewards = Collection::with('dragonReward.rarity')
            ->whereNotNull('dragon_reward_id')
            ->get()
            ->map(function ($collection) use ($accountId, $orbCounts) {
                $ownedDragonIds = DB::table('dragon_owning_details')
                    ->where('account_id', $accountId)
                    ->pluck('dragon_id')
                    ->toArray();
                
                $dragon = $collection->dragonReward;
                $isOwned = in_array($dragon->id, $ownedDragonIds);
                $isMember = $dragon->collections()->exists();
                
                return [
                    'id' => $dragon->id,
                    'dragon_name' => $dragon->dragon_name,
                    'dragon_book' => $dragon->dragon_book ?? '-',
                    'rarity' => $dragon->rarity->name ?? '-',
                    'collection_name' => $collection->collection_name,
                    'is_owned' => $isOwned,
                    'is_member' => $isMember,
                    'jumlah_orb' => (int) ($orbCounts[$dragon->id] ?? 0),
                ];
            });

        return response()->json([
            'data' => $dragonRewards,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Dragon;
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
        $dragons = $collection->dragons()
            ->with('rarity')
            ->get()
            ->map(function ($dragon) {
                return [
                    'dragon_id' => $dragon->id,
                    'dragon_book' => $dragon->dragon_book ?? '-',
                    'dragon_name' => $dragon->dragon_name,
                    'rarity' => $dragon->rarity->name ?? '-',
                ];
            });

        return response()->json([
            'data' => $dragons,
        ]);
    }

    public function dragonRewards()
    {
        $accountId = 1;
        
        $dragonRewards = Collection::with('dragonReward.rarity')
            ->whereNotNull('dragon_reward_id')
            ->get()
            ->map(function ($collection) use ($accountId) {
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
                ];
            });

        return response()->json([
            'data' => $dragonRewards,
        ]);
    }
}

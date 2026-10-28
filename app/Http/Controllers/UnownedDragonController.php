<?php

namespace App\Http\Controllers;

use App\Models\Rarity;
use App\Models\UnownedDragon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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

        $unownedDragons = UnownedDragon::with([
            'dragon.rarity',
            'dragon.element1',
            'dragon.element2',
            'dragon.element3',
            'dragon.element4',
        ])
            ->whereHas('dragon', function (Builder $query) use ($filters) {
                $query
                    ->when(isset($filters['rarity']), fn (Builder $query) => $query->where('rarity_id', $filters['rarity']))
                    ->when(isset($filters['is_rescue']), fn (Builder $query) => $query->where('is_rescue', $filters['is_rescue']))
                    ->when(isset($filters['is_collection']), fn (Builder $query) => $query->where('is_collection', $filters['is_collection']));
            })
            ->orderBy('dragon_id')
            ->paginate(12)
            ->withQueryString();

        return view('unowned-dragons.index', [
            'unownedDragons' => $unownedDragons,
            'rarities' => $rarities,
            'selectedRarity' => $filters['rarity'] ?? '',
            'selectedIsRescue' => $filters['is_rescue'] ?? '',
            'selectedIsCollection' => $filters['is_collection'] ?? '',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\DragonOwningDetail;
use App\Models\Rarity;
use Illuminate\Http\Request;

class DragonOwningController extends Controller
{
    public function index()
    {
        $accounts = Account::with(['dragonOwningDetails.dragon.rarity', 'dragonOwningDetails.dragon.element1', 'dragonOwningDetails.dragon.element2', 'dragonOwningDetails.dragon.element3', 'dragonOwningDetails.dragon.element4'])->latest()->get();

        return view('dragon-ownings.index', compact('accounts'));
    }

    public function create(Account $account)
    {
        $dragons = Dragon::query()
            ->orderByRaw("CASE WHEN dragon_book ~ '^[0-9]+$' THEN 1 ELSE 0 END DESC")
            ->orderByRaw("LPAD(COALESCE(NULLIF(dragon_book, ''), '0'), 10, '0')")
            ->orderBy('dragon_name')
            ->get();

        return view('dragon-ownings.create', compact('account', 'dragons'));
    }
    public function show(Account $account)
    {
        $ownedDragonIds = $account->dragonOwningDetails()->pluck('dragon_id')->toArray();

        $dragons = Dragon::query()
            ->when($ownedDragonIds, function ($query) use ($ownedDragonIds) {
                $query->whereNotIn('id', $ownedDragonIds);
            })
            ->orderByRaw("CASE WHEN dragon_book ~ '^[0-9]+' THEN 1 ELSE 0 END DESC")
            ->orderByRaw("LPAD(COALESCE(NULLIF(dragon_book, ''), '0'), 10, '0')")
            ->orderBy('dragon_name')
            ->get();

        $rarities = Rarity::orderBy('name')->get();

        return view('dragon-ownings.show', compact('account', 'dragons', 'rarities'));
    }

    public function data(Account $account)
    {
        $details = DragonOwningDetail::with(['dragon.rarity'])
            ->where('account_id', $account->id)
            ->get();

        $data = $details->map(function ($detail) {
            return [
                'dragon_id' => $detail->dragon->id,
                'dragon_name' => $detail->dragon->dragon_name,
                'rarity' => $detail->dragon->rarity->name ?? '-',
                'dragon_book' => $detail->dragon->dragon_book,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function search(Account $account, Request $request)
    {
        $term = trim($request->query('term', ''));

        $ownedDragonIds = $account->dragonOwningDetails()->pluck('dragon_id')->toArray();

        $query = Dragon::query()
            ->when($ownedDragonIds, function ($query) use ($ownedDragonIds) {
                $query->whereNotIn('id', $ownedDragonIds);
            })
            ->when($term, function ($query) use ($term) {
                $search = strtolower($term);
                $query->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(dragon_name) LIKE ?', ["%{$search}%"])
                          ->orWhereRaw('LOWER(dragon_book) LIKE ?', ["%{$search}%"]);
                });
            })
            ->orderByRaw("CASE WHEN dragon_book ~ '^[0-9]+' THEN 1 ELSE 0 END DESC")
            ->orderByRaw("LPAD(COALESCE(NULLIF(dragon_book, ''), '0'), 10, '0')")
            ->orderBy('dragon_name')
            ->limit(30)
            ->get();

        $results = $query->map(function ($dragon) {
            return [
                'id' => $dragon->id,
                'value' => trim($dragon->dragon_book . ' - ' . $dragon->dragon_name . ($dragon->rarity? ' ('. $dragon->rarity->name .')' : '')),
            ];
        });

        return response()->json($results);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Dragon;
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
        $dragons = Dragon::all();

        return view('dragon-ownings.create', compact('account', 'dragons'));
    }
}

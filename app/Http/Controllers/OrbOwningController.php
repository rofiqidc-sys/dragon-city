<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\OrbOwning;
use Illuminate\Http\Request;

class OrbOwningController extends Controller
{
    public function index()
    {
        $orbOwnings = OrbOwning::with(['dragon.rarity', 'dragon.element1', 'dragon.element2', 'dragon.element3', 'dragon.element4', 'account'])
            ->latest()
            ->get();

        return view('orb-ownings.index', compact('orbOwnings'));
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
}

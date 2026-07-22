<?php

namespace App\Http\Controllers;

use App\Models\Rarity;
use Illuminate\Http\Request;

class RarityController extends Controller
{
    public function index()
    {
        $rarities = Rarity::latest()->get();

        return view('rarities.index', compact('rarities'));
    }

    public function create()
    {
        return view('rarities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'key_need_to_summon' => 'nullable|string|max:255',
        ]);

        Rarity::create($request->all());

        return redirect()->route('rarities.index')->with('success', 'Rarity created successfully.');
    }

    public function edit(Rarity $rarity)
    {
        return view('rarities.edit', compact('rarity'));
    }

    public function update(Request $request, Rarity $rarity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'key_need_to_summon' => 'nullable|string|max:255',
        ]);

        $rarity->update($request->all());

        return redirect()->route('rarities.index')->with('success', 'Rarity updated successfully.');
    }

    public function destroy(Rarity $rarity)
    {
        $rarity->delete();

        return redirect()->route('rarities.index')->with('success', 'Rarity deleted successfully.');
    }
}

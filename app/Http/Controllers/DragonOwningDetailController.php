<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Dragon;
use App\Models\DragonOwningDetail;
use Illuminate\Http\Request;

class DragonOwningDetailController extends Controller
{
    public function store(Request $request, Account $account)
    {
        $request->validate([
            'dragon_id' => 'required|exists:dragons,id',
        ]);

        $existingDragon = DragonOwningDetail::where('account_id', $account->id)
            ->where('dragon_id', $request->dragon_id)
            ->first();

        if ($existingDragon) {
            return redirect()->route('dragon-ownings.index')->with('warning', 'Dragon already assigned to this account.');
        }

        DragonOwningDetail::create([
            'account_id' => $account->id,
            'dragon_id' => $request->dragon_id,
        ]);

        return redirect()->route('dragon-ownings.index')->with('success', 'Dragon added to account successfully.');
    }

    public function destroy(Account $account, DragonOwningDetail $dragonOwningDetail)
    {
        if ($dragonOwningDetail->account_id !== $account->id) {
            abort(403);
        }

        $dragonOwningDetail->delete();

        return redirect()->route('dragon-ownings.index')->with('success', 'Dragon removed from account successfully.');
    }
}

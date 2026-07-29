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

        $dragon = Dragon::findOrFail($request->dragon_id);

        $existingDragon = DragonOwningDetail::where('account_id', $account->id)
            ->where('dragon_id', $request->dragon_id)
            ->first();

        if ($existingDragon) {
            $message = "Dragon '{$dragon->dragon_name}' is already assigned to this account.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->route('dragon-ownings.show', $account)->with('warning', $message);
        }

        DragonOwningDetail::create([
            'account_id' => $account->id,
            'dragon_id' => $request->dragon_id,
        ]);

        $message = "Dragon '{$dragon->dragon_name}' added to account successfully.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'dragon_id' => $dragon->id,
                    'dragon_book' => $dragon->dragon_book,
                    'dragon_name' => $dragon->dragon_name,
                    'rarity' => $dragon->rarity->name ?? '-',
                ],
            ]);
        }

        return redirect()->route('dragon-ownings.show', $account)->with('success', $message);
    }

    public function destroy(Account $account, DragonOwningDetail $dragonOwningDetail)
    {
        if ($dragonOwningDetail->account_id !== $account->id) {
            abort(403);
        }

        $dragonName = $dragonOwningDetail->dragon->dragon_name;
        $dragonOwningDetail->delete();

        return redirect()->route('dragon-ownings.index')->with('success', "Dragon '{$dragonName}' removed from account successfully.");
    }
}

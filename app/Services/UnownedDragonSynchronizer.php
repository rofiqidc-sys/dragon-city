<?php

namespace App\Services;

use App\Models\DragonOwningDetail;
use App\Models\UnownedDragon;
use Illuminate\Support\Facades\DB;

class UnownedDragonSynchronizer
{
    public static function sync(int $dragonId): void
    {
        if (DragonOwningDetail::where('dragon_id', $dragonId)->exists()) {
            UnownedDragon::where('dragon_id', $dragonId)->delete();

            return;
        }

        UnownedDragon::firstOrCreate(['dragon_id' => $dragonId]);
    }

    public static function syncAll(): void
    {
        DB::table('unowned_dragons')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('dragons')
                    ->whereColumn('dragons.id', 'unowned_dragons.dragon_id');
            })
            ->orWhereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('dragon_owning_details')
                    ->whereColumn('dragon_owning_details.dragon_id', 'unowned_dragons.dragon_id');
            })
            ->delete();

        DB::table('unowned_dragons')->insertUsing(
            ['dragon_id', 'created_at', 'updated_at'],
            DB::table('dragons')
                ->select('dragons.id', DB::raw('CURRENT_TIMESTAMP'), DB::raw('CURRENT_TIMESTAMP'))
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('dragon_owning_details')
                        ->whereColumn('dragon_owning_details.dragon_id', 'dragons.id');
                })
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('unowned_dragons')
                        ->whereColumn('unowned_dragons.dragon_id', 'dragons.id');
                })
        );
    }
}

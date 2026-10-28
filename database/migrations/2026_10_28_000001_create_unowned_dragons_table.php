<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unowned_dragons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dragon_id')->unique()->constrained('dragons')->cascadeOnDelete();
            $table->timestamps();
        });

        DB::table('unowned_dragons')->insertUsing(
            ['dragon_id', 'created_at', 'updated_at'],
            DB::table('dragons')
                ->select('dragons.id', DB::raw('CURRENT_TIMESTAMP'), DB::raw('CURRENT_TIMESTAMP'))
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('dragon_owning_details')
                        ->whereColumn('dragon_owning_details.dragon_id', 'dragons.id');
                })
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('unowned_dragons');
    }
};

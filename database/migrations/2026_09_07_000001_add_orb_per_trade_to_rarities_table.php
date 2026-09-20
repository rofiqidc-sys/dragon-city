<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rarities', function (Blueprint $table) {
            $table->unsignedInteger('orb_per_trade')->nullable()->after('key_need_to_summon');
        });
    }

    public function down(): void
    {
        Schema::table('rarities', function (Blueprint $table) {
            $table->dropColumn('orb_per_trade');
        });
    }
};
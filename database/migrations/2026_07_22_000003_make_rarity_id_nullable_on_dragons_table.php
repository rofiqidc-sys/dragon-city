<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dragons', function (Blueprint $table) {
            $table->dropForeign(['rarity_id']);
            $table->unsignedBigInteger('rarity_id')->nullable()->change();
            $table->foreign('rarity_id')->references('id')->on('rarities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dragons', function (Blueprint $table) {
            $table->dropForeign(['rarity_id']);
            $table->unsignedBigInteger('rarity_id')->nullable(false)->change();
            $table->foreign('rarity_id')->references('id')->on('rarities')->cascadeOnDelete();
        });
    }
};
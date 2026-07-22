<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dragons', function (Blueprint $table) {
            $table->id();
            $table->string('dragon_name');
            $table->foreignId('rarity_id')->constrained('rarities')->onDelete('cascade');
            $table->foreignId('element_1_id')->constrained('elements')->onDelete('cascade');
            $table->foreignId('element_2_id')->nullable()->constrained('elements')->onDelete('set null');
            $table->foreignId('element_3_id')->nullable()->constrained('elements')->onDelete('set null');
            $table->foreignId('element_4_id')->nullable()->constrained('elements')->onDelete('set null');
            $table->integer('summon_time')->nullable();
            $table->integer('orb_to_summon')->default(0);
            $table->integer('hatching_time')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dragons');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_dragon_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->foreignId('dragon_id')->constrained('dragons')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['collection_id', 'dragon_id']);
        });

        DB::table('dragons')
            ->whereNotNull('collection_id')
            ->get(['collection_id', 'id', 'created_at', 'updated_at'])
            ->each(function ($dragon) {
                DB::table('collection_dragon_members')->insert([
                    'collection_id' => $dragon->collection_id,
                    'dragon_id' => $dragon->id,
                    'created_at' => $dragon->created_at ?? now(),
                    'updated_at' => $dragon->updated_at ?? now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_dragon_members');
    }
};
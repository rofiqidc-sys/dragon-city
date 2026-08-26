<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('collection_name');
            $table->timestamps();
        });

        Schema::create('collection_dragons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('collections')->onDelete('cascade');
            $table->foreignId('dragon_id')->constrained('dragons')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['collection_id', 'dragon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_dragons');
        Schema::dropIfExists('collections');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dragon_owning_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->foreignId('dragon_id')->constrained('dragons')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['account_id', 'dragon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dragon_owning_details');
    }
};

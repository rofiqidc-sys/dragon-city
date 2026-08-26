<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trading_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dragon_id')->constrained('dragons')->cascadeOnDelete();
            $table->foreignId('trader_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('reciever_id')->constrained('accounts')->cascadeOnDelete();
            $table->unsignedInteger('jumlah_orb');
            $table->string('status_trade')->default('recalling');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_tasks');
    }
};
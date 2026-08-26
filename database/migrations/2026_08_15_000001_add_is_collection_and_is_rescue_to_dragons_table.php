<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dragons', function (Blueprint $table) {
            $table->boolean('is_collection')->default(false)->after('is_best_heroic');
            $table->boolean('is_rescue')->default(false)->after('is_collection');
        });
    }

    public function down(): void
    {
        Schema::table('dragons', function (Blueprint $table) {
            $table->dropColumn(['is_collection', 'is_rescue']);
        });
    }
};

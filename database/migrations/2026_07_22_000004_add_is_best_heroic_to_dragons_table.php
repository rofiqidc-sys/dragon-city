<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dragons', function (Blueprint $table) {
            $table->boolean('is_best_heroic')->nullable()->after('hatching_time');
        });
    }

    public function down(): void
    {
        Schema::table('dragons', function (Blueprint $table) {
            $table->dropColumn('is_best_heroic');
        });
    }
};

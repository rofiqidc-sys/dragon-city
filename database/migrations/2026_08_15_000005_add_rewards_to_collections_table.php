<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->integer('gem_reward')->default(0)->after('collection_name');
            $table->foreignId('dragon_reward_id')->nullable()->constrained('dragons')->onDelete('set null')->after('gem_reward');
        });
    }

    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropForeignKey(['dragon_reward_id']);
            $table->dropColumn(['gem_reward', 'dragon_reward_id']);
        });
    }
};

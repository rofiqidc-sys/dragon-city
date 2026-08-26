<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dragons', function (Blueprint $table) {
            $table->foreignId('collection_id')->nullable()->constrained('collections')->onDelete('set null')->after('is_rescue');
        });
    }

    public function down(): void
    {
        Schema::table('dragons', function (Blueprint $table) {
            $table->dropForeignKey(['collection_id']);
            $table->dropColumn('collection_id');
        });
    }
};

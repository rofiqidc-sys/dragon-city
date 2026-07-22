<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dragons', function (Blueprint $table) {
            if (!Schema::hasColumn('dragons', 'dragon_book')) {
                $table->string('dragon_book')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dragons', function (Blueprint $table) {
            if (Schema::hasColumn('dragons', 'dragon_book')) {
                $table->dropColumn('dragon_book');
            }
        });
    }
};

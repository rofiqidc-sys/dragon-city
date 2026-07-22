<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dragons', function (Blueprint $table) {
            if (!Schema::hasColumn('dragons', 'alias')) {
                $table->string('alias', 7)->nullable()->unique()->after('dragon_book');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dragons', function (Blueprint $table) {
            if (Schema::hasColumn('dragons', 'alias')) {
                $table->dropUnique('dragons_alias_unique');
                $table->dropColumn('alias');
            }
        });
    }
};
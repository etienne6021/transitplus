<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('entrees', function (Blueprint $table) {
            if (!Schema::hasColumn('entrees', 'agency_id')) {
                $table->foreignId('agency_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
        });

        Schema::table('sorties', function (Blueprint $table) {
            if (!Schema::hasColumn('sorties', 'agency_id')) {
                $table->foreignId('agency_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrees', function (Blueprint $table) {
            $table->dropForeign(['agency_id']);
            $table->dropColumn('agency_id');
        });

        Schema::table('sorties', function (Blueprint $table) {
            $table->dropForeign(['agency_id']);
            $table->dropColumn('agency_id');
        });
    }
};

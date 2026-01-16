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
        Schema::table('declarations', function (Blueprint $table) {
            $table->decimal('poids_total', 12, 3)->nullable();
            $table->integer('colis_total')->nullable();
            $table->string('manifest_num')->nullable();
            $table->string('bl_num')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('declarations', function (Blueprint $table) {
            //
        });
    }
};

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
        Schema::table('prospects', function (Blueprint $table) {
            $table->string('source')->nullable(); // Recommandation, Terrain (Port), Web, Publicité
            $table->string('priority')->default('Normal'); // Basse, Normal, Haute, Urgent
            $table->text('full_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            //
        });
    }
};

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
        Schema::create('entrees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained()->cascadeOnDelete();
            $table->string('reference_mad')->unique();
            $table->date('date_arrivee');
            $table->string('provenance');
            $table->string('conducteur')->nullable();
            $table->string('statut')->default('En attente');
            $table->integer('nombre_colis')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrees');
    }
};

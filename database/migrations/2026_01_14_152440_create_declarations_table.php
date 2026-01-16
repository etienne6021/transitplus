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
        Schema::create('declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained()->cascadeOnDelete();
            $table->string('numero_sydonia')->unique()->nullable();
            $table->date('date_sydonia')->nullable();
            $table->string('regime')->nullable();
            $table->string('circuit')->nullable(); // Vert, Jaune, Bleu, Rouge
            $table->string('bureau')->nullable(); // Ex: Lomé Port
            $table->decimal('valeur_douane', 15, 2)->nullable();
            $table->decimal('droits_douane', 15, 2)->nullable();
            $table->string('statut')->default('En attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declarations');
    }
};

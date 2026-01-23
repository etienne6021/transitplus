<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            $table->string('title');
            $table->text('description')->nullable();
            
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            
            $table->string('status')->default('En attente'); // En attente, Terminé, Annulé
            $table->string('color')->nullable(); // Pour l'affichage visuel
            
            $table->string('category')->nullable(); // Réunion, Appel, Visite, Tâche
            $table->boolean('is_public')->default(false); // Si partagé avec l'agence
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_items');
    }
};

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
        Schema::create('sorties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entree_id')->constrained()->cascadeOnDelete();
            $table->string('reference_sortie')->unique();
            $table->date('date_sortie');
            $table->integer('nombre_colis_sortis');
            $table->string('receptionnaire')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sorties');
    }
};

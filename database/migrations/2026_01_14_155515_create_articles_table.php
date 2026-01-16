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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declaration_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('hscode')->nullable(); // Position tarifaire
            $table->decimal('quantity', 15, 2)->default(0);
            $table->string('unit')->nullable(); // Sacs, Tonnes, Unités
            $table->decimal('weight', 15, 2)->nullable();
            $table->decimal('value', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};

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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained()->cascadeOnDelete();
            $table->string('number')->unique();
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal_honoraires', 15, 2)->default(0);
            $table->decimal('subtotal_debours', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0); // TVA au Togo (18%)
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('Brouillon'); // Brouillon, Envoyée, Payée, Annulée
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // Provision, Débours, Honoraire
            $table->string('category')->nullable(); // PAL, LCT, TBS, OTR, Douane
            $table->string('label');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};

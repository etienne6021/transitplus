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
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->date('due_date')->nullable();
            $table->string('party_name')->nullable();
            $table->string('status')->default('Complété'); // Brouillon, Complété, En attente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            //
        });
    }
};

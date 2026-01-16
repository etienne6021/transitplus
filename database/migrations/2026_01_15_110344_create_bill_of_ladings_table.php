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
        Schema::create('bill_of_ladings', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('dossier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ship_id')->nullable()->constrained()->nullOnDelete();
            $table->date('etd')->nullable(); // Estimated Time of Departure
            $table->date('eta')->nullable(); // Estimated Time of Arrival
            $table->string('port_loading')->nullable();
            $table->string('port_discharge')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_of_ladings');
    }
};

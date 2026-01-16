<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customs_regimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('code')->unique(); // Ex: C100, IM4, EX1
            $table->string('label'); // Ex: Mise à la consommation
            $table->string('type')->default('Import'); // Import, Export, Transit
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customs_regimes');
    }
};

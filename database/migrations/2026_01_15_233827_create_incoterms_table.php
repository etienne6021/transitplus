<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoterms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // Ex: FOB, CIF, DDP
            $table->string('name'); // Ex: Free On Board
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoterms');
    }
};

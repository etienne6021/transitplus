<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name'); // Ex: MSC, MAERSK, CMA CGM
            $table->string('code')->nullable(); // SCAC Code
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_lines');
    }
};

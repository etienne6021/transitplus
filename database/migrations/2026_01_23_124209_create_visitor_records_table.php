<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_name');
            $table->string('company')->nullable();
            $table->string('phone')->nullable();
            $table->string('person_to_see');
            $table->string('purpose'); // Motif
            $table->dateTime('entry_time');
            $table->dateTime('exit_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_records');
    }
};

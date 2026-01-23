<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->enum('type', ['Arrivée', 'Départ']);
            $table->date('date_record');
            $table->string('sender_receiver'); // Expéditeur ou Destinataire
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('scanned_file')->nullable(); // Lien vers le PDF
            $table->string('statut')->default('Traité'); // En attente, Traité, Urgent
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_records');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->string('direction')->default('Entrée')->after('type'); // Entrée, Sortie
            $table->string('reference_number')->nullable()->after('payment_method'); // N° Chèque, Réf Virement
            $table->string('bank_name')->nullable()->after('reference_number');
            $table->string('attachment')->nullable()->after('notes'); // Justificatif
        });
    }

    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropColumn(['direction', 'reference_number', 'bank_name', 'attachment']);
        });
    }
};

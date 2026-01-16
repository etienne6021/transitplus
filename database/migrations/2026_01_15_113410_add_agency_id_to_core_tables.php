<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['clients', 'dossiers', 'invoices', 'financial_transactions', 'declarations', 'ships', 'bill_of_ladings'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'agency_id')) {
                    $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        $tables = ['clients', 'dossiers', 'invoices', 'financial_transactions', 'declarations', 'ships', 'bill_of_ladings'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['agency_id']);
                $table->dropColumn('agency_id');
            });
        }
    }
};

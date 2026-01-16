<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'financial_transactions',
            'entrees',
            'sorties',
            'leaves',
            'payrolls',
            'articles',
            'invoice_items',
            'sale_items',
            'quotation_items'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'agency_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('agency_id')->nullable()->constrained()->cascadeOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'financial_transactions',
            'entrees',
            'sorties',
            'leaves',
            'payrolls',
            'articles',
            'invoice_items',
            'sale_items',
            'quotation_items'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'agency_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['agency_id']);
                    $table->dropColumn('agency_id');
                });
            }
        }
    }
};

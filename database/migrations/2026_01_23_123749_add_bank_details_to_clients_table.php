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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('email');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_swift_bic')->nullable()->after('bank_account_number');
            $table->string('bank_address')->nullable()->after('bank_swift_bic');
            $table->text('bank_rib_details')->nullable()->after('bank_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_number', 'bank_swift_bic', 'bank_address', 'bank_rib_details']);
        });
    }
};

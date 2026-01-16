<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('cnss_number')->nullable()->after('salary');
            $table->string('account_number')->nullable()->after('cnss_number');
            $table->string('contract_type')->default('CDI')->after('account_number'); // CDI, CDD, Alternance
            $table->text('notes')->nullable()->after('contract_type');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('transport_allowance', 15, 2)->default(0)->after('bonuses');
            $table->decimal('other_allowances', 15, 2)->default(0)->after('transport_allowance');
            $table->decimal('cnss_employee', 15, 2)->default(0)->after('deductions');
            $table->decimal('cnss_employer', 15, 2)->default(0)->after('cnss_employee');
            $table->decimal('irpp', 15, 2)->default(0)->after('cnss_employer');
            $table->decimal('brut_salary', 15, 2)->default(0)->after('net_salary');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['cnss_number', 'account_number', 'contract_type', 'notes']);
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'transport_allowance', 'other_allowances', 
                'cnss_employee', 'cnss_employer', 'irpp', 'brut_salary'
            ]);
        });
    }
};

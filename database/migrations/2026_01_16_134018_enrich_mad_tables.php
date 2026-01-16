<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entrees', function (Blueprint $table) {
            $table->string('nature_marchandise')->nullable()->after('reference_mad');
            $table->integer('nb_colis_avaries')->default(0)->after('nombre_colis');
            $table->text('description_avaries')->nullable()->after('nb_colis_avaries');
            $table->string('control_officer')->nullable()->after('conducteur');
            $table->date('control_date')->nullable()->after('control_officer');
            $table->string('bordereau_entree')->nullable()->after('statut');
        });

        Schema::table('sorties', function (Blueprint $table) {
            $table->string('inspecteur_douane')->nullable()->after('receptionnaire');
            $table->string('scelle_numero')->nullable()->after('inspecteur_douane');
            $table->string('bordereau_sortie')->nullable()->after('scelle_numero');
            $table->boolean('is_partial')->default(false)->after('nombre_colis_sortis');
        });
    }

    public function down(): void
    {
        Schema::table('entrees', function (Blueprint $table) {
            $table->dropColumn([
                'nature_marchandise', 'nb_colis_avaries', 'description_avaries', 
                'control_officer', 'control_date', 'bordereau_entree'
            ]);
        });

        Schema::table('sorties', function (Blueprint $table) {
            $table->dropColumn(['inspecteur_douane', 'scelle_numero', 'bordereau_sortie', 'is_partial']);
        });
    }
};

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransitSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cli1 = \App\Models\Client::create([
            'name' => 'SOTOPLA (Société Togolaise de Plastique)',
            'nif' => '1000456789',
            'phone' => '+228 90 00 11 22',
        ]);

        $cli2 = \App\Models\Client::create([
            'name' => 'Import-Export Excellence SARL',
            'nif' => '2000789456',
            'phone' => '+228 92 33 44 55',
        ]);

        $dos1 = \App\Models\Dossier::create([
            'reference' => 'TR-2026-A001',
            'client_id' => $cli1->id,
            'type' => 'Import',
            'mode' => 'Maritime',
            'statut' => 'En cours',
            'description' => 'Importation de matières premières plastiques via LCT.',
        ]);

        \App\Models\FinancialTransaction::create([
            'dossier_id' => $dos1->id,
            'type' => 'Provision',
            'label' => 'Avance sur frais de dédouanement',
            'amount' => 1500000,
            'date' => now(),
            'payment_method' => 'Chèque',
        ]);

        \App\Models\FinancialTransaction::create([
            'dossier_id' => $dos1->id,
            'type' => 'Débours',
            'category' => 'LCT',
            'label' => 'Frais de manutention LCT',
            'amount' => 450000,
            'date' => now(),
        ]);
        
        \App\Models\FinancialTransaction::create([
            'dossier_id' => $dos1->id,
            'type' => 'Débours',
            'category' => 'OTR',
            'label' => 'Droits de douane (OTR)',
            'amount' => 850000,
            'date' => now(),
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\Declaration;
use App\Models\Article;
use Illuminate\Support\Facades\DB;

class SydoniaXmlService
{
    public function import(string $xmlContent, ?Dossier $existingDossier = null)
    {
        $xml = simplexml_load_string($xmlContent);
        if (!$xml) throw new \Exception("Format XML invalide.");
        
        return DB::transaction(function () use ($xml, $existingDossier) {
            // Helper function to handle <null/> tags and empty nodes
            $getVal = function ($node) {
                if (!$node) return '';
                if (isset($node->null)) return '';
                $val = trim((string) $node);
                return $val;
            };

            // 1. Identification du Client (Consignee)
            $nif = $getVal($xml->Traders->Consignee->Consignee_code);
            $clientName = $getVal($xml->Traders->Consignee->Consignee_name) ?: 'Client Inconnu';
            
            $client = Client::firstOrCreate(
                ['nif' => $nif ?: 'GENERIC-' . time()],
                ['name' => $clientName]
            );

            // 2. Dossier / Déclaration
            $regNum = $getVal($xml->Identification->Registration->Number) ?: 'SCAN-' . date('His');
            $regime = $getVal($xml->Identification->Type->Type_of_declaration);
            $officeCode = $getVal($xml->Identification->Office_segment->Customs_clearance_office_code) ?: 'TG001';

            $dossier = $existingDossier ?? Dossier::firstOrCreate(
                ['reference' => 'SD-' . $regNum . '-' . date('Y')],
                [
                    'client_id' => $client->id,
                    'type' => str_contains(strtoupper($regime), 'EX') ? 'Export' : 'Import',
                    'mode' => 'Maritime',
                    'statut' => 'En cours',
                    'description' => 'Import Sydonia SAD n° ' . $regNum . ' (' . $regime . ')',
                ]
            );

            // 3. Déclaration
            $declaration = Declaration::updateOrCreate(
                ['numero_sydonia' => $regNum],
                [
                    'dossier_id' => $dossier->id,
                    'date_sydonia' => $getVal($xml->Identification->Registration->Date) ?: now()->toDateString(),
                    'bureau' => $officeCode,
                    'regime' => $regime,
                    'circuit' => 'Jaune',
                    'valeur_douane' => (float) ($xml->Valuation->Total_CIF ?? 0),
                    'droits_douane' => (float) ($xml->Financial->Amounts->Totals_taxes ?? 0),
                    'poids_total' => (float) ($xml->Valuation->Weight->Gross_weight ?? 0),
                    'colis_total' => (int) ($xml->Property->Nbers->Total_number_of_packages ?? 0),
                    'manifest_num' => $getVal($xml->Identification->Manifest_reference_number),
                    'statut' => 'Liquidé',
                ]
            );

            // 4. Import des Articles
            $declaration->articles()->delete();
            
            // Dans SYDONIA/ASYCUDA, les tags <Item> peuvent être des enfants directs de <ASYCUDA>
            foreach ($xml->Item as $item) {
                $declaration->articles()->create([
                    'description' => $getVal($item->Goods_description->Description_of_goods) ?: 'Article sans description',
                    'hscode' => $getVal($item->Tarification->HScode->Commodity_code),
                    'quantity' => (float) ($item->Packages->Number_of_packages ?? 1),
                    'unit' => $getVal($item->Packages->Kind_of_packages_code) ?: 'COLIS',
                    'weight' => (float) ($item->Valuation_item->Weight_itm->Net_weight_itm ?? 0),
                    'value' => (float) ($item->Valuation_item->Item_Invoice->Amount_national_currency ?? 0),
                ]);
            }

            return $declaration;
        });
    }

    public static function numberToWords($number)
    {
        $formatter = new \NumberFormatter('fr', \NumberFormatter::SPELLOUT);
        return ucfirst($formatter->format($number));
    }
}

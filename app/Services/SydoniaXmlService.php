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
            // 1. Identification du Client par NIF
            $nif = (string) ($xml->Traders->Consignee->Consignee_code ?? '');
            $clientName = (string) ($xml->Traders->Consignee->Consignee_name ?? 'Client Inconnu');
            
            $client = Client::firstOrCreate(
                ['nif' => $nif],
                ['name' => $clientName]
            );

            // 2. Dossier
            $regNum = (string) ($xml->Identification->Registration->Number ?? rand(1000,9999));
            $regimeCode = (string) ($xml->Type_of_declaration->Declaration_type_code ?? '');
            $regimeSub = (string) ($xml->Type_of_declaration->Declaration_type_subcode ?? '');
            $regime = $regimeCode . $regimeSub; // Ex: IM4

            $dossier = $existingDossier ?? Dossier::firstOrCreate(
                ['reference' => 'SD-' . $regNum . '-' . date('Y')],
                [
                    'client_id' => $client->id,
                    'type' => str_contains($regime, 'EX') ? 'Export' : 'Import',
                    'mode' => 'Maritime',
                    'statut' => 'En cours',
                    'description' => 'Import Sydonia SAD n° ' . $regNum,
                ]
            );

            // 3. Déclaration (Gestion de l'Upsert pour éviter les erreurs de doublons)
            $declaration = Declaration::updateOrCreate(
                ['numero_sydonia' => $regNum],
                [
                    'dossier_id' => $dossier->id,
                    'date_sydonia' => (string) ($xml->Identification->Registration->Date ?? now()->toDateString()),
                    'bureau' => (string) ($xml->Identification->Office_code ?? 'TG001'),
                    'regime' => $regime,
                    'circuit' => 'Jaune',
                    'valeur_douane' => (float) ($xml->General_information->Value_details->General_information_value_details_customs_value ?? 0),
                    'droits_douane' => (float) ($xml->Financial->Amounts->Total_tours ?? 0),
                    'poids_total' => (float) ($xml->Financial->Totals->Total_gross_weight ?? 0),
                    'colis_total' => (int) ($xml->Financial->Totals->Total_number_of_packages ?? 0),
                    'manifest_num' => (string) ($xml->Transport->Means_of_transport->Registration_number_at_arrival ?? ''),
                    'statut' => 'Liquidé',
                ]
            );

            // 4. Import des Articles (Nettoyage préalable pour éviter les doublons sur ré-import)
            $declaration->articles()->delete();
            
            if ($xml->Items->Item) {
                foreach ($xml->Items->Item as $item) {
                    $declaration->articles()->create([
                        'description' => (string) $item->Tariff->Goods_description,
                        'hscode' => (string) $item->Tariff->Commodity_code,
                        'quantity' => (float) ($item->Packages->Number_of_packages ?? 1),
                        'unit' => (string) ($item->Packages->Kind_of_packages_code ?? 'COLIS'),
                        'weight' => (float) ($item->Valuation_item->Weight_net ?? 0),
                        'value' => (float) ($item->Valuation_item->Item_price ?? 0),
                    ]);
                }
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

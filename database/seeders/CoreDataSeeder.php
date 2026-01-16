<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Currency;

class CoreDataSeeder extends Seeder
{
    public function run(): void
    {
        // Pays (Focus Afrique de l'Ouest)
        $countries = [
            ['code' => 'TG', 'name' => 'Togo', 'phone_code' => '+228', 'emoji' => '🇹🇬'],
            ['code' => 'BJ', 'name' => 'Bénin', 'phone_code' => '+229', 'emoji' => '🇧🇯'],
            ['code' => 'GH', 'name' => 'Ghana', 'phone_code' => '+233', 'emoji' => '🇬🇭'],
            ['code' => 'CI', 'name' => 'Côte d\'Ivoire', 'phone_code' => '+225', 'emoji' => '🇨🇮'],
            ['code' => 'NE', 'name' => 'Niger', 'phone_code' => '+227', 'emoji' => '🇳🇪'],
            ['code' => 'BF', 'name' => 'Burkina Faso', 'phone_code' => '+226', 'emoji' => '🇧🇫'],
            ['code' => 'ML', 'name' => 'Mali', 'phone_code' => '+223', 'emoji' => '🇲🇱'],
            ['code' => 'SN', 'name' => 'Sénégal', 'phone_code' => '+221', 'emoji' => '🇸🇳'],
            ['code' => 'CN', 'name' => 'Chine', 'phone_code' => '+86', 'emoji' => '🇨🇳'],
            ['code' => 'FR', 'name' => 'France', 'phone_code' => '+33', 'emoji' => '🇫🇷'],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(['code' => $country['code']], $country);
        }

        // Devises
        $currencies = [
            ['code' => 'XOF', 'name' => 'Franc CFA (BCEAO)', 'symbol' => 'FCFA', 'is_default' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'is_default' => false],
            ['code' => 'USD', 'name' => 'Dollar US', 'symbol' => '$', 'is_default' => false],
            ['code' => 'CNY', 'name' => 'Yuan Chinois', 'symbol' => '¥', 'is_default' => false],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(['code' => $currency['code']], $currency);
        }
    }
}

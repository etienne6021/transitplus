<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création de l'agence de démonstration
        $agency = \App\Models\Agency::firstOrCreate(
            ['name' => 'B-TRANS LOGISTICS'],
            [
                'email' => 'contact@btrans.tg',
                'contact_phone' => '+228 90 00 00 00',
                'modules' => ['transit', 'commerce', 'hr', 'finance', 'mad'],
                'nif' => '1000123456',
                'rccm' => 'TG-LOM-2024-B-001',
            ]
        );

        // Création du Super Admin
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'admin@transit.plus'],
            [
                'name' => 'Directeur Général',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'agency_id' => $agency->id,
            ]
        );

        // S'assurer que les rôles existent avant d'assigner
        $this->call(RolesAndPermissionsSeeder::class);

        $user->assignRole('Super Admin');
    }
}

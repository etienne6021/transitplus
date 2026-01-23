<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Initialisation des permissions
        $permissions = [
            // Admin & Système
            'gestion_parametres',
            'gestion_utilisateurs',
            'gestion_agencies',
            'gestion_agenda_autres',
            
            // CRM & Business Development
            'gestion_clients',
            'gestion_prospects',
            'gestion_devis',

            // Transit & Douane
            'voir_transit',
            'creer_transit',
            'modifier_transit',
            'supprimer_transit',
            
            // Logistique MAD (Magasin et Aire de Dédouanement)
            'gestion_mad',
            
            // Commerce, Stock & Ventes
            'gestion_produits',
            'gestion_stocks',
            'gestion_ventes',
            
            // Finance & Comptabilité
            'gestion_facturation',
            'gestion_reglements',
            'gestion_tresorerie',
            
            // RH & Paie
            'gestion_personnel',
            'gestion_paie',
            'gestion_conges',

            // Secrétariat
            'gestion_courrier',
            'gestion_visiteurs',
            'gestion_notes_service',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Création des Rôles
        $superAdmin = Role::findOrCreate('Super Admin');
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::findOrCreate('Admin');
        // L'admin d'une agence a accès à tout sauf la gestion globale des agences
        $admin->givePermissionTo(Permission::all());
        $admin->revokePermissionTo('gestion_agencies');

        $secretary = Role::findOrCreate('Secrétaire');
        $secretary->givePermissionTo([
            'gestion_courrier', 'gestion_visiteurs', 'gestion_notes_service', 
            'gestion_agenda_autres', 'voir_transit'
        ]);

        $transitAgent = Role::findOrCreate('Agent Transit');
        $transitAgent->givePermissionTo([
            'voir_transit', 'creer_transit', 'modifier_transit', 'gestion_mad'
        ]);

        $rh = Role::findOrCreate('Responsable RH');
        $rh->givePermissionTo([
            'gestion_personnel', 'gestion_paie', 'gestion_conges'
        ]);

        $accountant = Role::findOrCreate('Comptable');
        $accountant->givePermissionTo([
            'gestion_facturation', 'gestion_tresorerie', 'gestion_ventes'
        ]);

        // Assigner le rôle Super Admin au compte principal pour le test
        $user = \App\Models\User::where('email', 'admin@transit.plus')->first();
        if ($user) {
            $user->assignRole('Super Admin');
        }
    }
}

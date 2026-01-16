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
            
            // Transit & Douane
            'voir_transit',
            'creer_transit',
            'modifier_transit',
            'supprimer_transit',
            
            // Logistique MAD
            'gestion_mad',
            
            // Commerce & Ventes
            'gestion_ventes',
            'gestion_stocks',
            
            // Finance
            'gestion_facturation',
            'gestion_tresorerie',
            
            // RH
            'gestion_personnel',
            'gestion_paie',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Création des Rôles
        $superAdmin = Role::findOrCreate('Super Admin');
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::findOrCreate('Admin');
        // L'admin d'une agence n'a pas forcément toutes les permissions système (comme créer d'autres agences)
        $admin->givePermissionTo([
            'gestion_parametres', 'gestion_utilisateurs', 'voir_transit', 'creer_transit', 'modifier_transit',
            'gestion_mad', 'gestion_ventes', 'gestion_stocks', 'gestion_facturation', 'gestion_tresorerie',
            'gestion_personnel', 'gestion_paie'
        ]);

        $transitAgent = Role::findOrCreate('Agent Transit');
        $transitAgent->givePermissionTo([
            'voir_transit', 'creer_transit', 'modifier_transit', 'gestion_mad'
        ]);

        $rh = Role::findOrCreate('Responsable RH');
        $rh->givePermissionTo([
            'gestion_personnel', 'gestion_paie'
        ]);

        $accountant = Role::findOrCreate('Comptable');
        $accountant->givePermissionTo([
            'gestion_facturation', 'gestion_tresorerie', 'gestion_ventes'
        ]);

        // Assigner le rôle Admin au premier utilisateur s'il existe
        $user = \App\Models\User::first();
        if ($user) {
            $user->assignRole('Admin');
        }
    }
}

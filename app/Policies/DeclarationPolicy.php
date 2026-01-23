<?php

namespace App\Policies;

use App\Models\Declaration;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DeclarationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->can('voir_transit');
    }

    public function view(User $user, Declaration $declaration): bool
    {
        return $user->hasRole('Super Admin') || $user->can('voir_transit');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->can('creer_transit');
    }

    public function update(User $user, Declaration $declaration): bool
    {
        return $user->hasRole('Super Admin') || $user->can('modifier_transit');
    }

    public function delete(User $user, Declaration $declaration): bool
    {
        return $user->hasRole('Super Admin') || $user->can('supprimer_transit');
    }
}

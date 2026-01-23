<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_clients') || $user->can('voir_transit');
    }

    public function view(User $user, Client $client): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_clients') || $user->can('voir_transit');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_clients');
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_clients');
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_clients');
    }
}

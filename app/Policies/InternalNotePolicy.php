<?php

namespace App\Policies;

use App\Models\InternalNote;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InternalNotePolicy
{
    public function viewAny(User $user): bool
    {
        // Tout le monde peut voir les notes internes ou seulement le secrétariat ?
        // Généralement tout le monde peut les voir si elles sont actives.
        return true; 
    }

    public function view(User $user, InternalNote $internalNote): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_notes_service');
    }

    public function update(User $user, InternalNote $internalNote): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_notes_service');
    }

    public function delete(User $user, InternalNote $internalNote): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_notes_service');
    }
}

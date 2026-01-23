<?php

namespace App\Policies;

use App\Models\AgendaItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AgendaItemPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Le filtrage se fait dans le query builder du resource
    }

    public function view(User $user, AgendaItem $agendaItem): bool
    {
        return $user->hasRole('Super Admin') || 
               $user->id === $agendaItem->user_id || 
               $agendaItem->is_public ||
               $user->can('gestion_agenda_autres');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AgendaItem $agendaItem): bool
    {
        return $user->hasRole('Super Admin') || 
               $user->id === $agendaItem->user_id || 
               $user->can('gestion_agenda_autres');
    }

    public function delete(User $user, AgendaItem $agendaItem): bool
    {
        return $user->hasRole('Super Admin') || 
               $user->id === $agendaItem->user_id || 
               $user->can('gestion_agenda_autres');
    }
}

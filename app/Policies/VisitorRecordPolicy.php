<?php

namespace App\Policies;

use App\Models\VisitorRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class VisitorRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_visiteurs');
    }

    public function view(User $user, VisitorRecord $visitorRecord): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_visiteurs');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_visiteurs');
    }

    public function update(User $user, VisitorRecord $visitorRecord): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_visiteurs');
    }

    public function delete(User $user, VisitorRecord $visitorRecord): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_visiteurs');
    }
}

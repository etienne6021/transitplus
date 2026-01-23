<?php

namespace App\Policies;

use App\Models\MailRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MailRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_courrier');
    }

    public function view(User $user, MailRecord $mailRecord): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_courrier');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_courrier');
    }

    public function update(User $user, MailRecord $mailRecord): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_courrier');
    }

    public function delete(User $user, MailRecord $mailRecord): bool
    {
        return $user->hasRole('Super Admin') || $user->can('gestion_courrier');
    }
}

<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends AdminOnlyPolicy
{
    /**
     * Admin cannot delete themselves via policy.
     */
    public function delete(User $user, mixed $record): bool
    {
        return $user->isAdmin() && $user->id !== $record->id;
    }
}

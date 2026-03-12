<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function toggleBlock(User $actor, User $target): bool
    {
        return $actor->role === 'admin' && $target->role !== 'admin';
    }
}

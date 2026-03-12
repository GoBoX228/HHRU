<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;

class ChatPolicy
{
    public function view(User $user, Chat $chat): bool
    {
        if (! in_array($user->role, ['freelancer', 'employer'], true)) {
            return false;
        }

        return (int) $chat->employer_user_id === (int) $user->id
            || (int) $chat->freelancer_user_id === (int) $user->id;
    }

    public function sendMessage(User $user, Chat $chat): bool
    {
        return $this->view($user, $chat);
    }
}

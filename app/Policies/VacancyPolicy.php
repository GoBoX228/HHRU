<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vacancy;

class VacancyPolicy
{
    public function apply(User $user, Vacancy $vacancy): bool
    {
        return $user->role === 'freelancer' && $vacancy->status === 'open';
    }

    public function updateStatus(User $user, Vacancy $vacancy): bool
    {
        return $user->role === 'employer' && (int) $vacancy->employer_user_id === (int) $user->id;
    }

    public function manageApplications(User $user, Vacancy $vacancy): bool
    {
        return $this->updateStatus($user, $vacancy);
    }

    public function archive(User $user, Vacancy $vacancy): bool
    {
        return $user->role === 'admin';
    }
}

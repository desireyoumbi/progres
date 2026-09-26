<?php

namespace App\Policies;

use App\Models\RotatingContribution;
use App\Models\User;

class RotatingContributionPolicy
{
    public function manage(User $user): bool
    {
        return $user->role === 'treasurer';
    }
}
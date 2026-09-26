<?php

namespace App\Policies;

use App\Models\IndividualContribution;
use App\Models\User;

class IndividualContributionPolicy
{
    public function manage(User $user): bool
    {
        return $user->role === 'treasurer';
    }
}
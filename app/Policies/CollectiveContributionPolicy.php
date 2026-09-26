<?php

namespace App\Policies;

use App\Models\CollectiveContribution;
use App\Models\User;

class CollectiveContributionPolicy
{
    /**
     * Seul le trésorier peut approuver, rejeter, modifier ou supprimer
     * une cotisation de la caisse collective.
     */
    public function manage(User $user): bool
    {
        return $user->role === 'treasurer';
    }
}
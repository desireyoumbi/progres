<?php

namespace App\Policies;

use App\Models\Tontine;
use App\Models\User;

class TontinePolicy
{
    /**
     * Seuls le président et le trésorier peuvent créer une tontine.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['president', 'treasurer']);
    }

    /**
     * Même règle pour la modification et la clôture.
     */
    public function update(User $user, Tontine $tontine): bool
    {
        return in_array($user->role, ['president', 'treasurer']);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'must_change_password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'must_change_password'=> 'boolean',
    ];

    // Les liens d'adhésion vers le modèle pivot TontineMember
    public function tontineMemberships(): HasMany
    {
        return $this->hasMany(TontineMember::class);
    }

    // Relation directe avec les tontines via la table pivot tontine_members
    public function tontines(): BelongsToMany
    {
        return $this->belongsToMany(Tontine::class, 'tontine_members')
                    ->using(TontineMember::class)
                    ->withPivot(['joined_at', 'is_active'])
                    ->withTimestamps();
    }

    // Ses propres dépôts hebdomadaires (tontine rotative)
    public function rotatingContributions(): HasMany
    {
        return $this->hasMany(RotatingContribution::class);
    }

    // Ses propres dépôts mensuels (caisse collective)
    public function collectiveContributions(): HasMany
    {
        return $this->hasMany(CollectiveContribution::class);
    }

    // Ses propres dépôts mensuels (caisse individuelle)
    public function individualContributions(): HasMany
    {
        return $this->hasMany(IndividualContribution::class);
    }

    // Les mois où il a été désigné bénéficiaire de la tontine rotative
    public function beneficiariesReceived(): HasMany
    {
        return $this->hasMany(Beneficiary::class, 'user_id');
    }

    // Les bénéficiaires qu'il a désignés (s'il est président/trésorier)
    public function beneficiariesAssigned(): HasMany
    {
        return $this->hasMany(Beneficiary::class, 'assigned_by');
    }

    // Les dépôts (rotatifs) qu'il a validés en tant que président/trésorier
    public function rotatingContributionsApproved(): HasMany
    {
        return $this->hasMany(RotatingContribution::class, 'approved_by');
    }

    // Vérifie si l'utilisateur est le président
    public function isPresident(): bool
    {
        return $this->role === 'president';
    }

    // Vérifie si l'utilisateur est le trésorier
    public function isTreasurer(): bool
    {
        return $this->role === 'treasurer';
    }

   // Vérifie si l'utilisateur est le secrétaire
    public function isSecretary(): bool
    {
        return $this->role === 'secretary';
    }

    // Raccourci pour vérifier si l'utilisateur fait partie du bureau (Admin / Bureau exécutif)
    public function isAdmin(): bool
    {
        return in_array($this->role, ['president', 'treasurer', 'secretary']);
    }
}
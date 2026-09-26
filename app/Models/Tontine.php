<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tontine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'rotating_amount',
        'collective_amount',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rotating_amount' => 'integer',
        'collective_amount' => 'integer',
    ];

    // Les membres inscrits au cercle de tontine rotative pour ce cycle
    public function members(): HasMany
    {
        return $this->hasMany(TontineMember::class);
    }

    // Tous les dépôts hebdomadaires de la tontine rotative pour ce cycle
    public function rotatingContributions(): HasMany
    {
        return $this->hasMany(RotatingContribution::class);
    }

    // Tous les dépôts mensuels de la caisse collective pour ce cycle
    public function collectiveContributions(): HasMany
    {
        return $this->hasMany(CollectiveContribution::class);
    }

    // Tous les dépôts mensuels de la caisse individuelle pour ce cycle
    public function individualContributions(): HasMany
    {
        return $this->hasMany(IndividualContribution::class);
    }

    // Les bénéficiaires désignés chaque mois pour ce cycle
    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class);
    }
}
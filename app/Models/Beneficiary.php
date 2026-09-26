<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'tontine_id',
        'month',
        'user_id',
        'assigned_by',
        'status'
    ];

    protected $casts = [
        'month' => 'date',
    ];

    // La tontine (cycle) à laquelle ce bénéficiaire est rattaché
    public function tontine(): BelongsTo
    {
        return $this->belongsTo(Tontine::class);
    }

    // Le membre qui encaisse la cagnotte ce mois-là
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Le membre du bureau (président/trésorier) qui a désigné ce bénéficiaire
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
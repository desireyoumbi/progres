<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RotatingContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'tontine_id',
        'user_id',
        'week',
        'amount',
        'status',
        'proof_path',
        'approved_by',
    ];

    protected $casts = [
        'week' => 'date',
        'amount' => 'integer',
    ];

    // La tontine (cycle) à laquelle cette cotisation est rattachée
    public function tontine(): BelongsTo
    {
        return $this->belongsTo(Tontine::class);
    }

    // Le membre qui a fait ce dépôt
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Le membre du bureau (président/trésorier) qui a validé ou rejeté ce dépôt
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
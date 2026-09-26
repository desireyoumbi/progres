<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TontineMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'tontine_id',
        'user_id',
        'joined_at',
        'is_active',
    ];

    protected $casts = [
        'joined_at' => 'date',
        'is_active' => 'boolean',
    ];

    // La tontine (cycle) concernée
    public function tontine(): BelongsTo
    {
        return $this->belongsTo(Tontine::class);
    }

    // Le membre inscrit au cercle rotatif
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
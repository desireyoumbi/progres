<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = ['title', 'meeting_date', 'content', 'user_id'];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Construit le lien "click-to-chat" WhatsApp pour partager ce rapport.
     * Envoie le contenu intégral rédigé par l'auteur, avec une mise en forme
     * WhatsApp (gras/italique) et un lien vers la page complète du rapport
     * (utile maintenant que le site est accessible en ligne).
     */
    public function getWhatsappShareUrlAttribute(): string
    {
        $text = "*{$this->title}*\n"
            . "_Réunion du {$this->meeting_date->format('d/m/Y')} — rédigé par {$this->author->name}_\n"
            . "————————————————\n\n"
            . $this->content
            . "\n\n————————————————\n"
            . "Voir le rapport complet : " . route('admin.reports.show', $this);

        return 'https://web.whatsapp.com/send?text=' . urlencode($text);
    }
}
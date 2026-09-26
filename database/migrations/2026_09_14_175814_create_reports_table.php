<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Titre du rapport (ex: Réunion du 15 Septembre 2026)
            $table->date('meeting_date'); // Date de la réunion
            $table->text('content'); // Contenu / Compte-rendu détaillé
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Secrétaire ou Admin rédacteur
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
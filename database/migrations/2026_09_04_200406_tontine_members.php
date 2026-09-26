<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tontine_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('joined_at');
            $table->boolean('is_active')->default(true); // permet de désactiver sans supprimer
            $table->timestamps();

            $table->unique(['tontine_id', 'user_id']); // un membre ne s'inscrit qu'une fois par tontine
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tontine_members');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('individual_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('month'); // premier jour du mois concerné
            $table->unsignedInteger('amount'); // montant libre, choisi par le membre
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('proof_path')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // pas de contrainte unique sur (tontine_id, user_id, month) :
            // un membre pourrait vouloir déposer plusieurs fois dans le même mois
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('individual_contributions');
    }
};
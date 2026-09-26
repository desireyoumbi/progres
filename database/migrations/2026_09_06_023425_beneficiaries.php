<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->cascadeOnDelete();
            $table->date('month'); // premier jour du mois concerné
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // membre qui encaisse
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete(); // president/treasurer
            $table->timestamps();

            $table->unique(['tontine_id', 'month']); // un seul bénéficiaire par mois et par tontine
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
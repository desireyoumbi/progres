<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tontines', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ex. "Tontine 2026"
            $table->date('start_date');
            $table->date('end_date'); // date de déblocage des caisses collective et individuelle
            $table->unsignedInteger('rotating_amount'); // montant fixe hebdo de la tontine rotative
            $table->unsignedInteger('collective_amount'); // montant fixe mensuel de la caisse collective
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tontines');
    }
};